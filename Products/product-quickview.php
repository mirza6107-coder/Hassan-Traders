<?php
// product-quickview.php — returns JSON for Quick View modal
header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) { echo json_encode(['success' => false]); exit; }

$conn = mysqli_connect('localhost', 'root', '', 'htss');
if (!$conn) { echo json_encode(['success' => false, 'message' => 'DB error']); exit; }

$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE ID = ? AND LOWER(Status) = 'published' LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$p = mysqli_fetch_assoc($result);
mysqli_close($conn);

if (!$p) { echo json_encode(['success' => false]); exit; }

echo json_encode([
  'success'       => true,
  'id'            => (int)$p['ID'],
  'name'          => $p['P_name'] ?? '—',
  'brand'         => $p['Brand'] ?? '',
  'category'      => $p['Category'] ?? '',
  'price'         => (float)$p['Price'],
  'orignalprice'  => (float)($p['orignalprice'] ?? 0),
  'stock'         => (int)($p['Quantity'] ?? 0),
  'image'         => $p['P_image'] ?? '',
  'description'   => $p['Description'] ?? '',
]);
