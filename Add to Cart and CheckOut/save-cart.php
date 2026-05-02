<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');

// ── db_connect.php is one level up from 'Add to Cart and CheckOut/' ──
// Folder structure: htss/db_connect.php
//                   htss/Add to Cart and CheckOut/save-cart.php
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
// ── Check DB connection ───────────────────────────────────────
if (!$conn || mysqli_connect_errno()) {
    echo json_encode(['success' => false, 'reason' => 'db_connection_failed', 'error' => mysqli_connect_error()]);
    exit;
}

// ── Must be logged in ─────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'reason' => 'not_logged_in']);
    exit;
}

// ── Parse JSON body ───────────────────────────────────────────
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(['success' => false, 'reason' => 'invalid_json', 'raw' => $raw]);
    exit;
}

$userId    = (int)$_SESSION['user_id'];
$productId = (int)($data['id']       ?? 0);
$name      = trim($data['name']      ?? '');
$price     = (float)($data['price']  ?? 0);
$image     = trim($data['image']     ?? '');
$quantity  = (int)($data['quantity'] ?? 1);

if (!$productId) {
    echo json_encode(['success' => false, 'reason' => 'invalid_product_id', 'received' => $data]);
    exit;
}

// ── Auto-create table if it doesn't exist yet ─────────────────
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS user_cart (
        id          INT            AUTO_INCREMENT PRIMARY KEY,
        user_id     INT            NOT NULL,
        product_id  INT            NOT NULL,
        name        VARCHAR(255)   NOT NULL,
        price       DECIMAL(10,2)  NOT NULL,
        image       VARCHAR(255),
        quantity    INT            NOT NULL DEFAULT 1,
        updated_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY  unique_user_product (user_id, product_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )
");

// ── Insert or increment quantity ──────────────────────────────
$stmt = mysqli_prepare($conn, "
    INSERT INTO user_cart (user_id, product_id, name, price, image, quantity)
    VALUES (?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
");

if (!$stmt) {
    echo json_encode(['success' => false, 'reason' => 'prepare_failed', 'error' => mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, "iisdsi", $userId, $productId, $name, $price, $image, $quantity);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => false, 'reason' => 'execute_failed', 'error' => mysqli_stmt_error($stmt)]);
    exit;
}

echo json_encode([
    'success'    => true,
    'user_id'    => $userId,
    'product_id' => $productId,
    'quantity'   => $quantity,
]);