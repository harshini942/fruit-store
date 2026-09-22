<?php
require_once '../config/database.php';
$result = mysqli_query($conn, "SELECT id, name, price, stock FROM products WHERE stock > 0");
$products = [];
while($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
echo json_encode($products);
?>