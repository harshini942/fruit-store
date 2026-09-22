<?php
// Database සම්බන්ධ කිරීම
require_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // බිල්පතේ දත්ත ලබා ගැනීම (ඔබේ form එකේ නම් අනුව වෙනස් විය හැක)
    $product_id = $_POST['product_id'];
    $sale_quantity = $_POST['quantity']; // පාරිභෝගිකයා ගන්නා ප්‍රමාණය

    // 1. තොගය පරීක්ෂා කිරීම (Stock Validation)
    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo json_encode(["status" => "error", "message" => "භාණ්ඩය සොයාගත නොහැක!"]);
        exit();
    }

    if ($product['stock'] < $sale_quantity) {
        // තොගය මදි නම් බිල්පතක් සෑදෙන්නේ නැත
        echo json_encode(["status" => "error", "message" => "දෝෂයකි: ගබඩාවේ ප්‍රමාණවත් තොගයක් නොමැත! (පවතින තොගය: " . $product['stock'] . " kg)"]);
        exit();
    }

    // 2. තොගය ප්‍රමාණවත් නම්, බිල්පත සෑදීම සහ තොගය අඩු කිරීම
    $conn->begin_transaction(); // දත්ත ගබඩාවේ ආරක්ෂාව සඳහා (Transaction)

    try {
        // තොගය අඩු කිරීම
        $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $update_stmt->bind_param("di", $sale_quantity, $product_id);
        $update_stmt->execute();

        // [මෙතැනට ඔබේ බිල්පත DB එකට Save කරන INSERT කේතය දමන්න]
        // උදාහරණයක් ලෙස:
        // $insert_stmt = $conn->prepare("INSERT INTO invoices ...");
        // $insert_stmt->execute();

        $conn->commit(); // සියල්ල සාර්ථක නම් දත්ත Save කරන්න
        echo json_encode(["status" => "success", "message" => "බිල්පත සාර්ථකව සකසන ලදී."]);

    } catch (Exception $e) {
        $conn->rollback(); // දෝෂයක් ආවොත් වෙනස්කම් ඉවත් කරන්න
        echo json_encode(["status" => "error", "message" => "පද්ධති දෝෂයක්: " . $e->getMessage()]);
    }
}
?>