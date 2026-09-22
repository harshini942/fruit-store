<?php
// config/database.php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'fruit_store_db';

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>