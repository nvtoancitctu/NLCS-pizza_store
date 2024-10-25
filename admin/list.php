<?php
require_once '../config.php';
require_once '../controllers/ProductController.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /index.php?page=login");
    exit();
}

$productController = new ProductController($conn);

// Lấy danh sách sản phẩm
$products = $productController->listProducts();
?>

<h1 class="text-3xl font-bold text-center mb-6">Product Management</h1>

<div class="container mx-auto p-6 bg-white shadow-lg rounded-lg w-4/5">
    <!-- Add New Product Button -->
    <a href="/index.php?page=add" class="inline-block mb-4 px-6 py-2 bg-green-500 text-white font-semibold rounded hover:bg-green-600 transition duration-200">Add New Product</a>

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