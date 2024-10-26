<?php
require_once '../config.php';
require_once '../controllers/ProductController.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /index.php?page=login");
    exit();
}

$productController = new ProductController($conn);
$product_id = $_GET['id'];

// Xóa sản phẩm
$productController->deleteProduct($product_id);
$_SESSION['success'] = "Product $product_id has been deleted successfully!";
header("Location: /index.php?page=list");
exit();
