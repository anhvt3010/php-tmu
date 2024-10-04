<?php
// Kết nối đến cơ sở dữ liệu
try {
    $pdo = new PDO('mysql:host=localhost;dbname=shoppingcart', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; // Dừng thực thi nếu không thể kết nối
}

// Bắt đầu mã của bạn
include "functions.php";

// Kiểm tra xem có ID đơn hàng không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $order_id = (int)$_GET['id'];

    // Truy vấn để lấy thông tin đơn hàng
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // Truy vấn để lấy chi tiết sản phẩm trong đơn hàng
    $stmt = $pdo->prepare('SELECT od.*, p.title, p.price FROM order_detail od JOIN products p ON od.product_id = p.id WHERE od.order_id = ?');
    $stmt->execute([$order_id]);
    $order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tính toán tổng giá tiền
    $total_price = 0;
    foreach ($order_details as $detail) {
        $total_price += $detail['price'] * $detail['quantity'];
    }

    // Cập nhật trạng thái đơn hàng
    if (isset($_POST['update_status'])) {
        $new_status = (int)$_POST['status'];
        $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$new_status, $order_id]);
        // Cập nhật lại thông tin đơn hàng sau khi thay đổi trạng thái
        $order['status'] = $new_status; // Cập nhật trạng thái trong biến

        header('Location: order.php');
    }
} else {
    // Nếu không có ID đơn hàng, chuyển hướng về trang danh sách đơn hàng
    header('Location: orders.php');
    exit;
}
?>

<?= template_header('Order Details') ?>

    <div class="container-fluid">
        <h2>Order Details</h2>
        <h4>Customer Information</h4>
        <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['customer_address']) ?></p>

        <form method="post">
            <p><strong>Status:</strong>
                <select name="status">
                    <option value="0" <?= $order['status'] == 0 ? 'selected' : '' ?>>Pending</option>
                    <option value="1" <?= $order['status'] == 1 ? 'selected' : '' ?>>Accepted</option>
                    <option value="2" <?= $order['status'] == 2 ? 'selected' : '' ?>>Waiting for Delivery</option>
                    <option value="3" <?= $order['status'] == 3 ? 'selected' : '' ?>>Delivering</option>
                    <option value="4" <?= $order['status'] == 4 ? 'selected' : '' ?>>Completed</option>
                    <option value="5" <?= $order['status'] == 5 ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit" name="update_status" class="btn btn-primary">Update</button>
            </p>
        </form>

        <p><strong>Date Added:</strong> <?= date('Y-m-d H:i:s', strtotime($order['date_added'])) ?></p>

        <h4>Order Products</h4>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($order_details as $detail): ?>
                <tr>
                    <td><?= htmlspecialchars($detail['title']) ?></td>
                    <td><?= number_format($detail['price'], 0, ',', '.') ?>.000 VND</td>
                    <td><?= $detail['quantity'] ?></td>
                    <td><?= number_format($detail['price'] * $detail['quantity'], 0, ',', '.') ?>.000 VND</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <h4>Total Price: <?= number_format($total_price + 30, 0, ',', '.') ?>.000 VND (Including Delivery Fee)</h4>
    </div>

<?= template_footer() ?>