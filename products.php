<?php
// Số lượng sản phẩm mỗi trang được hiển thị trên page 
global $pdo;
$num_products_on_each_page = 4;

// Trang hiện tại
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;

// Chọn các sản phẩm theo thứ tự được thêm vào theo ngày
$stmt = $pdo->prepare('SELECT * FROM products ORDER BY date_added DESC LIMIT ?, ?');

// Dùng hàm bindValue cho phép sử dụng biến trong câu lệnh SQL
$stmt->bindValue(1, ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(2, $num_products_on_each_page, PDO::PARAM_INT);
$stmt->execute();

// Dùng hàm Fetch để trả về danh sách sản phẩm
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy tổng số sản phẩm
$total_products = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
?>

<?= template_header('Products') ?>

    <div class="hero-wrap hero-bread" style="background-image: url('images/bg_1.jpg');">
        <div class="container">
            <div class="row no-gutters slider-text align-items-center justify-content-center">
                <div class="col-md-9 ftco-animate text-center">
                    <p class="breadcrumbs"><span class="mr-2"><a href="index.html">Home</a></span> <span>Products</span></p>
                    <h1 class="mb-0 bread">Products</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 mb-5 text-center">
                    <ul class="product-category">
                        <li><a href="#" class="active">All</a></li>
                        <li><a href="#">Vegetables</a></li>
                        <li><a href="#">Fruits</a></li>
                    </ul>
                </div>
            </div>
            <p>Total <?=$total_products?> Products</p>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-3 ftco-animate">
                        <div class="product">
                            <a href="index.php?page=product&id=<?=$product['id']?>" class="img-prod">
                                <img class="img-fluid" src="imgs/<?=$product['img']?>" alt="<?=$product['title']?>">
                                <span class="status">30%</span>
                                <div class="overlay"></div>
                            </a>
                            <div class="text py-3 pb-4 px-3 text-center">
                                <h3><a href="index.php?page=product&id=<?=$product['id']?>"><?=$product['title']?></a></h3>
                                <div class="d-flex">
                                    <div class="pricing">
                                        <p class="price">
                                            <span class="mr-2 price-dc"><?=$product['price']?></span>
                                            <span class="price-sale">
                                            <?php if ($product['rrp'] > 0): ?>
                                                <span class="rrp">
                                                    &dollar;<?=$product['rrp']?>
                                                </span>
                                            <?php endif; ?>
                                        </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="bottom-area d-flex px-3">
                                    <div class="m-auto d-flex">
                                        <a href="#" class="add-to-cart d-flex justify-content-center align-items-center text-center">
                                            <span><i class="ion-ios-menu"></i></span>
                                        </a>
                                        <a href="#" class="buy-now d-flex justify-content-center align-items-center mx-1">
                                            <span><i class="ion-ios-cart"></i></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="row mt-5">
                <div class="col text-center">
                    <div class="block-27">
                        <ul>
                            <li>
                                <?php if ($current_page > 1): ?>
                                    <a href="index.php?page=products&p=<?=$current_page-1?>">Prev</a>
                                <?php endif; ?>
                            </li>
                            <?php
                            // Tính số trang
                            $total_pages = ceil($total_products / $num_products_on_each_page);
                            for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="<?= ($i == $current_page) ? 'active' : '' ?>">
                                    <a href="index.php?page=products&p=<?=$i?>"><?=$i?></a>
                                </li>
                            <?php endfor; ?>
                            <li>
                                <?php if ($current_page < $total_pages): ?>
                                    <a href="index.php?page=products&p=<?=$current_page+1?>">Next</a>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?= template_footer() ?>