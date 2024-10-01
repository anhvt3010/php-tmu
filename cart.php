<?php
// Nếu người dùng click vào nút thêm vào Giỏ hàng thì bắt đầu thực hiện

if (isset($_POST['product_id'], $_POST['quantity']) && is_numeric($_POST['product_id']) && is_numeric($_POST['quantity'])) {
    
	// Đặt biến post để xác định mã sản phẩm nhanh và dễ hơn 
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
	
    // Chuẩn bị lấy dữ liệu từ mySQL và kiểm tra xem sản phẩm đó có trong CSDL chưa?
	
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_POST['product_id']]);
	
    // Dùng hàm Fetch trả về sản phẩm 
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
	
    // Kiểm tra sản phẩm trên hệ thống
    if ($product && $quantity > 0) {
		
        // Nếu Product có trong CSDL thì hệ thống có thể tạo mới (nếu chưa tạo) hoặc cập nhật vào Giỏ hàng
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            if (array_key_exists($product_id, $_SESSION['cart'])) {
				
                // Nếu sản phẩm đã có trong Giỏ hàng thì cập nhật số lượng
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
				
                // Nếu sản phẩm không có trong Giỏ hàng thì thêm vào Giỏ 
                $_SESSION['cart'][$product_id] = $quantity;
            }
        } else {
			
            // Nếu không có sản phẩm nào trong Giỏ thì thêm sản phẩm đầu tiên vào Giỏ hàng
            $_SESSION['cart'] = array($product_id => $quantity);
        }
    }
	
    // Tránh submit nhiều lần ...
    header('location: index.php?page=cart');
    exit;
}

//Đưa sản phẩm ra khỏi Giỏ hàng, kiểm tra URL với chức năng "remove", thông qua mã id của sản phẩm
if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart']) && isset($_SESSION['cart'][$_GET['remove']])) {
    
	// Xóa sản phẩm khỏi Giỏ hàng
    unset($_SESSION['cart'][$_GET['remove']]);
}

// Cập nhật số lượng sản phẩm Update product quantities in cart if the user clicks the "Update" button on the shopping cart page
if (isset($_POST['update']) && isset($_SESSION['cart'])) {
    // Loop through the post data so we can update the quantities for every product in cart
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'quantity') !== false && is_numeric($v)) {
            $id = str_replace('quantity-', '', $k);
            $quantity = (int)$v;
            // Always do checks and validation
            if (is_numeric($id) && isset($_SESSION['cart'][$id]) && $quantity > 0) {
                // Update new quantity
                $_SESSION['cart'][$id] = $quantity;
            }
        }
    }
    // Prevent form resubmission...
    header('Location: index.php?page=cart');
    exit;
}

// Send the user to the place order page if they click the Place Order button, also the cart should not be empty
if (isset($_POST['placeorder']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    header('Location: index.php?page=placeorder');
    exit;
}

// Check the session variable for products in cart
$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$products = array();
$subtotal = 0.00;
// If there are products in cart
if ($products_in_cart) {
    // There are products in the cart so we need to select those products from the database
    // Products in cart array to question mark string array, we need the SQL statement to include IN (?,?,?,...etc)
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id IN (' . $array_to_question_marks . ')');
    // We only need the array keys, not the values, the keys are the id's of the products
    $stmt->execute(array_keys($products_in_cart));
    // Fetch the products from the database and return the result as an Array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Calculate the subtotal
    foreach ($products as $product) {
        $subtotal += (float)$product['price'] * (int)$products_in_cart[$product['id']];
    }
}
?>

<?=template_header('Cart')?>

<div class="cart content-wrapper">
    <h1>Shopping Cart</h1>
    <form action="index.php?page=cart" method="post">
        <table>
            <thead>
                <tr>
                    <td colspan="2">Product</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td>Total</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">You have no products added in your Shopping Cart</td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="img">
                        <a href="index.php?page=product&id=<?=$product['id']?>">
                            <img src="imgs/<?=$product['img']?>" width="50" height="50" alt="<?=$product['title']?>">
                        </a>
                    </td>
                    <td>
                        <a href="index.php?page=product&id=<?=$product['id']?>"><?=$product['title']?></a>
                        <br>
                        <a href="index.php?page=cart&remove=<?=$product['id']?>" class="remove">Remove</a>
                    </td>
                    <td class="price">&dollar;<?=$product['price']?></td>
                    <td class="quantity">
                        <input type="number" name="quantity-<?=$product['id']?>" value="<?=$products_in_cart[$product['id']]?>" min="1" max="<?=$product['quantity']?>" placeholder="Quantity" required>
                    </td>
                    <td class="price">&dollar;<?=$product['price'] * $products_in_cart[$product['id']]?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="subtotal">
            <span class="text">Subtotal</span>
            <span class="price">&dollar;<?=$subtotal?></span>
        </div>
        <div class="buttons">
            <input type="submit" value="Update" name="update">
            <input type="submit" value="Place Order" name="placeorder">
        </div>
    </form>
</div>

<?=template_footer()?>

