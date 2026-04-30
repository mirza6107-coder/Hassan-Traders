<?php
$host = "localhost";
$user = "root"; 
$pass = ""; 
$dbname = "htss"; 

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Set charset
mysqli_set_charset($conn, "utf8mb4");
?>