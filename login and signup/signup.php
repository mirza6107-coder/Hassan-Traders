<?php
session_start();
$conn = mysqli_connect('localhost', 'root', '', 'htss');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $emailaddress = mysqli_real_escape_string($conn, $_POST['emailaddress']);
    $password = $_POST['password'];

    // Securely hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (Fullname, Email, password)
            VALUES ('$fullname','$emailaddress','$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        // Log them in automatically after signup
        $_SESSION['user_name'] = $fullname;
        header("Location: ../login and signup/login.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
mysqli_close($conn);
