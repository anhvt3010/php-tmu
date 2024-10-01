<?php
// Kiểm tra đã có tham số mô tả đường dẫn URL chưa?
if (isset($_GET['id'])) {
    // Chuẩn bị kết nối và thực thi, tránh lỗi SQL injection
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);

    // Dùng Fetch lấy sản phẩm từ CSDL cho vào mảng Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra xem đã có sản phẩm đó chưa?
    if (!$product) {
        // Nếu lỗi thì báo lỗi mảng không rỗng
        exit('Sản phẩm không tồn tại!');
    }
} else {
    // Trường hợp không hiển thị hoặc không có sản phẩm
    exit('Sản phẩm không tồn tại!');
}
?>

<?= template_header('Sản phẩm') ?>

    <div class="hero-wrap hero-bread" style="background-image: url('imgs/banner-detail-product.jpg');">
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 ftco-animate text-center">
<!--                    <p class="breadcrumbs">-->
<!--                        <span class="mr-2"><a href="index.html">Trang chủ</a></span>-->
<!--                        <span class="mr-2"><a href="index.html">Sản phẩm</a></span>-->
<!--                        <span>Chi tiết sản phẩm</span>-->
<!--                    </p>-->
<!--                    <h1 class="mb-0 bread">Chi tiết sản phẩm</h1>-->
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-5 ftco-animate">
                    <a href="" class="image-popup">
                        <img src="imgs/<?= $product['img'] ?>" class="img-fluid" alt="<?= $product['title'] ?>">
                    </a>
                </div>
                <div class="col-lg-6 product-details pl-md-5 ftco-animate">
                    <h3><?= $product['title'] ?></h3>
                    <div class="rating d-flex">
                        <p class="text-left mr-4">
                            <a href="#" class="mr-2">5.0</a>
                            <a href="#"><span class="ion-ios-star-outline"></span></a>
                            <a href="#"><span class="ion-ios-star-outline"></span></a>
                            <a href="#"><span class="ion-ios-star-outline"></span></a>
                            <a href="#"><span class="ion-ios-star-outline"></span></a>
                            <a href="#"><span class="ion-ios-star-outline"></span></a>
                        </p>
                        <p class="text-left mr-4">
                            <a href="#" class="mr-2" style="color: #000;">100 <span style="color: #bbb;">Đánh giá</span></a>
                        </p>
                        <p class="text-left">
                            <a href="#" class="mr-2" style="color: #000;">500 <span style="color: #bbb;">Đã bán</span></a>
                        </p>
                    </div>
                    <p class="price"><span>&dollar;<?= $product['price'] ?></span></p>
                    <p><?php echo "<pre>" . htmlspecialchars($product['description']) . "</pre>"; ?></p>
                    <form action="index.php?page=cart" method="post">
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-group d-flex">
                                    <div class="select-wrap">
                                        <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                                        <select name="" id="" class="form-control">
                                            <option value="">Nhỏ</option>
                                            <option value="">Vừa</option>
                                            <option value="">Lớn</option>
                                            <option value="">Rất lớn</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="w-100"></div>
                            <div class="input-group col-md-6 d-flex mb-3">
                                <span class="input-group-btn mr-2">
                                    <button type="button" class="quantity-left-minus btn" data-type="minus" data-field="">
                                        <i class="ion-ios-remove"></i>
                                    </button>
                                </span>
                                <input type="number" id="quantity" name="quantity" class="form-control input-number" value="1"
                                       min="1" max="<?= $product['quantity'] ?>" required>
                                <span class="input-group-btn ml-2">
                                    <button type="button" class="quantity-right-plus btn" data-type="plus" data-field="">
                                        <i class="ion-ios-add"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="w-100"></div>
                            <div class="col-md-12">
                                <p style="color: #000;"><?= $product['quantity'] ?> kg có sẵn</p>
                            </div>
                        </div>
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <p><input type="submit" class="btn btn-black py-3 px-5" value="Thêm vào giỏ hàng"></p>
                    </form>
                </div>
            </div>
        </div>
    </section>

<?= template_footer() ?>
<script>
    $(document).ready(function(){
        var quantitiy = 0;
        $('.quantity-right-plus').click(function(e){
            e.preventDefault();
            var quantity = parseInt($('#quantity').val());
            var max = parseInt($('#quantity').attr('max'));
            if(quantity < max){
                $('#quantity').val(quantity + 1);
            }
        });

        $('.quantity-left-minus').click(function(e){
            e.preventDefault();
            var quantity = parseInt($('#quantity').val());
            if(quantity > 1){
                $('#quantity').val(quantity - 1);
            }
        });
    });
</script>
