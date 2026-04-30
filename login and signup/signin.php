<?php
session_start(); 
$conn = mysqli_connect('localhost', 'root', '', 'htss');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['emailaddress']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE Email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashed_password_db = $row['password'];

        if (password_verify($password, $hashed_password_db)) {
            // Store common user data
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['user_name'] = $row['fullname']; // Fixed: Use 'Fullname' to match your DB
            $_SESSION['role']      = $row['role'];     // Ensure this column exists in your DB
            $_SESSION['email']     = $row['Email'];    // Recommended for profile.php

            if ($_SESSION['role'] == 'admin') {
                header("Location: ../Admin-Panel/dashboard.php");
            } else {
                header("Location: ../Home/home.php");
            }
            exit();
        } else {
            echo "<script>alert('Invalid Password'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found'); window.location='login.php';</script>";
    }
}
mysqli_close($conn);
?>