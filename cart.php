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
    // Lấy thông tin từ biểu mẫu
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';

    // Kiểm tra và xác thực thông tin
    if (empty($fullname) || empty($phone) || empty($address)) {
        // Nếu có trường nào đó trống, hiển thị thông báo lỗi
        echo "<script>alert('Please fill in the shipping information completely.'); window.location.href='index.php?page=cart';</script>";
        exit;
    }

    // Tính toán tổng tiền (giả sử phí giao hàng là 30.000 VND)
    $subtotal = 0.00;
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        // Giả sử bạn đã có một hàm để lấy giá sản phẩm từ CSDL
        $stmt = $pdo->prepare('SELECT price FROM products WHERE id = ?');
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $subtotal += $product['price'] * $quantity;
        }
    }
    $total = $subtotal + 30; // Thêm phí giao hàng

    // Thêm thông tin đơn hàng vào bảng orders
    $stmt = $pdo->prepare('INSERT INTO orders (customer_name, customer_phone, customer_address, status) VALUES (?, ?, ?, ?)');
    $stmt->execute([$fullname, $phone, $address, 0]); // 0 là trạng thái mặc định

    // Lấy ID của đơn hàng vừa tạo
    $order_id = $pdo->lastInsertId();

    // Thêm thông tin chi tiết đơn hàng vào bảng order_detail
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $pdo->prepare('INSERT INTO order_detail (order_id, product_id, quantity) VALUES (?, ?, ?)');
        $stmt->execute([$order_id, $product_id, $quantity]);
    }

    // Ghi lại thông tin đơn hàng vào mảng
    $order_info = [
        'fullname' => $fullname,
        'phone' => $phone,
        'address' => $address,
        'cart' => $_SESSION['cart'],
        'subtotal' => $subtotal,
        'total' => $total
    ];

    // Chuyển hướng đến trang đặt hàng
    header('Location: index.php?page=placeorder&order_info=' . urlencode(serialize($order_info)));
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
$total = $subtotal + 30
?>

<?= template_header('Cart') ?>

<section class="ftco-section ftco-cart">
    <div class="container">
        <form action="index.php?page=cart" method="post">
            <div class="row">
                <div class="col-md-12 ftco-animate">
                    <div class="cart-list">
                        <table class="table">
                            <thead class="thead-primary">
                            <tr class="text-center">
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>Product name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="5" style="text-align:center;">You have no products added in your
                                        Shopping Cart
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr class="text-center">
                                        <td class="product-remove">
                                            <a href="index.php?page=cart&remove=<?= $product['id'] ?>">
                                                <span class="ion-ios-close"></span>
                                            </a>
                                        </td>

                                        <td class="image-prod">
                                            <a href="index.php?page=product&id=<?= $product['id'] ?>">
                                                <img src="imgs/<?= $product['img'] ?>" width="150" height="150" alt="<?= $product['title'] ?>">
                                            </a>
                                        </td>
                                        <td class="product-name">
                                            <a href="index.php?page=product&id=<?= $product['id'] ?>"><?= $product['title'] ?></a>
                                        </td>
                                        <td class="price"><?= $product['price'] ?>.000 VND</td>

                                        <td class="quantity">
                                            <div class="input-group mb-3">
                                                <span class="input-group-btn mr-2">
                                                    <button type="button" class="quantity-left-minus btn" data-type="minus" data-field="<?= $product['id'] ?>">
                                                        <i class="ion-ios-remove"></i>
                                                    </button>
                                                </span>
                                                <input type="number" id="quantity-<?= $product['id'] ?>" class="quantity form-control input-number"
                                                       name="quantity-<?= $product['id'] ?>"
                                                       value="<?= $products_in_cart[$product['id']] ?>" min="1"
                                                       max="<?= $product['quantity'] ?>"
                                                       placeholder="Quantity" required>
                                                <span class="input-group-btn ml-2">
                                                    <button type="button" class="quantity-right-plus btn" data-type="plus" data-field="<?= $product['id'] ?>">
                                                        <i class="ion-ios-add"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </td>

                                        <td class="total">
                                            <?php
                                            $total_price = $product['price'] * $products_in_cart[$product['id']];
                                            if ($total_price > 1000) {
                                                echo number_format($total_price, 0, ',', '.') . ".000 VND";
                                            } else {
                                                echo $total_price . ".000 VND";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php if (!empty($products)): // Kiểm tra nếu có sản phẩm trong giỏ ?>
                <div class="row justify-content-end">
                    <div class="col-lg-6 mt-5 cart-wrap ftco-animate">
                        <div class="cart-total mb-3">
                            <h3>Delivery Information</h3>
                            <div class="form-group row">
                                <label for="name" class="col-sm-2 col-form-label">Fullname: </label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" name="fullname" placeholder="enter your name..." >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="phone" class="col-sm-2 col-form-label">Phone: </label>
                                <div class="col-sm-10">
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="enter your phone..." >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address">Address delivery</label>
                                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 mt-5 cart-wrap ftco-animate">
                        <div class="cart-total mb-3">
                            <h3>Cart Totals</h3>
                            <p class="d-flex">
                                <span>Subtotal</span>
                                <span>
                                    <?php
                                    echo number_format($subtotal, 0, ',', '.') . ".000 VND";
                                    ?>
                                </span>
                            </p>
                            <p class="d-flex">
                                <span>Delivery</span>
                                <span>30.000 VND</span>
                            </p>
                            <hr>
                            <p class="d-flex total-price">
                                <span>Total</span>
                                <span>
                                    <?php
                                    echo number_format($total, 0, ',', '.') . ".000 VND";
                                    ?>
                                </span>
                            </p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <input class="btn btn-secondary py-3 px-4" type="submit" value="Update" name="update">
                            <input class="btn btn-primary py-3 px-4" type="submit" value="Place Order" name="placeorder">
                        </div>
                    </div>
                </div>
            <?php endif; // Kết thúc kiểm tra sản phẩm ?>
        </form>
    </div>
</section>
<?= template_footer() ?>
<script>
    $(document).ready(function(){
        $('.quantity-right-plus').click(function(e){
            e.preventDefault();
            var productId = $(this).data('field'); // Lấy ID sản phẩm từ data-field
            var quantityInput = $('#quantity-' + productId); // Lấy trường số lượng tương ứng
            var quantity = parseInt(quantityInput.val());
            var max = parseInt(quantityInput.attr('max'));
            if(quantity < max){
                quantityInput.val(quantity + 1);
            }
        });

        $('.quantity-left-minus').click(function(e){
            e.preventDefault();
            var productId = $(this).data('field'); // Lấy ID sản phẩm từ data-field
            var quantityInput = $('#quantity-' + productId); // Lấy trường số lượng tương ứng
            var quantity = parseInt(quantityInput.val());
            if(quantity > 1){
                quantityInput.val(quantity - 1);
            }
        });
    });
</script>

