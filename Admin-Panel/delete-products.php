<?php
$conn = mysqli_connect('localhost', 'root', '', 'htss');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the ID from the URL parameter
$id = $_GET['id'];

if (isset($id)) {
    // Delete the record matching this ID
    $sql = "DELETE FROM products WHERE ID = $id";

    if (mysqli_query($conn, $sql)) {
        // Redirect back to show the product is gone
        header("Location: all-products.php?msg=deleted");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>