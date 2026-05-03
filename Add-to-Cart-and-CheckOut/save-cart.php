<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

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
if (!$conn) {
    echo json_encode(['success' => false, 'reason' => 'db_connection_failed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'reason' => 'not_logged_in']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode(['success' => false, 'reason' => 'invalid_json', 'raw' => $raw]);
    exit;
}

// ── What action is being requested? ──────────────────────────
// action: 'add' | 'update' | 'remove' | 'clear' | 'sync'
$action    = trim($data['action']    ?? 'add');
$userId    = (int)$_SESSION['user_id'];
$productId = (int)($data['id']       ?? 0);
$name      = trim($data['name']      ?? '');
$price     = (float)($data['price']  ?? 0);
$image     = trim($data['image']     ?? '');
$quantity  = (int)($data['quantity'] ?? 1);



// ── Handle each action ────────────────────────────────────────

// ADD — insert or increment
if ($action === 'add') {
    if (!$productId) {
        echo json_encode(['success' => false, 'reason' => 'invalid_product_id']);
        exit;
    }
    $stmt = mysqli_prepare($conn, "
        INSERT INTO user_cart (user_id, product_id, name, price, image, quantity)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ");
    mysqli_stmt_bind_param($stmt, "iisdsi", $userId, $productId, $name, $price, $image, $quantity);
    mysqli_stmt_execute($stmt);
    echo json_encode(['success' => true, 'action' => 'add', 'product_id' => $productId]);
    exit;
}

// UPDATE — set exact quantity (called when user changes qty in cart)
if ($action === 'update') {
    if (!$productId || $quantity < 1) {
        echo json_encode(['success' => false, 'reason' => 'invalid_data']);
        exit;
    }
    $stmt = mysqli_prepare($conn, "
        UPDATE user_cart SET quantity = ? WHERE user_id = ? AND product_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "iii", $quantity, $userId, $productId);
    mysqli_stmt_execute($stmt);
    echo json_encode(['success' => true, 'action' => 'update', 'product_id' => $productId, 'quantity' => $quantity]);
    exit;
}

// REMOVE — delete one item
if ($action === 'remove') {
    if (!$productId) {
        echo json_encode(['success' => false, 'reason' => 'invalid_product_id']);
        exit;
    }
    $stmt = mysqli_prepare($conn, "
        DELETE FROM user_cart WHERE user_id = ? AND product_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "ii", $userId, $productId);
    mysqli_stmt_execute($stmt);
    echo json_encode(['success' => true, 'action' => 'remove', 'product_id' => $productId]);
    exit;
}

// CLEAR — empty the whole cart (called after order is placed)
if ($action === 'clear') {
    $stmt = mysqli_prepare($conn, "DELETE FROM user_cart WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    echo json_encode(['success' => true, 'action' => 'clear']);
    exit;
}

// SYNC — replace entire DB cart with whatever is in localStorage
// Useful as a fallback to make sure DB always matches the browser
if ($action === 'sync') {
    $items = $data['items'] ?? [];
    // Clear existing cart for this user
    $del = mysqli_prepare($conn, "DELETE FROM user_cart WHERE user_id = ?");
    mysqli_stmt_bind_param($del, "i", $userId);
    mysqli_stmt_execute($del);

    if (!empty($items)) {
        $ins = mysqli_prepare($conn, "
            INSERT INTO user_cart (user_id, product_id, name, price, image, quantity)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        foreach ($items as $item) {
            $pid = (int)($item['id']       ?? 0);
            $nm  = trim($item['name']      ?? '');
            $pr  = (float)($item['price']  ?? 0);
            $img = trim($item['image']     ?? '');
            $qty = (int)($item['quantity'] ?? 1);
            if ($pid > 0) {
                mysqli_stmt_bind_param($ins, "iisdsi", $userId, $pid, $nm, $pr, $img, $qty);
                mysqli_stmt_execute($ins);
            }
        }
    }
    echo json_encode(['success' => true, 'action' => 'sync', 'count' => count($items)]);
    exit;
}

echo json_encode(['success' => false, 'reason' => 'unknown_action', 'action' => $action]);