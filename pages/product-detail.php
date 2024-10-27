<?php
require_once '../config.php';
require_once '../controllers/ProductController.php';

// Khởi tạo ProductController
$productController = new ProductController($conn);

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? $_GET['id'] : null;
$product = $productController->getProductDetails($product_id);

// Kiểm tra nếu sản phẩm không tồn tại
if (!$product) {
    echo "<h1 class='text-center mt-5'>Product not found</h1>";
    exit();
}
?>

<div class="container my-5">
    <div class="row">
        <!-- Hình ảnh sản phẩm -->
        <div class="col-md-6 flex justify-center items-center">
            <img src="/images/<?php echo htmlspecialchars($product['image']); ?>"
                class="w-3/5 h-auto mx-auto object-cover rounded-lg transition duration-500 ease-in-out transform hover:rotate-12 hover:scale-110"
                alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <!-- Chi tiết sản phẩm -->
        <div class="col-md-6">
            <h1 class="mt-8 text-5xl font-extrabold text-black-700 tracking-wide mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="text-lg text-gray-600 mb-3"><?php echo htmlspecialchars($product['description']); ?></p>

            <!-- Hiển thị giá: Nếu có giảm giá thì hiện giá giảm -->
            <?php if ($product['discount'] > 0): ?>
                <p class="text-gray-600 line-through">Original Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                <strong class="text-2xl font-semibold text-blue-600">Discounted Price:</strong>
                <span class="text-red-600 text-3xl font-bold">$<?php echo htmlspecialchars($product['discount']); ?></span>
            <?php else: ?>
                <strong class="text-2xl font-semibold text-blue-600 mb-4">Price:</strong>
                <span class="text-red-600 text-3xl font-bold">$<?php echo htmlspecialchars($product['price']); ?></span>
            <?php endif; ?>

            <!-- Form thêm vào giỏ hàng -->
            <form method="POST" class="add-to-cart-form" style="display:inline;">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <div class="form-group mb-4">
                    <label for="quantity" class="text-lg font-semibold mt-4">Quantity:</label>
                    <input type="number" class="form-control w-25" id="quantity" name="quantity" value="1" min="1">
                </div>
                <button type="button" class="add-to-cart-button px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-300">Add to Cart</button>
            </form>
        </div>
    </div>

    <!-- Sản phẩm liên quan ngẫu nhiên -->
    <div class="related-products mt-5">
        <h2 class="text-center mt-8 text-4xl font-extrabold text-blue-700 tracking-wide">You May Also Like</h2>
        <div class="row">
            <?php
            $relatedProducts = $productController->getRandomProducts(3); // Lấy 3 sản phẩm ngẫu nhiên
            foreach ($relatedProducts as $relatedProduct):
            ?>
                <div class="col-md-4 col-sm-6 p-4">
                    <div class="card h-full bg-white rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300 ease-in-out">
                        <img class="w-3/5 h-auto mx-auto object-cover rounded-lg transition duration-500 ease-in-out transform hover:rotate-12 hover:scale-110"
                            src="/images/<?php echo htmlspecialchars($relatedProduct['image']); ?>"
                            alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title text-xl font-bold text-gray-800 text-center"><?php echo htmlspecialchars($relatedProduct['name']); ?></h5>

                            <!-- Hiển thị giá: Nếu có giảm giá thì hiện giá giảm -->
                            <?php if ($relatedProduct['discount'] > 0): ?>
                                <p class="text-gray-500 line-through text-center">Original Price: $<?php echo htmlspecialchars($relatedProduct['price']); ?></p>
                                <p class="card-text text-red-600 font-semibold text-center">Discounted Price: $<?php echo htmlspecialchars($relatedProduct['discount']); ?></p>
                            <?php else: ?>
                                <p class="card-text text-red-600 font-semibold text-center">Price: $<?php echo htmlspecialchars($relatedProduct['price']); ?></p>
                            <?php endif; ?>
                            <div class="text-center mt-auto mb-2">
                                <button type="button" class="bg-blue-500 text-white mt-2 px-5 py-2 rounded-lg transition duration-300 hover:bg-blue-600 shadow-lg"
                                    onclick="window.location.href='/index.php?page=product-detail&id=<?php echo $relatedProduct['id']; ?>'">View Details</button>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>