<?php
// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ProductController
$productController = new ProductController($conn);

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? $_GET['id'] : null;
$product = $productController->getProductDetails($product_id);

// Kiểm tra nếu sản phẩm không tồn tại
if (!$product) {
    echo "<h1 class='text-center mt-5'>Product not found</h1>";
    exit();
}

// Kiểm tra token CSRF khi gửi yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        echo "<h1 class='text-center mt-5'>Forbidden: Invalid CSRF token</h1>";
        exit();
    }
}
?>

<!-- Giao diện sản phẩm -->
<div class="container my-5">

    <!-- Chi tiết sản phẩm -->
    <div class="row align-items-center">
        <!-- Hình ảnh sản phẩm -->
        <div class="col-md-6 flex justify-center items-center">
            <img src="/images/<?php echo htmlspecialchars($product['image']); ?>"
                class="w-3/5 h-auto mx-auto object-cover rounded-lg transition duration-500 ease-in-out transform hover:rotate-12 hover:scale-110"
                alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-md-6">
            <h1 class="display-4 fw-bold mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="text-muted mb-4"><?php echo htmlspecialchars($product['description']); ?></p>

            <!-- Giá sản phẩm -->
            <?php
            $currentDateTime = new DateTime();
            $discountEndTime = new DateTime($product['discount_end_time']);
            ?>
            <div class="mb-4">
                <?php if ($product['discount'] > 0 && $discountEndTime >= $currentDateTime): ?>
                    <p class="text-muted text-decoration-line-through">Original Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                    <p class="fs-2 text-danger fw-bold">Discounted Price: $<?php echo htmlspecialchars($product['discount']); ?></p>
                <?php else: ?>
                    <p class="fs-2 text-danger fw-bold">Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                <?php endif; ?>
            </div>

            <!-- Form thêm vào giỏ hàng -->
            <form method="POST" class="add-to-cart-form" style="display:inline;">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <div class="form-group mb-3 mt-3">
                    <label for="quantity" class="text-lg font-semibold text-gray-700">Quantity:</label>
                    <input type="number" class="w-20 text-center border border-gray-300 rounded-lg p-2 mt-1" id="quantity" name="quantity" value="1" min="1">
                </div>
                <button type="button" class="add-to-cart-button bg-blue-500 text-white px-5 py-2 rounded-lg transition duration-300 ease-in-out transform hover:bg-purple-600 hover:shadow-lg hover:-translate-y-1 hover:scale-105">Add to Cart</button>
            </form>
        </div>
    </div>

    <!-- Sản phẩm liên quan -->
    <div class="related-products mt-5">
        <h2 class="text-center mb-4 display-5 font-extrabold text-primary">You May Also Like</h2>
        <div class="row">
            <?php
            $relatedProducts = $productController->getRandomProducts(3);
            foreach ($relatedProducts as $relatedProduct): ?>
                <div class="col-md-4 col-sm-6 p-4">
                    <div class="card h-100 shadow-sm border-0 rounded-lg">
                        <div class="card-body text-center">
                            <img src="/images/<?php echo htmlspecialchars($relatedProduct['image']); ?>"
                                class="card-img-top rounded-lg w-3/5 h-auto mx-auto object-cover transition duration-500 ease-in-out transform hover:rotate-12 hover:scale-110"
                                alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                            <h5 class="card-title mt-3"><?php echo htmlspecialchars($relatedProduct['name']); ?></h5>
                            <p class="text-danger fw-bold">$<?php echo htmlspecialchars($relatedProduct['price']); ?></p>
                            <a href="/product-detail/id=<?php echo $relatedProduct['id']; ?>"
                                class="btn btn-outline-primary mt-2">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>