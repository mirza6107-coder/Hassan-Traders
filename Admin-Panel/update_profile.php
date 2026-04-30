<?php
session_start();
include('db_connect.php');

header('Content-Type: application/json');

// Auth guard
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
  echo json_encode(['success' => false, 'message' => 'Unauthorised.']);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
  exit();
}

$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email']    ?? '');

// ── Validation ──
if ($username === '' || $email === '') {
  echo json_encode(['success' => false, 'message' => 'Name and email are required.']);
  exit();
}

if (strlen($username) > 80) {
  echo json_encode(['success' => false, 'message' => 'Name must be 80 characters or fewer.']);
  exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
  exit();
}

// ── Database update ──
// Adjust the table/column names to match your actual schema.
// Common pattern: table = `users`, columns = `username`, `email`, `id`
try {
  $user_id = $_SESSION['user_id'] ?? null;

  if ($user_id) {
    // Prepared statement update by ID (recommended)
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $email, $user_id);
    $stmt->execute();
    $stmt->close();
  } else {
    // Fallback: update by current username stored in session
    $old_name = $_SESSION['user_name'];
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE username = ?");
    $stmt->bind_param("sss", $username, $email, $old_name);
    $stmt->execute();
    $stmt->close();
  }

  // Refresh session values
  $_SESSION['user_name'] = $username;
  $_SESSION['email']     = $email;

  echo json_encode([
    'success'  => true,
    'message'  => 'Profile updated successfully.',
    'username' => $username,
    'email'    => $email,
  ]);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
