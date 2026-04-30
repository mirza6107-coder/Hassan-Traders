<?php
// get_orders.php - Updated to support ?status= filter (including Returned)

ob_start();
ini_set('display_errors', 0);
set_error_handler(function ($errno, $errstr) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['error' => "PHP ($errno): $errstr"]);
    exit;
});

header('Content-Type: application/json');

$conn = mysqli_connect('localhost', 'root', '', 'htss');
if (!$conn) {
    ob_end_clean();
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// Get optional status filter
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : null;

// Auto-detect columns...
$orderCols = [];
$r = mysqli_query($conn, "SHOW COLUMNS FROM orders");
while ($c = mysqli_fetch_assoc($r)) $orderCols[] = $c['Field'];

$paymentSelect = in_array('payment_method', $orderCols) ? "o.payment_method," : "'—' AS payment_method,";

$statusSelect = in_array('status_ENUM', $orderCols) ? "o.status_ENUM AS status_raw," : "o.status AS status_raw,";

$dateCol = in_array('order_date', $orderCols) ? "o.order_date" : "NOW() AS order_date";

// Customers phone
$custCols = [];
$r = mysqli_query($conn, "SHOW COLUMNS FROM customers");
while ($c = mysqli_fetch_assoc($r)) $custCols[] = $c['Field'];
$phoneSelect = in_array('phone', $custCols) ? "c.phone," : "'—' AS phone,";

// Order items
$hasItems = mysqli_num_rows(mysqli_query($conn, "SHOW TABLES LIKE 'order_items'")) > 0;
$itemsSelect = $hasItems ?
    "GROUP_CONCAT(oi.product_name, ' × ', oi.quantity ORDER BY oi.id SEPARATOR ' + ') AS items, COUNT(oi.id) AS item_count" :
    "'—' AS items, 0 AS item_count";
$itemsJoin = $hasItems ? "LEFT JOIN order_items oi ON oi.order_id = o.id" : "";

// Status filter
$whereClause = "";

if ($statusFilter) {
    $safeStatus = mysqli_real_escape_string($conn, $statusFilter);

    if (in_array('status_ENUM', $orderCols)) {
        $whereClause = "WHERE o.status_ENUM = '$safeStatus'";
    } else {
        $whereClause = "WHERE o.status = '$safeStatus'";
    }
}
// Main Query - Updated for compatibility
$sql = "SELECT
            o.id,
            c.name AS customer_name,
            $phoneSelect
            c.city,
            o.total_amount,
            $statusSelect
            $paymentSelect
            $dateCol,
            $itemsSelect
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        $itemsJoin
        $whereClause
        GROUP BY 
            o.id, 
            c.name, 
            c.city, 
            o.total_amount, 
            o.order_date, 
            status_raw, 
            payment_method
        ORDER BY order_date DESC";

$result = mysqli_query($conn, $sql);

function normaliseStatus($raw)
{
    if (is_numeric($raw)) {
        $map = [0 => 'Pending', 1 => 'Processing', 2 => 'Shipped', 3 => 'Delivered', 4 => 'Cancelled', 5 => 'Returned'];
        return $map[(int)$raw] ?? 'Pending';
    }
    $clean = ucfirst(strtolower(trim($raw)));
    $allowed = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled', 'Returned'];
    return in_array($clean, $allowed) ? $clean : 'Pending';
}

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = [
        'id'         => (int)$row['id'],
        'customer'   => $row['customer_name'] ?? 'Unknown',
        'phone'      => $row['phone'] ?? '—',
        'city'       => $row['city'] ?? '—',
        'amount'     => (float)($row['total_amount'] ?? 0),
        'status'     => normaliseStatus($row['status_raw'] ?? 0),
        'payment'    => $row['payment_method'] ?? '—',
        'date'       => isset($row['order_date']) ? date('d M Y, h:i A', strtotime($row['order_date'])) : '—',
        'items'      => $row['items'] ?? (($row['item_count'] ?? 0) . ' item(s)'),
        'item_count' => (int)($row['item_count'] ?? 0)
    ];
}

ob_end_clean();
echo json_encode($orders);
mysqli_close($conn);
