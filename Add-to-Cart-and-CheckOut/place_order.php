<?php
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

$loggedInUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;


$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

$name          = trim($data['name']          ?? '');
$phone         = trim($data['phone']         ?? '');
$email         = trim($data['email']         ?? '');
$address       = trim($data['address']       ?? '');
$city          = trim($data['city']          ?? 'Sargodha');
$paymentMethod = trim($data['paymentMethod'] ?? 'cod');
$total         = floatval($data['total']     ?? 0);
$cart          = $data['cart']               ?? [];

if (!$name || !$phone || !$address || empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

mysqli_begin_transaction($conn);

try {
    // 1. Insert Customer
    // customers table columns: id, name, phone, email, address, city, joined
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO customers (name, phone, email, address, city) VALUES (?, ?, ?, ?, ?)"
    );
    if (!$stmt) throw new Exception('Prepare failed (customers): ' . mysqli_error($conn));
    mysqli_stmt_bind_param($stmt, 'sssss', $name, $phone, $email, $address, $city);
    mysqli_stmt_execute($stmt);
    $customerId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // 2. Create Order
    // orders table columns: id, customer_id, total_amount, status_ENUM, payment_method, order_date
    // status_ENUM defaults to 'Pending' — we don't need to pass it
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO orders (customer_id, user_id,total_amount, payment_method) VALUES (?, ?, ?,?)"
    );
    if (!$stmt) throw new Exception('Prepare failed (orders): ' . mysqli_error($conn));
    mysqli_stmt_bind_param($stmt, 'iids', $customerId, $loggedInUserId, $total, $paymentMethod);
    mysqli_stmt_execute($stmt);
    $orderId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // 3. Insert Order Items
    // order_items columns: id, order_id, product_name, quantity, price
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)"
    );
    if (!$stmt) throw new Exception('Prepare failed (order_items): ' . mysqli_error($conn));
    foreach ($cart as $item) {
        $productName = trim($item['name']      ?? 'Unknown');
        $price       = floatval($item['price'] ?? 0);
        $quantity    = intval($item['quantity'] ?? 1);
        mysqli_stmt_bind_param($stmt, 'isdi', $orderId, $productName, $price, $quantity);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);

    // 4. Clear user's DB cart after successful order
    if (isset($_SESSION['user_id'])) {
        $userId    = (int)$_SESSION['user_id'];
        $clearStmt = mysqli_prepare($conn, "DELETE FROM user_cart WHERE user_id = ?");
        mysqli_stmt_bind_param($clearStmt, "i", $userId);
        mysqli_stmt_execute($clearStmt);
        mysqli_stmt_close($clearStmt);
    }

    mysqli_commit($conn);

    // Save shipping info to session for auto-fill on next checkout
    $_SESSION['checkout_name']    = $name;
    $_SESSION['checkout_phone']   = $phone;
    $_SESSION['checkout_email']   = $email;
    $_SESSION['checkout_address'] = $address;
    $_SESSION['checkout_city']    = $city;

    echo json_encode([
        'success'  => true,
        'order_id' => $orderId,
        'message'  => 'Order placed successfully'
    ]);
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
