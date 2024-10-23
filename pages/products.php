<?php
require_once '../config.php';
require_once '../controllers/ProductController.php';

// Initialize the Product Controller
$productController = new ProductController($conn);

// Get the user ID if logged in
$user_id = $_SESSION['user_id'] ?? null;

// Fetch all categories
$categories = $productController->getCategories();

// Fetch products based on the selected category (if any)
$category_id = isset($_GET['category_id']) && is_numeric($_GET['category_id']) ? intval($_GET['category_id']) : null;
$products = $productController->listProducts($category_id);
?>

<!-- Page Title -->
<h1 class="text-center mt-8 text-5xl font-extrabold text-blue-700 tracking-wide">Our Delicious Pizza Menu</h1><br />

<!-- Product Categories -->
<div class="text-center mb-6">
    <a href="/index.php?page=products" class="inline-block px-4 py-2 rounded-lg <?= !$category_id ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700' ?> m-2 hover:bg-red-700 transition duration-300">All</a>
    <?php foreach ($categories as $category): ?>
        <a href="/index.php?page=products&category_id=<?= $category['id'] ?>"
            class="inline-block px-4 py-2 rounded-lg <?= ($category_id == $category['id']) ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700' ?> m-2 hover:bg-red-700 transition duration-300">
            <?= htmlspecialchars($category['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Product Listing -->
<div class="container mx-auto px-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="p-4">
                    <div class="bg-white rounded-lg shadow-lg transition-transform transform hover:scale-105">
                        <img class="rounded-t-lg w-3/5 h-auto mx-auto object-contain" src="/images/<?= htmlspecialchars($product['image']) ?>"
                            alt="<?= htmlspecialchars($product['name'] . ' image') ?>">

                        <div class="card-body">
                            <h5 class="card-title text-xl font-bold text-gray-800"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text text-gray-600"><?= htmlspecialchars($product['description']) ?></p>

                            <!-- Hiển thị giá: Nếu có giảm giá thì hiện giá giảm -->
                            <?php if ($product['discount'] > 0): ?>
                                <div>
                                    <p class="text-l font-semibold text-gray-500 line-through">Original Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                                    <p class="text-xl font-semibold text-red-600 mt-2">Discounted Price: $<?php echo htmlspecialchars($product['discount']); ?></p>
                                </div>
                            <?php else: ?>
                                <h3 class="text-xl font-semibold text-red-600 mt-2 mb-2">Price: $<?php echo htmlspecialchars($product['price']); ?></h3>
                            <?php endif; ?>

                            <!-- Hiệu chỉnh 2 nút View Details và Add to Cart -->
                            <div class="mt-4 flex justify-center space-x-6">
                                <a href="/index.php?page=product-detail&id=<?= $product['id'] ?>" class="btn btn-primary">View Details</a>
                                <form method="POST" class="add-to-cart-form" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="button" class="btn btn-warning add-to-cart-button" onclick="addToCart(<?= $product['id'] ?>)">Add to Cart</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class=" text-center text-xl text-gray-700 mt-4">No products found.</p>
        <?php endif; ?>
    </div>
</div>