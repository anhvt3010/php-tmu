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

// Số lượng sản phẩm mỗi trang được hiển thị trên page
$num_products_on_each_page = 10; // Bạn có thể điều chỉnh số lượng sản phẩm hiển thị mỗi trang

// Trang hiện tại
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;

// Truy vấn để lấy danh sách sản phẩm
$stmt = $pdo->prepare('SELECT * FROM products ORDER BY date_added DESC LIMIT ?, ?');
$stmt->bindValue(1, ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(2, $num_products_on_each_page, PDO::PARAM_INT);
$stmt->execute();

// Dùng hàm Fetch để trả về danh sách sản phẩm
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy tổng số sản phẩm
$total_products = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
?>

<?= template_header('Home') ?>

    <div class="container-fluid">
        <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4 mb-3">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_length" id="dataTable_length">
                        <label>
                            <a href="add.php" class="btn btn-success">New Product</a>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Products List</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Image</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                        </tfoot>
                        <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['title']) ?></td>
                                <td><img src="../imgs/<?= htmlspecialchars($product['img']) ?>" width="50" height="50" alt="<?= htmlspecialchars($product['title']) ?>"></td>
                                <td><?= number_format($product['price'], 0, ',', '.') ?>.000 VND</td>
                                <td><?= $product['quantity'] ?></td>
                                <td><?= date('Y-m-d', strtotime($product['date_added'])) ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $product['id'] ?>)">Delete</a>
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
    function confirmDelete(productId) {
        if (confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) {
            // Nếu người dùng xác nhận, chuyển hướng đến trang xóa
            window.location.href = 'delete.php?id=' + productId;
        }
    }
</script>

