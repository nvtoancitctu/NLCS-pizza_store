<?php
// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /index.php?page=login");
    exit();
}

$productController = new ProductController($conn);
$products = $productController->listProducts(); // Giả sử có phương thức này để lấy danh sách sản phẩm

// Xóa sản phẩm nếu có yêu cầu từ GET
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $productController->deleteProduct($product_id);
    $_SESSION['success'] = "Product $product_id has been deleted successfully!";
    header("Location: /index.php?page=list");
    exit();
}
