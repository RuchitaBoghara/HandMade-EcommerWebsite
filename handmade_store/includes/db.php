<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "handmade_store";


$conn = new mysqli("localhost", "root", "", "handmade_store");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
