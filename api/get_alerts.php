<?php
require_once '../config/database.php';
$result = mysqli_query($conn, "SELECT * FROM stock_alerts WHERE is_read = 0 ORDER BY created_at DESC LIMIT 10");
$alerts = [];
while($row = mysqli_fetch_assoc($result)) {
    $alerts[] = $row;
}
echo json_encode($alerts);
?>