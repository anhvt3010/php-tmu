<?php
// Kết nối đến cơ sở dữ liệu
try {
    $pdo = new PDO('mysql:host=localhost;dbname=shoppingcart', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Kiểm tra xem có thông tin đơn hàng trong URL không
if (isset($_GET['order_info'])) {
    $order_info = unserialize(urldecode($_GET['order_info']));

    // Cập nhật số lượng sản phẩm trong kho
    foreach ($order_info['cart'] as $product_id => $quantity) {
        $stmt = $pdo->prepare('UPDATE products SET quantity = quantity - ? WHERE id = ?');
        $stmt->execute([$quantity, $product_id]);
    }
}
?>

<?= template_header('Place Order') ?>

    <div class="placeorder content-wrapper" style="height: 500px; display: flex; align-items: center; justify-content: center; text-align: center;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Order Information</h2>
                    <?php
                    if (isset($order_info)) {
                        echo '<p><strong>Full Name:</strong> ' . htmlspecialchars($order_info['fullname']) . '</p>';
                        echo '<p><strong>Phone:</strong> ' . htmlspecialchars($order_info['phone']) . '</p>';
                        echo '<p><strong>Address:</strong> ' . htmlspecialchars($order_info['address']) . '</p>';
                        echo '<p><strong>Subtotal:</strong> ' . number_format($order_info['subtotal'], 0, ',', '.') . '.000 VND</p>';
                        echo '<p><strong>Delivery:</strong> 30.000 VND</p>';
                        echo '<p><strong>Total:</strong> ' . number_format($order_info['total'], 0, ',', '.') . '.000 VND</p>';
                    }
                    ?>
                </div>
                <div class="col-md-6">
                    <h3>Cart Items</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php
                        if (isset($order_info)) {
                            foreach ($order_info['cart'] as $product_id => $quantity) {
                                $stmt = $pdo->prepare('SELECT title, img FROM products WHERE id = ?');
                                $stmt->execute([$product_id]);
                                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($product) {
                                    echo '<li style="display: flex; align-items: center; margin-bottom: 10px;">';
                                    echo '<img src="imgs/' . htmlspecialchars($product['img']) . '" width="50" height="50" alt="' . htmlspecialchars($product['title']) . '" style="margin-right: 10px;">';
                                    echo '<span>' . htmlspecialchars($product['title']) . '</span>';
                                    echo '<span style="margin-left: auto;">Quantity: ' . htmlspecialchars($quantity) . '</span>';
                                    echo '</li>';
                                }
                            }
                        }
                        ?>
                    </ul>
                </div>
                <a class="btn btn-primary py-3 px-4 mt-3" href="index.php">Back to home</a>
            </div>
        </div>
    </div>

<?= template_footer() ?>

<?php
// Xóa giỏ hàng sau khi đặt hàng
unset($_SESSION['cart']);
?>