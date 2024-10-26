<?php
require_once '../config.php';
require_once '../controllers/ProductController.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /index.php?page=login");
    exit();
}

$productController = new ProductController($conn);
$categories = $productController->getDistinctCategories();
$product_id = $_GET['id'];
$product = $productController->getProductDetails($product_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    // Kiểm tra discount và gán NULL nếu không có giá trị
    $discount = !empty($_POST['discount']) ? $_POST['discount'] : null;
    $discount_end_time = $_POST['discount_end_time'] ?? null;

    // Lấy thông tin sản phẩm hiện tại để giữ lại hình ảnh nếu không có hình ảnh mới
    $currentProduct = $productController->getProductDetails($product_id);
    $image = $currentProduct['image']; // Giữ lại hình ảnh hiện tại

    // Kiểm tra nếu có tệp hình ảnh mới được tải lên
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        // Check image file format
        if (in_array(strtolower($file_ext), $allowed)) {
            // Attempt to upload the image to the images folder
            if (move_uploaded_file($_FILES['image']['tmp_name'], "images/$image")) {
                // Cập nhật sản phẩm với thông tin giảm giá
                $productController->updateProduct($name, $description, $price, $image, $category_id, $discount, $discount_end_time, $product['id']); // Thêm $product['id']
                $_SESSION['success'] = "Product $product_id has been updated successfully!";
                header("Location: /index.php?page=list");
                exit();
            } else {
                $error = "Failed to upload the image. Please try again.";
            }
        } else {
            $error = "Invalid file format. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    } else {
        // Nếu không tải hình ảnh mới, chỉ cập nhật thông tin sản phẩm
        $productController->updateProduct($name, $description, $price, $image, $category_id, $discount, $discount_end_time, $product['id']); // Thêm $product['id']
        $_SESSION['success'] = "Product $product_id has been updated successfully!";
        header("Location: /index.php?page=list");
        exit();
    }
}

?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<h1 class="text-center">Edit Product</h1>

<div class="container">
    <form action="/index.php?page=edit&id=<?= htmlspecialchars($product['id']) ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Product Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" required><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" class="form-control" min="0" max="100" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select name="category_id" class="form-control" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>" <?= $product['category_id'] == $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="discount">Discount (%)</label>
            <input type="number" name="discount" class="form-control" min="0" max="100" step="0.01" value="<?= htmlspecialchars($product['discount']) ?>" placeholder="Enter discount (e.g., 15.50)">
        </div>
        <div class="form-group">
            <label for="discount_end_time">Discount End Time</label>
            <input type="datetime-local" name="discount_end_time" class="form-control" value="<?= htmlspecialchars($product['discount_end_time']) ?>">
        </div>
        <div class="form-group">
            <label for="image">Product Image</label>
            <input type="file" name="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>