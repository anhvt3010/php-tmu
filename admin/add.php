<?php
include "functions.php";
// Kết nối đến cơ sở dữ liệu
try {
    $pdo = new PDO('mysql:host=localhost;dbname=shoppingcart', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; // Dừng thực thi nếu không thể kết nối
}

// Xử lý lưu thông tin sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    // Xử lý tải lên hình ảnh
    $img = $_FILES['img']['name'];
    $target_dir = "C:/xampp/htdocs/shoppingcart/imgs/"; // Đường dẫn thư mục lưu trữ hình ảnh
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
    if (!in_array($imageFileType, ['jpg'])) {
        echo "Xin lỗi, chỉ cho phép tệp JPG.";
        $uploadOk = 0;
    }

    // Nếu tất cả các kiểm tra đều thành công, di chuyển tệp đến thư mục đích
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES['img']['tmp_name'], $target_file)) {
            // Thêm thông tin sản phẩm vào cơ sở dữ liệu
            $stmt = $pdo->prepare('INSERT INTO products (title, price, quantity, img, description, category, date_added) VALUES (?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$title, $price, $quantity, $img, $description, $category]);

            // Chuyển hướng về trang danh sách sản phẩm sau khi lưu
            header('Location: index.php');
            exit;
        } else {
            echo "Xin lỗi, đã xảy ra lỗi khi tải lên tệp.";
        }
    }
}
?>

<?= template_header('Add Product') ?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Add New Product</h1>
    <div class="row">
        <div class="col-md-6">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Product Name</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" class="form-control" id="price" name="price" required>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <select class="form-control" id="category" name="category" required>
                        <option value="noodles">Noodles</option>
                        <option value="fruits">Fruits</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="img">Upload Image</label>
                    <input type="file" class="form-control" id="img" name="img" required onchange="previewImage(event)">
                </div>
                <button type="submit" class="btn btn-primary">Add Product</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
        <div class="col-md-6">
            <h4>Image Preview</h4>
            <img id="preview" src="#" alt="Image Preview" style="max-width: 100%; height: auto; display: none;">
        </div>
    </div>

</div>

<?= template_footer() ?>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('preview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>