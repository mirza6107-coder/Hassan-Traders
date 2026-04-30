<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login and signup/login.php");
    exit();
}

$conn = mysqli_connect('localhost', 'root', '', 'htss');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// Get form data
$productname   = mysqli_real_escape_string($conn, $_POST['productname'] ?? '');
$category      = mysqli_real_escape_string($conn, $_POST['category'] ?? '');
$description   = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
$brand         = mysqli_real_escape_string($conn, $_POST['brand'] ?? '');
$status        = mysqli_real_escape_string($conn, $_POST['status'] ?? 'published');
$price         = (float)($_POST['price'] ?? 0);
$orignalprice  = (float)($_POST['orignalprice'] ?? 0);
$quantity      = (int)($_POST['quantity'] ?? 0);

$image_name     = $_FILES['productimage']['name'] ?? '';
$image_tmp_name = $_FILES['productimage']['tmp_name'] ?? '';
$upload_folder  = "uploads/";

if (empty($productname) || empty($category)) {
    die("Product name and category are required.");
}

// === AUTO CREATE CATEGORY IF IT DOESN'T EXIST ===
$check_sql = "SELECT id FROM categories WHERE name = '$category'";
$result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($result) == 0) {
    // Category doesn't exist → Create it automatically
    $insert_cat = "INSERT INTO categories (name, description, status) 
                   VALUES ('$category', '', 'active')";
    mysqli_query($conn, $insert_cat);
}

// Create uploads folder if not exists
if (!is_dir($upload_folder)) {
    mkdir($upload_folder, 0777, true);
}

// Handle image upload
if (!empty($image_name) && move_uploaded_file($image_tmp_name, $upload_folder . $image_name)) {
    $image_path = $image_name;
} else {
    $image_path = 'no-image.jpg'; // fallback
}

// Insert product
$sql = "INSERT INTO products (P_name, Category, Description, Price, orignalprice, Quantity, Brand, Status, P_image)
        VALUES ('$productname', '$category', '$description', '$price', '$orignalprice', '$quantity', '$brand', '$status', '$image_path')";

if (mysqli_query($conn, $sql)) {
    header("Location: addNEWproducts.php?success=1&new_product=1");
    exit();
} else {
    echo "Database Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>