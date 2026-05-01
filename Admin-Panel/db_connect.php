<?php
// db_connect.php
$host     = "localhost";
$dbname   = "htss";   // ← CHANGE THIS
$user     = "root"; 
$pass     = ""; 

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>