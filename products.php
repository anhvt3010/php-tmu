<?php
// Số lượng sản phẩm mỗi trang được hiển thị trên page 

$num_products_on_each_page = 4;

// Trang hiện tại 

$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;

// Chọn các sản phẩm theo thứ tự được thêm vào theo ngày 

$stmt = $pdo->prepare('SELECT * FROM products ORDER BY date_added DESC LIMIT ?,?');

//Dùng hàm bindValue cho phép will allow us to use an integer in the SQL statement, which we need to use for the LIMIT clause

$stmt->bindValue(1, ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(2, $num_products_on_each_page, PDO::PARAM_INT);
$stmt->execute();

// Dùng hàm Fetch để trả về danh sách sản phẩm 

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy tổng số sản phẩm products

$total_products = $pdo->query('SELECT * FROM products')->rowCount();
?>

<?=template_header('Products')?>

<div class="products content-wrapper">
    <h1>Products</h1>
    <p><?=$total_products?> Products</p>
	
    <div class="products-wrapper">
	
        <?php foreach ($products as $product): ?>
        <a href="index.php?page=product&id=<?=$product['id']?>" class="product">
           
		   <img src="imgs/<?=$product['img']?>" width="200" height="200" alt="<?=$product['title']?>">
            
			<span class="name">
				<?=$product['title']?>
			</span>
            <span class="price">
                &dollar;<?=$product['price']?>
				
                <?php if ($product['rrp'] > 0): ?>
                <span class="rrp">
					&dollar;<?=$product['rrp']?>
				</span>
                <?php endif; ?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>
    <div class="buttons">
        <?php if ($current_page > 1): ?>
        <a href="index.php?page=products&p=<?=$current_page-1?>">Prev</a>
		<?php endif; ?>
		
        <?php if ($total_products > ($current_page * $num_products_on_each_page) - $num_products_on_each_page + count($products)): ?>
        <a href="index.php?page=products&p=<?=$current_page+1?>">Next</a>
        <?php endif; ?>
    </div>
</div>

<?=template_footer()?>