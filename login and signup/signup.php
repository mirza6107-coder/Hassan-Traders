<?php
session_start();
// db_connect.php
$host     = "localhost";
$dbname   = "htss";   // ← CHANGE THIS
$user     = "root"; 
$pass     = ""; 

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$fullname = trim($_POST['fullname']     ?? '');
$email    = trim($_POST['emailaddress'] ?? '');
$password = $_POST['password']          ?? '';

// ── Validation ────────────────────────────────────────────────
$errors = [];
if (!$fullname)                                      $errors[] = "Full name is required.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL))      $errors[] = "A valid email is required.";
if (strlen($password) < 6)                           $errors[] = "Password must be at least 6 characters.";

if ($errors) {
    $_SESSION['auth_error'] = implode(' ', $errors);
    header("Location: login.php?tab=signup");
    exit;
}

// ── Check if email already taken ─────────────────────────────
$check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($check, "s", $email);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    $_SESSION['auth_error'] = "An account with this email already exists.";
    header("Location: login.php?tab=signup");
    exit;
}

// ── Insert — columns: fullname, email, password, role, profile_image
$hashed       = password_hash($password, PASSWORD_DEFAULT);
$role         = 'user';                  // default role matches your DB
$profileImage = 'default_avatar.png';   // matches your existing rows

$insert = mysqli_prepare($conn, "
    INSERT INTO users (fullname, email, password, role, profile_image)
    VALUES (?, ?, ?, ?, ?)
");
mysqli_stmt_bind_param($insert, "sssss", $fullname, $email, $hashed, $role, $profileImage);
mysqli_stmt_execute($insert);
$newUserId = mysqli_insert_id($conn);

// ── Auto-login after signup ───────────────────────────────────
$_SESSION['user_id']       = $newUserId;
$_SESSION['user_name']     = $fullname;
$_SESSION['user_email']    = $email;
$_SESSION['role']          = 'user';
$_SESSION['cart_on_login'] = '[]';

header("Location: ../Home/home.php");
exit;