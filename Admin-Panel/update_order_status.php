<?php
// update_order_status.php - Supports Returned (stock restored)

ob_start();
ini_set('display_errors', 0);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST required']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$orderId   = (int)($body['id'] ?? 0);
$newStatus = trim($body['status'] ?? '');

$allowed = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled', 'Returned'];
if ($orderId < 1 || !in_array($newStatus, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

$conn = mysqli_connect('localhost', 'root', '', 'htss');
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed']);
    exit;
}

$currentStatus = null;
$res = mysqli_query($conn, "SELECT status_ENUM FROM orders WHERE id = $orderId LIMIT 1");
if ($row = mysqli_fetch_assoc($res)) {
    $currentStatus = $row['status_ENUM'];
}

$shouldDeduct  = ($newStatus === 'Delivered' && $currentStatus !== 'Delivered');
$shouldRestore = in_array($newStatus, ['Cancelled', 'Returned']) && 
                 !in_array($currentStatus, ['Cancelled', 'Returned']);

$safeStatus = mysqli_real_escape_string($conn, $newStatus);
mysqli_query($conn, "UPDATE orders SET status_ENUM = '$safeStatus' WHERE id = $orderId");

$actionMessage = "";
$itemsAffected = 0;

if ($shouldDeduct) {
    $itemsAffected = deductStock($conn, $orderId);
    $actionMessage = "Stock deducted for $itemsAffected item(s)";
} elseif ($shouldRestore) {
    $itemsAffected = restoreStock($conn, $orderId);
    $actionMessage = "Stock restored for $itemsAffected item(s)";
}

mysqli_close($conn);

echo json_encode([
    'success' => true,
    'message' => "Order #$orderId updated to $newStatus. $actionMessage"
]);

function deductStock($conn, $orderId) {
    $count = 0;
    $result = mysqli_query($conn, "SELECT product_name, quantity FROM order_items WHERE order_id = $orderId");
    while ($item = mysqli_fetch_assoc($result)) {
        $name = trim($item['product_name']);
        $qty = (int)$item['quantity'];
        if ($qty > 0 && $name) {
            $safe = mysqli_real_escape_string($conn, $name);
            $sql = "UPDATE products SET `Quantity` = `Quantity` - $qty 
                    WHERE `P_name` = '$safe' AND `Quantity` >= $qty";
            if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) $count++;
        }
    }
    return $count;
}

function restoreStock($conn, $orderId) {
    $count = 0;
    $result = mysqli_query($conn, "SELECT product_name, quantity FROM order_items WHERE order_id = $orderId");
    while ($item = mysqli_fetch_assoc($result)) {
        $name = trim($item['product_name']);
        $qty = (int)$item['quantity'];
        if ($qty > 0 && $name) {
            $safe = mysqli_real_escape_string($conn, $name);
            $sql = "UPDATE products SET `Quantity` = `Quantity` + $qty WHERE `P_name` = '$safe'";
            if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) $count++;
        }
    }
    return $count;
}
?>