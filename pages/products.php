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
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$products = $productController->listProducts($category_id);
?>

<!-- Page Title -->
<h1 class="text-center mt-4 text-4xl font-bold text-gray-800 tracking-wider">Our Pizza Menu</h1></br>

<!-- Product Categories -->
<div class="text-center mb-4">
    <a href="/index.php?page=products" class="btn <?= !$category_id ? 'btn-primary' : 'btn-outline-primary' ?> m-1">All</a>
    <?php foreach ($categories as $category): ?>
        <a href="/index.php?page=products&category_id=<?= $category['id'] ?>"
            class="btn <?= ($category_id == $category['id']) ? 'btn-primary' : 'btn-outline-primary' ?> m-1">
            <?= htmlspecialchars($category['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Product Listing -->
<div class="container mx-auto">
    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 col-sm-6 p-4">
                    <div class="card h-full bg-white rounded-lg shadow-lg transform hover:scale-105 transition-transform duration-300 ease-in-out">
                        <img class="card-img-top mx-auto" style="width: 80%; height: auto;" src="/images/<?= htmlspecialchars($product['image']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>">

                        <div class="card-body">
                            <h5 class="card-title text-xl font-bold text-gray-800"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text text-gray-600"><?= htmlspecialchars($product['description']) ?></p>
                            <p class="card-text text-danger text-lg font-semibold">$<?= htmlspecialchars($product['discount']) ?></p>
                            <a href="/index.php?page=product-detail&id=<?= $product['id'] ?>" class="btn btn-primary mt-2">View Details</a>

                            <form method="POST" class="add-to-cart-form" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="button" class="btn btn-warning mt-2 add-to-cart-button">Add to Cart</button>
                            </form>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-xl text-gray-700">No products found.</p>
        <?php endif; ?>
    </div>
</div>