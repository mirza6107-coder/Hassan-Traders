<?php
header('Content-Type: application/json');

$conn = mysqli_connect('localhost', 'root', '', 'htss');

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

// Sanitize inputs
$name          = trim($data['name']          ?? '');
$phone         = trim($data['phone']         ?? '');
$email         = trim($data['email']         ?? '');
$address       = trim($data['address']       ?? '');
$city          = trim($data['city']          ?? 'Sargodha');
$paymentMethod = trim($data['paymentMethod'] ?? 'cod');
$total         = floatval($data['total']     ?? 0);
$cart          = $data['cart']               ?? [];

// Basic validation
if (!$name || !$phone || !$address || empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

mysqli_begin_transaction($conn);

try {

    // 1. Insert Customer
    //    Table: customers (id, name, phone, email, address, city, joined)
    //    'joined' has a DEFAULT of NOW() so we don't need to pass it.
    $stmt = mysqli_prepare($conn,
        "INSERT INTO customers (name, phone, email, address, city) VALUES (?, ?, ?, ?, ?)"
    );
    if (!$stmt) throw new Exception('Prepare failed (customers): ' . mysqli_error($conn));

    mysqli_stmt_bind_param($stmt, 'sssss', $name, $phone, $email, $address, $city);
    mysqli_stmt_execute($stmt);
    $customerId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // 2. Create Order
    //    Table: orders (id, customer_id, total_amount, payment_method, status, order_date)
    //    'status' defaults to 'pending' and 'order_date' defaults to NOW().
    $stmt = mysqli_prepare($conn,
        "INSERT INTO orders (customer_id, total_amount, payment_method) VALUES (?, ?, ?)"
    );
    if (!$stmt) throw new Exception('Prepare failed (orders): ' . mysqli_error($conn));

    mysqli_stmt_bind_param($stmt, 'ids', $customerId, $total, $paymentMethod);
    mysqli_stmt_execute($stmt);
    $orderId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // 3. Insert Order Items
    //    Table: order_items (id, order_id, product_name, price, quantity)
    $stmt = mysqli_prepare($conn,
        "INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)"
    );
    if (!$stmt) throw new Exception('Prepare failed (order_items): ' . mysqli_error($conn));

    foreach ($cart as $item) {
        $productName = trim($item['name']     ?? 'Unknown');
        $price       = floatval($item['price']    ?? 0);
        $quantity    = intval($item['quantity'] ?? 1);

        mysqli_stmt_bind_param($stmt, 'isdi', $orderId, $productName, $price, $quantity);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);

    mysqli_commit($conn);

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
?>