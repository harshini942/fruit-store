<?php
include '../config/database.php'; // ඔබේ database connection එක

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $added_qty = $_POST['quantity'];

    // 1. Stock එක update කිරීම
    $sql = "UPDATE products SET stock = stock + ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $added_qty, $product_id);
    $stmt->execute();

    // 2. Log එක record කිරීම
    $sql_log = "INSERT INTO stock_logs (product_id, quantity_added) VALUES (?, ?)";
    $stmt_log = $conn->prepare($sql_log);
    $stmt_log->bind_param("ii", $product_id, $added_qty);
    $stmt_log->execute();

    echo json_encode(["status" => "success", "message" => "Stock updated successfully"]);
}
?>