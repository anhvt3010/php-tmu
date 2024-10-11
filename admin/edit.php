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
        $description = $_POST['description'];

        // Xử lý tải lên hình ảnh
        $img = $_FILES['img']['name'];
        $target_dir = "C:/xampp/htdocs/shoppingcart/imgs/"; // Đường dẫn thư mục lưu trữ hình ảnh
        $target_file = $target_dir . basename($img);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra xem tệp có phải là hình ảnh không
        if (!empty($_FILES['img']['tmp_name'])) {
            $check = getimagesize($_FILES['img']['tmp_name']);
            if ($check === false) {
                echo "Tệp không phải là hình ảnh.";
                $uploadOk = 0;
            }

            // Kiểm tra kích thước tệp
            if ($_FILES['img']['size'] > 5000000) { // Giới hạn kích thước tệp 5MB
                echo "Xin lỗi, tệp của bạn quá lớn.";
                $uploadOk = 0;
            }

            // Kiểm tra định dạng tệp
            if (!in_array($imageFileType, ['jpg'])) {
                echo "Xin lỗi, chỉ cho phép tệp JPG";
                $uploadOk = 0;
            }
        } else {
            // Nếu không có tệp hình ảnh mới, giữ lại hình ảnh cũ
            $img = $product['img'];
            $uploadOk = 1; // Đặt uploadOk thành 1 để không kiểm tra tải lên
        }

        // Nếu tất cả các kiểm tra đều thành công, di chuyển tệp đến thư mục đích
        if ($uploadOk == 1) {
            if (!empty($_FILES['img']['tmp_name'])) {
                if (move_uploaded_file($_FILES['img']['tmp_name'], $target_file)) {
                    // Cập nhật thông tin sản phẩm vào cơ sở dữ liệu
                    $stmt = $pdo->prepare('UPDATE products SET title = ?, price = ?, quantity = ?, img = ?, description = ? WHERE id = ?');
                    $stmt->execute([$title, $price, $quantity, $img, $description, $product_id]);
                } else {
                    echo "Xin lỗi, đã xảy ra lỗi khi tải lên tệp.";
                }
            } else {
                // Cập nhật thông tin sản phẩm mà không thay đổi hình ảnh
                $stmt = $pdo->prepare('UPDATE products SET title = ?, price = ?, quantity = ?, description = ? WHERE id = ?');
                $stmt->execute([$title, $price, $quantity, $description, $product_id]);
            }

            // Chuyển hướng về trang danh sách sản phẩm sau khi lưu
            header('Location: index.php');
            exit;
        }
    }
} else {
    echo "ID sản phẩm không hợp lệ.";
    exit;
}
?>

<?= template_header('Edit Product') ?>

    <div class="container-fluid row">
        <div class="col-md-6">
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
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5" required><?= htmlspecialchars($product['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="img">Upload Image</label>
                    <input type="file" class="form-control" id="img" name="img">
                    <small class="form-text text-muted">Leave blank to keep the current image.</small>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
        <div class="col-md-6">
            <h4>Current Image</h4>
            <img src="../imgs/<?= htmlspecialchars($product['img']) ?>" width="400" height="400" alt="<?= htmlspecialchars($product['title']) ?>" class="img-fluid">
        </div>
    </div>

<?= template_footer() ?>