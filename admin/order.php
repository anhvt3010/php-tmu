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

// Truy vấn để lấy danh sách đơn đặt hàng
$stmt = $pdo->prepare('SELECT * FROM orders ORDER BY date_added DESC');
$stmt->execute();

// Dùng hàm Fetch để trả về danh sách đơn đặt hàng
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?= template_header('Orders') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Orders List</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Customer Phone</th>
                        <th>Customer Address</th>
                        <th>Status</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Customer Phone</th>
                        <th>Customer Address</th>
                        <th>Status</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                    </tfoot>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td><?= htmlspecialchars($order['customer_phone']) ?></td>
                            <td><?= htmlspecialchars($order['customer_address']) ?></td>
                            <td>
                                <?php
                                // Hiển thị trạng thái theo giá trị
                                switch ($order['status']) {
                                    case 0:
                                        echo 'Pending';
                                        break;
                                    case 1:
                                        echo 'Accepted';
                                        break;
                                    case 2:
                                        echo 'Waiting for Delivery';
                                        break;
                                    case 3:
                                        echo 'Delivering';
                                        break;
                                    case 4:
                                        echo 'Completed';
                                        break;
                                    case 5:
                                        echo 'Cancelled';
                                        break;
                                    default:
                                        echo 'Unknown Status';
                                }
                                ?>
                            </td>
                            <td><?= date('Y-m-d H:i:s', strtotime($order['date_added'])) ?></td>
                            <td>
                                <a href="view_order.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">View</a>
                                <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $order['id'] ?>)">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?= template_footer() ?>
<script>
    function confirmDelete(orderId) {
        if (confirm("Bạn có chắc chắn muốn xóa đơn hàng này?")) {
            // Nếu người dùng xác nhận, chuyển hướng đến trang xóa
            window.location.href = 'delete_order.php?id=' + orderId;
        }
    }
</script>