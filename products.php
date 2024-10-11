<?php
// Số lượng sản phẩm mỗi trang được hiển thị trên page
$num_products_on_each_page = 4;

// Trang hiện tại
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;
$category = isset($_GET['c']) ? $_GET['c'] : null;
$price_range = isset($_GET['price_range']) ? $_GET['price_range'] : 'all';

switch ($price_range) {
    case '0-50':
        $min_price = 0;
        $max_price = 50;
        break;
    case '50-100':
        $min_price = 50;
        $max_price = 100;
        break;
    case '100-200':
        $min_price = 100;
        $max_price = 200;
        break;
    case '+200':
        $min_price = 200;
        $max_price = PHP_INT_MAX; // Sử dụng PHP_INT_MAX để đảm bảo lấy tất cả sản phẩm có giá trên 200
        break;
    default:
        $min_price = 0;
        $max_price = PHP_INT_MAX;
        break;
}

// Chuẩn bị câu lệnh SQL
if ($category) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE category = ? AND price BETWEEN ? AND ? ORDER BY date_added DESC LIMIT ?, ?');
    $stmt->bindValue(1, $category, PDO::PARAM_STR);
    $stmt->bindValue(2, $min_price, PDO::PARAM_INT);
    $stmt->bindValue(3, $max_price, PDO::PARAM_INT);
    $stmt->bindValue(4, ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
    $stmt->bindValue(5, $num_products_on_each_page, PDO::PARAM_INT);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);https://maps.g

    $total_products_stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category = ? AND price BETWEEN ? AND ?');
    $total_products_stmt->bindValue(1, $category, PDO::PARAM_STR);
    $total_products_stmt->bindValue(2, $min_price, PDO::PARAM_INT);
    $total_products_stmt->bindValue(3, $max_price, PDO::PARAM_INT);
    $total_products_stmt->execute();
    $total_products = $total_products_stmt->fetchColumn();
} else {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE price BETWEEN ? AND ? ORDER BY date_added DESC LIMIT ?, ?');
    $stmt->bindValue(1, $min_price, PDO::PARAM_INT);
    $stmt->bindValue(2, $max_price, PDO::PARAM_INT);
    $stmt->bindValue(3, ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
    $stmt->bindValue(4, $num_products_on_each_page, PDO::PARAM_INT);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_products_stmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE price BETWEEN ? AND ?');
    $total_products_stmt->bindValue(1, $min_price, PDO::PARAM_INT);
    $total_products_stmt->bindValue(2, $max_price, PDO::PARAM_INT);
    $total_products_stmt->execute();
    $total_products = $total_products_stmt->fetchColumn();
}
?>

<?php template_header('') ?>

    <div class="hero-wrap hero-bread" style="background-image: url('imgs/bg_1.jpg');">
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
                        <li><a href="index.php?page=products&s=all" class="<?= isset($_GET['s']) && $_GET['s'] == 'all' ? 'active' : '' ?>">All</a></li>
                        <li><a href="index.php?page=products&c=noodles&s=n" class="<?= isset($_GET['s']) && $_GET['s'] == 'n' ? 'active' : '' ?>">Noodles</a></li>
                        <li><a href="index.php?page=products&c=fruits&s=f" class="<?= isset($_GET['s']) && $_GET['s'] == 'f' ? 'active' : '' ?>">Fruits</a></li>
                        <li><a href="index.php?page=products&c=other&s=o" class="<?= isset($_GET['s']) && $_GET['s'] == 'o' ? 'active' : '' ?>">Other</a></li>
                    </ul>
                </div>
            </div>
            <div class="row justify-content-center mb-4">
                <div class="col-md-10 text-center">
                    <a href="index.php?page=products&price_range=0-50" class="btn btn-outline-primary <?= $price_range == '0-50' ? 'active' : '' ?>">0 - 50</a>
                    <a href="index.php?page=products&price_range=50-100" class="btn btn-outline-primary <?= $price_range == '50-100' ? 'active' : '' ?>">50 - 100</a>
                    <a href="index.php?page=products&price_range=100-200" class="btn btn-outline-primary <?= $price_range == '100-200' ? 'active' : '' ?>">100 - 200</a>
                    <a href="index.php?page=products&price_range=+200" class="btn btn-outline-primary <?= $price_range == '+200' ? 'active' : '' ?>">200+</a>
                </div>
            </div>
            <p>Total <?=$total_products?> Products</p>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-3 ftco-animate">
                        <div class="product">
                            <a href="index.php?page=product&id=<?=$product['id']?>" class="img-prod">
                                <img class="img-fluid" src="imgs/<?=$product['img']?>" alt="<?=$product['title']?>">
                            </a>
                            <div class="text py-3 pb-4 px-3 text-center">
                                <h3><a href="index.php?page=product&id=<?=$product['id']?>"><?=$product['title']?></a></h3>
                                <div class="d-flex">
                                    <div class="pricing">
                                    <span class="mr-2 price-dc" style="color: #82ae46">
                                        <?=$product['price']?>.000 VND
                                    </span>
                                    </div>
                                </div>
                                <div class="bottom-area d-flex px-3">
                                    <div class="m-auto d-flex">
                                        <a href="index.php?page=product&id=<?=$product['id']?>" class="add-to-cart d-flex justify-content-center align-items-center text-center">
                                            <span><i class="ion-ios-menu"></i></span>
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
                                    <a href="index.php?page=products&p=<?=$current_page-1?><?= $category ? '&c=' . urlencode($category) : '' ?>&price_range=<?=$price_range?>">Prev</a>
                                <?php endif; ?>
                            </li>
                            <?php
                            $total_pages = ceil($total_products / $num_products_on_each_page);
                            for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="<?= ($i == $current_page) ? 'active' : '' ?>">
                                    <a href="index.php?page=products&p=<?=$i?><?= $category ? '&c=' . urlencode($category) : '' ?>&price_range=<?=$price_range?>"><?=$i?></a>
                                </li>
                            <?php endfor; ?>
                            <li>
                                <?php if ($current_page < $total_pages): ?>
                                    <a href="index.php?page=products&p=<?=$current_page+1?><?= $category ? '&c=' . urlencode($category) : '' ?>&price_range=<?=$price_range?>">Next</a>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?= template_footer() ?>