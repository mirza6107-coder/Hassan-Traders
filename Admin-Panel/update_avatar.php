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

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
  $err = $_FILES['avatar']['error'] ?? 'unknown';
  echo json_encode(['success' => false, 'message' => 'Upload error (code ' . $err . '). Please try again.']);
  exit();
}

$file      = $_FILES['avatar'];
$max_size  = 2 * 1024 * 1024; // 2 MB
$allowed   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

// ── Size check ──
if ($file['size'] > $max_size) {
  echo json_encode(['success' => false, 'message' => 'Image must be under 2 MB.']);
  exit();
}

// ── MIME type check (use finfo for security) ──
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
if (!in_array($mime, $allowed)) {
  echo json_encode(['success' => false, 'message' => 'Only JPEG, PNG, WebP, and GIF images are allowed.']);
  exit();
}

// ── Destination directory ──
$upload_dir = __DIR__ . '/uploads/avatars/';
if (!is_dir($upload_dir)) {
  mkdir($upload_dir, 0755, true);
}

// ── Generate unique filename ──
$ext      = match($mime) {
  'image/jpeg' => 'jpg',
  'image/png'  => 'png',
  'image/webp' => 'webp',
  'image/gif'  => 'gif',
  default      => 'jpg',
};
$user_id  = $_SESSION['user_id'] ?? preg_replace('/[^a-z0-9]/i', '', $_SESSION['user_name']);
$filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
$dest     = $upload_dir . $filename;

// ── Delete old avatar if exists ──
if (!empty($_SESSION['avatar'])) {
  $old_file = __DIR__ . '/' . ltrim($_SESSION['avatar'], '/');
  if (file_exists($old_file)) {
    @unlink($old_file);
  }
}

// ── Move uploaded file ──
if (!move_uploaded_file($file['tmp_name'], $dest)) {
  echo json_encode(['success' => false, 'message' => 'Failed to save image. Check server write permissions.']);
  exit();
}

// ── Public URL (relative to web root) ──
$avatar_url = 'uploads/avatars/' . $filename;

// ── Update session ──
$_SESSION['avatar'] = $avatar_url;

// ── Persist in DB (optional but recommended) ──
try {
  $uid = $_SESSION['user_id'] ?? null;
  if ($uid) {
    $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->bind_param("si", $avatar_url, $uid);
    $stmt->execute();
    $stmt->close();
  } else {
    $uname = $_SESSION['user_name'];
    $stmt  = $conn->prepare("UPDATE users SET avatar = ? WHERE username = ?");
    $stmt->bind_param("ss", $avatar_url, $uname);
    $stmt->execute();
    $stmt->close();
  }
} catch (Exception $e) {
  // Non-fatal: file is saved, DB update failed
  // Log the error server-side rather than showing to user
  error_log('Avatar DB update failed: ' . $e->getMessage());
}

echo json_encode([
  'success'    => true,
  'message'    => 'Profile photo updated!',
  'avatar_url' => $avatar_url,
]);
