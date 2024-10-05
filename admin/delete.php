<?php
// Kết nối đến cơ sở dữ liệu
try {
    $pdo = new PDO('mysql:host=localhost;dbname=shoppingcart', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; // Dừng thực thi nếu không thể kết nối
}

// Kiểm tra xem có ID sản phẩm không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    // Xóa sản phẩm khỏi cơ sở dữ liệu
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$product_id]);

    // Chuyển hướng về trang danh sách sản phẩm sau khi xóa
    header('Location: index.php');
    exit;
} else {
    echo "ID sản phẩm không hợp lệ.";
    exit;
}
?>