<?php
ob_start();
ini_set('display_errors', 0);

header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    ob_end_clean();
    echo 'Method not allowed';
    exit;
}

$conn = mysqli_connect('localhost', 'root', '', 'htss');

if (!$conn) {
    http_response_code(500);
    ob_end_clean();
    echo 'Connection failed: ' . mysqli_connect_error();
    exit;
}

// Sanitize inputs
$full_name = mysqli_real_escape_string($conn, trim($_POST['fname']    ?? ''));
$company   = mysqli_real_escape_string($conn, trim($_POST['company']  ?? ''));
$phone     = mysqli_real_escape_string($conn, trim($_POST['phone']    ?? ''));
$email     = mysqli_real_escape_string($conn, trim($_POST['email']    ?? ''));
$subject   = mysqli_real_escape_string($conn, trim($_POST['subject']  ?? ''));
$message   = mysqli_real_escape_string($conn, trim($_POST['message']  ?? ''));

// Validate required fields
if (empty($full_name) || empty($email) || empty($message)) {
    http_response_code(400);
    ob_end_clean();
    echo 'Please fill in all required fields.';
    exit;
}


mysqli_query($conn, "CREATE TABLE IF NOT EXISTS contact_messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(100) NOT NULL,
    company     VARCHAR(100),
    phone       VARCHAR(20),
    email       VARCHAR(100) NOT NULL,
    subject     VARCHAR(200),
    message     TEXT NOT NULL,
    read_status TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$sql = "INSERT INTO contact_messages (full_name, company, phone, email, subject, message)
        VALUES ('$full_name', '$company', '$phone', '$email', '$subject', '$message')";

if (mysqli_query($conn, $sql)) {
    http_response_code(200);
    ob_end_clean();
    echo 'Success';
} else {
    http_response_code(500);
    ob_end_clean();
    echo 'Database Error: ' . mysqli_error($conn);
}

mysqli_close($conn);
?>
