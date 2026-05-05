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

$email    = trim($_POST['emailaddress'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    $_SESSION['auth_error'] = "Please fill in all fields.";
    header("Location: login.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['auth_error'] = "Invalid email or password.";
    header("Location: login.php");
    exit;
}

// ── Set session ───────────────────────────────────────────────
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['fullname'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['role']       = $user['role'];

// ── Load this user's saved cart from DB ──────────────────────
$cartStmt = mysqli_prepare($conn, "SELECT * FROM user_cart WHERE user_id = ?");
mysqli_stmt_bind_param($cartStmt, "i", $user['id']);
mysqli_stmt_execute($cartStmt);
$cartResult = mysqli_stmt_get_result($cartStmt);

$cartForJS = [];
while ($row = mysqli_fetch_assoc($cartResult)) {
    $cartForJS[] = [
        'id'       => $row['product_id'],
        'name'     => $row['name'],
        'price'    => (float)$row['price'],
        'image'    => $row['image'],
        'quantity' => (int)$row['quantity'],
    ];
}
$_SESSION['cart_on_login'] = json_encode($cartForJS);

// ── Set session ───────────────────────────────────────────────
if ($user['role'] === 'admin') {
    // Separate keys for Admin
    $_SESSION['admin_id']    = $user['id'];
    $_SESSION['admin_name']  = $user['fullname'];
    $_SESSION['admin_email'] = $user['email'];
    $_SESSION['admin_role']  = 'admin';
    
    header("Location: ../Admin-Panel/dashboard.php");
} else {
    // Keys for standard User
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['fullname'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role']       = 'user';
    
    header("Location: ../Home/home.php");
}
exit;