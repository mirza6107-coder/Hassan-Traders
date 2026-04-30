<?php
// 1. Database Connection
$conn = mysqli_connect('localhost', 'root', '', 'htss');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 2. Get the ID from the URL (e.g., view-products.php?id=12)
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// 3. Fetch only this specific product
$query = "SELECT * FROM products WHERE ID = $id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

// 4. Redirect if product doesn't exist
if (!$product) {
    header("Location: all-products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $product['P_name']; ?> - Hassan Traders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="row g-0">
                        <div class="col-md-5">
                            <img src="uploads/<?php echo $product['P_image']; ?>" class="img-fluid rounded-start" alt="Product Image">
                        </div>
                        <div class="col-md-7">
                            <div class="card-body">
                                <h2 class="card-title"><?php echo $product['P_name']; ?></h2>
                                <p class="badge bg-primary"><?php echo $product['Category']; ?></p>
                                <p class="text-muted">Brand: <?php echo $product['Brand']; ?></p>
                                <hr>
                                <h3 class="text-danger">Rs. <?php echo number_format($product['Price']); ?></h3>
                                <h3 class="text-danger">Rs. <?php echo number_format($product['orignalprice']); ?></h3>
                                <p><strong>Stock Available:</strong> <?php echo $product['Quantity']; ?></p>
                                <p><strong>Status:</strong> <?php echo ucfirst($product['Status']); ?></p>
                                <p class="mt-3"><strong>Description:</strong><br>
                                    <?php echo nl2br($product['Description']); ?></p>

                                <a href="all-products.php" class="btn btn-secondary mt-3">Back to Inventory</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>