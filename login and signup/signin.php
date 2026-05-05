<?php
session_start();

// ====================== DATABASE CONNECTION ======================
$host     = "localhost";
$dbname   = "htss";        // ← Make sure this is correct
$user     = "root"; 
$pass     = ""; 

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// ====================== LOGIN ATTEMPTS PROTECTION ======================
$max_attempts = 5;
$lockout_time = 600; // 10 minutes in seconds

// Initialize session variables
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['last_attempt_time'])) {
    $_SESSION['last_attempt_time'] = 0;
}

// Check if user is locked out
if ($_SESSION['login_attempts'] >= $max_attempts) {
    $time_elapsed = time() - $_SESSION['last_attempt_time'];
    
    if ($time_elapsed < $lockout_time) {
        $minutes_left = ceil(($lockout_time - $time_elapsed) / 60);
        $_SESSION['auth_error'] = "Too many failed login attempts. Please try again after $minutes_left minute(s).";
        header("Location: login.php");
        exit;
    } else {
        // Reset attempts after lockout period
        $_SESSION['login_attempts'] = 0;
    }
}

// ====================== PROCESS LOGIN ======================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$email    = trim($_POST['emailaddress'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    $_SESSION['auth_error'] = "Please fill in all fields.";
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();
    header("Location: login.php");
    exit;
}

// Prepare and execute query
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();
    
    $remaining = $max_attempts - $_SESSION['login_attempts'];
    
    if ($remaining > 0) {
        $_SESSION['auth_error'] = "Invalid email or password. $remaining attempt(s) remaining.";
    } else {
        $_SESSION['auth_error'] = "Too many failed attempts. Account locked for 10 minutes.";
    }
    
    header("Location: login.php");
    exit;
}

// ====================== SUCCESSFUL LOGIN ======================
// Reset login attempts on success
$_SESSION['login_attempts'] = 0;
$_SESSION['last_attempt_time'] = 0;

// Set session variables
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['fullname'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['role']       = $user['role'];

// Load user's saved cart from database
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

// Redirect based on role
if ($user['role'] === 'admin') {
    $_SESSION['admin_id']    = $user['id'];
    $_SESSION['admin_name']  = $user['fullname'];
    $_SESSION['admin_email'] = $user['email'];
    $_SESSION['admin_role']  = 'admin';
    
    header("Location: ../Admin-Panel/dashboard.php");
} else {
    header("Location: ../../../../Hassan Traders Sanitary Store/index.php");
}

exit;
?>