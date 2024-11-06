<?php
// Kiểm tra quyền admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /index.php?page=login");
    exit();
}

// Kiểm tra và lấy thông báo thành công từ session
$success = '';
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']); // Xóa thông báo khỏi session
}

$productController = new ProductController($conn);

$searchTerm = '';
$products = $productController->listProducts(); // Mặc định lấy danh sách tất cả sản phẩm

// Xử lý tìm kiếm sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchTerm = isset($_POST['search_term']) ? trim($_POST['search_term']) : '';
    if (!empty($searchTerm)) {
        $products = $productController->searchProducts($searchTerm);
    }
}
?>

<h1 class="text-4xl font-extrabold text-center my-10 text-blue-700 drop-shadow-lg">Product Management</h1>
<div class="container mx-auto p-6 bg-white shadow-xl rounded-lg mb-4 w-11/12">
    <div class="row mb-4 d-flex align-items-center">
        <!-- Phần nút thêm sản phẩm và xuất file -->
        <div class="col-md-6 d-flex align-items-center justify-content-start">
            <button type="button"
                class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold px-6 py-2 rounded-lg text-sm transition duration-300 transform hover:-translate-y-1 hover:scale-105 shadow-lg"
                onclick="window.location.href='/index.php?page=add'">+ New Product
            </button>
            <button type="button"
                class="ml-8 inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-6 py-2 rounded-lg text-sm transition duration-300 transform hover:-translate-y-1 hover:scale-105 shadow-lg ml-2"
                onclick="window.location.href='/index.php?page=export'">Export to Excel
            </button>
        </div>

        <!-- Phần tìm kiếm -->
        <div class="col-md-6 d-flex justify-content-end align-items-center">
            <form method="POST" class="mb-0 w-100">
                <div class="input-group w-100">
                    <input type="text" name="search_term" class="form-control" placeholder="Search products..."
                        value="<?= htmlspecialchars($searchTerm ?? '') ?>" aria-label="Search products"
                        aria-describedby="button-search">
                    <button class="btn btn-primary" type="submit" name="search" id="button-search">Search</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

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
                    <td class="px-4 py-2 border-b text-center"><?= htmlspecialchars($product['id']) ?></td>
                    <td class="px-4 py-2 border-b text-center">
                        <img src="/images/<?= htmlspecialchars($product['image']); ?>"
                            class="w-16 h-16 object-cover mx-auto rounded-lg"
                            alt="<?= htmlspecialchars($product['name']); ?>">
                    </td>
                    <td class="px-4 py-2 border-b font-semibold text-gray-800"><?= htmlspecialchars($product['name']) ?></td>
                    <td class="px-4 py-2 border-b text-green-600 font-bold text-center">
                        $<?= number_format($product['price'], 2) ?>
                    </td>
                    <td class="px-4 py-2 border-b text-gray-600">
                        <?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...
                    </td>
                    <td class="px-4 py-2 border-b text-red-500 font-bold text-center">
                        <?php if (!empty($product['discount']) && $product['discount'] > 0): ?>
                            $<?= number_format($product['discount'], 2) ?>
                        <?php else: ?>
                            <span class="text-gray-500">No Discount</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-2 border-b text-center">
                        <div class="flex justify-center space-x-2">
                            <a href="/index.php?page=edit&id=<?= $product['id'] ?>"
                                class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition duration-200">
                                Edit
                            </a>
                            <a href="javascript:void(0);" onclick="confirmDelete(<?= $product['id'] ?>)"
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

<script>
    function confirmDelete(productId) {
        const confirmDelete = confirm("Are you sure you want to delete this product?");
        if (confirmDelete) {
            window.location.href = `/index.php?page=delete&id=${productId}`;
        }
    }
</script>