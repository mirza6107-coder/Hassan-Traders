<?php
header('Content-Type: application/json');

$conn = mysqli_connect('localhost', 'root', '', 'htss');

if (!$conn) {
    echo json_encode(['error' => 'Connection failed']);
    exit;
}

$sql = "SELECT 
            c.id, 
            c.name, 
            c.phone,
            c.email,
            c.address,
            c.city, 
            c.joined,
            COUNT(o.id) as total_orders,
            IFNULL(SUM(o.total_amount), 0) as total_spent
        FROM customers c
        LEFT JOIN orders o ON c.id = o.customer_id
        GROUP BY c.id
        ORDER BY c.joined DESC";

$result = mysqli_query($conn, $sql);
$customers = [];

while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = [
        'id'      => $row['id'],
        'name'    => $row['name'],
        'phone'   => $row['phone'],
        'email'   => $row['email']   ?? '',
        'address' => $row['address'] ?? '',
        'city'    => $row['city'],
        'orders'  => (int)$row['total_orders'],
        'spent'   => (float)$row['total_spent'],
        'joined'  => date('d M Y', strtotime($row['joined']))
    ];
}

echo json_encode($customers);
mysqli_close($conn);
?>