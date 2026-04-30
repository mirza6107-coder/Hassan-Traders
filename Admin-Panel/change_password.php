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

$current_password = $_POST['current_password'] ?? '';
$new_password     = $_POST['new_password']     ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// ── Validation ──
if ($current_password === '' || $new_password === '' || $confirm_password === '') {
  echo json_encode(['success' => false, 'message' => 'All fields are required.']);
  exit();
}

if (strlen($new_password) < 8) {
  echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters.']);
  exit();
}

if ($new_password !== $confirm_password) {
  echo json_encode(['success' => false, 'message' => 'New passwords do not match.']);
  exit();
}

if ($new_password === $current_password) {
  echo json_encode(['success' => false, 'message' => 'New password must be different from the current password.']);
  exit();
}

// ── Fetch current hashed password from DB ──
try {
  $user_id  = $_SESSION['user_id']  ?? null;
  $username = $_SESSION['user_name'];

  if ($user_id) {
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
  } else {
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
  }

  $stmt->execute();
  $stmt->bind_result($hashed_password);
  $stmt->fetch();
  $stmt->close();

  if (!$hashed_password) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit();
  }

  // ── Verify current password ──
  // Supports both password_hash() hashes and plain MD5 (legacy fallback).
  $verified = false;
  if (password_verify($current_password, $hashed_password)) {
    $verified = true;
  } elseif (md5($current_password) === $hashed_password) {
    // Legacy MD5 — still allow but upgrade on save
    $verified = true;
  }

  if (!$verified) {
    echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
    exit();
  }

  // ── Hash new password and update ──
  $new_hash = password_hash($new_password, PASSWORD_BCRYPT);

  if ($user_id) {
    $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $upd->bind_param("si", $new_hash, $user_id);
  } else {
    $upd = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    $upd->bind_param("ss", $new_hash, $username);
  }
  $upd->execute();
  $upd->close();

  echo json_encode(['success' => true, 'message' => 'Password changed successfully!']);

} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
