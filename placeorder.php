<?= template_header('Place Order') ?>

    <div class="placeorder content-wrapper" style="height: 500px; display: flex; align-items: center; justify-content: center; text-align: center;">
        <div>
            <h1 class="mt-5">Your Order Has Been Placed</h1>
            <p>Thank you for ordering with us! We'll contact you by email with your order details.</p>

            <?php
            // Kiểm tra xem có thông tin đơn hàng trong URL không
            if (isset($_GET['order_info'])) {
                $order_info = unserialize(urldecode($_GET['order_info']));

                echo '<h2>Order Information</h2>';
                echo '<p><strong>Full Name:</strong> ' . htmlspecialchars($order_info['fullname']) . '</p>';
                echo '<p><strong>Phone:</strong> ' . htmlspecialchars($order_info['phone']) . '</p>';
                echo '<p><strong>Address:</strong> ' . htmlspecialchars($order_info['address']) . '</p>';
                echo '<h3>Cart Items:</h3>';
                echo '<ul>';
                foreach ($order_info['cart'] as $product_id => $quantity) {
                    echo '<li>Product ID: ' . htmlspecialchars($product_id) . ' - Quantity: ' . htmlspecialchars($quantity) . '</li>';
                }
                echo '</ul>';
                echo '<p><strong>Subtotal:</strong> ' . number_format($order_info['subtotal'], 0, ',', '.') . ' VND</p>';
                echo '<p><strong>Total:</strong> ' . number_format($order_info['total'], 0, ',', '.') . ' VND</p>';
            }
            ?>

        </div>
    </div>

<?= template_footer() ?>

<?php
// Xóa giỏ hàng sau khi đặt hàng
unset($_SESSION['cart']);
?>