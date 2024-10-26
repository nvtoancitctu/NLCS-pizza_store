<?php
require_once '../config.php';
require_once '../controllers/ProductController.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /index.php?page=login");
    exit();
}

$productController = new ProductController($conn);

// Khởi tạo biến
$searchTerm = '';
$products = $productController->listProducts(); // Mặc định lấy danh sách tất cả sản phẩm

// Xử lý tìm kiếm sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchTerm = isset($_POST['search_term']) ? trim($_POST['search_term']) : '';

    // Nếu từ khóa tìm kiếm không trống, gọi hàm searchProducts
    if (!empty($searchTerm)) {
        $products = $productController->searchProducts($searchTerm);
    }
}
?>

<h1 class="text-4xl font-extrabold text-center my-10 text-blue-700 drop-shadow-lg">Product Management</h1>
<div class="container mx-auto p-6 bg-white shadow-xl rounded-lg w-4/5 mb-4">
    <div class="row mb-2">
        <div class="col-md-4">
            <!-- Add New Product Button -->
            <button type="button"
                class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold px-4 py-2 rounded-lg text-sm transition duration-300 transform hover:-translate-y-1 hover:scale-105 shadow-lg"
                onclick="window.location.href='/index.php?page=add'">+ New Product
            </button>
        </div>
        <div class="col-md-8 text-end">
            <!-- Search Form -->
            <form method="POST" class="mb-3">
                <div class="input-group" style="width: auto;">
                    <input type="text" name="search_term" class="form-control w-auto" placeholder="Search products..." value="<?= htmlspecialchars($searchTerm ?? '') ?>" aria-label="Search products" aria-describedby="button-search">
                    <button class="btn btn-primary" type="submit" name="search" id="button-search">Search</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Product Table -->
    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
        <thead>
            <tr class="bg-gray-100 text-gray-800 text-center">
                <th class="px-4 py-2 border-b">ID</th>
                <th class="px-4 py-2 border-b">Image</th>
                <th class="px-4 py-2 border-b">Name</th>
                <th class="px-4 py-2 border-b">Price</th>
                <th class="px-4 py-2 border-b">Description</th>
                <th class="px-4 py-2 border-b">Discount</th>
                <th class="px-4 py-2 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr class="hover:bg-gray-50">
                    <!-- Product ID -->
                    <td class="px-4 py-2 border-b text-center"><?= htmlspecialchars($product['id']) ?></td>

                    <!-- Product Image -->
                    <td class="px-4 py-2 border-b text-center">
                        <img src="/images/<?= htmlspecialchars($product['image']); ?>"
                            class="w-16 h-16 object-cover mx-auto rounded-lg"
                            alt="<?= htmlspecialchars($product['name']); ?>">
                    </td>

                    <!-- Product Name -->
                    <td class="px-4 py-2 border-b font-semibold text-gray-800"><?= htmlspecialchars($product['name']) ?></td>

                    <!-- Product Price -->
                    <td class="px-4 py-2 border-b text-green-600 font-bold text-center">
                        $<?= number_format($product['price'], 2) ?>
                    </td>

                    <!-- Product Description (shortened to 50 characters) -->
                    <td class="px-4 py-2 border-b text-gray-600">
                        <?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...
                    </td>

                    <!-- Discount Info -->
                    <td class="px-4 py-2 border-b text-red-500 font-bold text-center">
                        <?php if (!empty($product['discount'])): ?>
                            $<?= number_format($product['discount'], 2) ?>
                        <?php else: ?>
                            <span class="text-gray-500">No Discount</span>
                        <?php endif; ?>
                    </td>

                    <!-- Actions: Edit and Delete -->
                    <td class="px-4 py-2 border-b text-center">
                        <div class="flex justify-center space-x-2">
                            <a href="/index.php?page=edit&id=<?= $product['id'] ?>"
                                class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition duration-200">
                                Edit
                            </a>
                            <a href="/index.php?page=delete&id=<?= $product['id'] ?>"
                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition duration-200">
                                Delete
                            </a>
                        </div>
                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>
</div>