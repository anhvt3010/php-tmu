<?php
// Kết nối đến cơ sở dữ liệu
try {
    $pdo = new PDO('mysql:host=localhost;dbname=shoppingcart', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; // Dừng thực thi nếu không thể kết nối
}
include "functions.php";

// Kiểm tra xem có ID sản phẩm không
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = (int)$_GET['id'];

    // Lấy thông tin sản phẩm từ cơ sở dữ liệu
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra xem sản phẩm có tồn tại không
    if (!$product) {
        echo "Sản phẩm không tồn tại.";
        exit;
    }

    // Xử lý lưu thông tin sản phẩm
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];

        // Xử lý tải lên hình ảnh
        $img = $_FILES['img']['name'];
        $target_dir = "E:/xampp/htdocs/shoppingcart/imgs/"; // Đường dẫn thư mục lưu trữ hình ảnh
        $target_file = $target_dir . basename($img);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra xem tệp có phải là hình ảnh không
        $check = getimagesize($_FILES['img']['tmp_name']);
        if ($check === false) {
            echo "Tệp không phải là hình ảnh.";
            $uploadOk = 0;
        }

        // Kiểm tra kích thước tệp
        if ($_FILES['img']['size'] > 500000) { // Giới hạn kích thước tệp 500KB
            echo "Xin lỗi, tệp của bạn quá lớn.";
            $uploadOk = 0;
        }

        // Kiểm tra định dạng tệp
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "Xin lỗi, chỉ cho phép tệp JPG, JPEG, PNG & GIF.";
            $uploadOk = 0;
        }

        // Nếu tất cả các kiểm tra đều thành công, di chuyển tệp đến thư mục đích
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES['img']['tmp_name'], $target_file)) {
                // Cập nhật thông tin sản phẩm vào cơ sở dữ liệu
                $stmt = $pdo->prepare('UPDATE products SET title = ?, price = ?, quantity = ?, img = ? WHERE id = ?');
                $stmt->execute([$title, $price, $quantity, $img, $product_id]);

                // Chuyển hướng về trang danh sách sản phẩm sau khi lưu
                header('Location: index.php');
                exit;
            } else {
                echo "Xin lỗi, đã xảy ra lỗi khi tải lên tệp.";
            }
        }
    }
} else {
    echo "ID sản phẩm không hợp lệ.";
    exit;
}
?>

<?= template_header('Edit Product') ?>

    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Edit Product</h1>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Product Name</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($product['quantity']) ?>" required>
            </div>
            <div class="form-group">
                <label for="img">Upload Image</label>
                <input type="file" class="form-control" id="img" name="img" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

<?= template_footer() ?>