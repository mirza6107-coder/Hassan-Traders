<?php
$conn = mysqli_connect('localhost', 'root', '', 'htts');

if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $p_name = mysqli_real_escape_string($conn, $_POST['productname']);
    $category = $_POST['category'];
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $orignalprice    = mysqli_real_escape_string($conn, $_POST['orignalprice']);

    $quantity = $_POST['quantity'];

    // IMAGE LOGIC
    $image_name = $_FILES['productimage']['name'];

    if (!empty($image_name)) {
        // If a new image is uploaded
        $image_tmp_name = $_FILES['productimage']['tmp_name'];
        move_uploaded_file($image_tmp_name, "uploads/" . $image_name);

        $sql = "UPDATE products SET 
                P_name='$p_name', Category='$category', Brand='$brand', 
                Description='$description', Price='$price',orignalprice='$orignalprice', Quantity='$quantity', 
                P_image='$image_name' 
                WHERE ID=$id";
    } else {
        // If NO new image is uploaded, keep the old one (don't update P_image)
        $sql = "UPDATE products SET 
                P_name='$p_name', Category='$category', Brand='$brand', 
                Description='$description', Price='$price',orignalprice='$orignalprice', Quantity='$quantity' 
                WHERE ID=$id";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: all-products.php?msg=updated");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
