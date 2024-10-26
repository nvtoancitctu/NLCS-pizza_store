<?php
require_once '../config.php';
require_once '../controllers/ProductController.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: /index.php?page=login");
    exit();
}

$productController = new ProductController($conn);
$categories = $productController->getDistinctCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = !empty($_POST['description']) ? $_POST['description'] : null;
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $discount = !empty($_POST['discount']) ? $_POST['discount'] : null;
    $discount_end_time = !empty($_POST['discount_end_time']) ? $_POST['discount_end_time'] : null;
    $image = !empty($_POST['image']) ? $_POST['image'] : null;

    // Kiểm tra nếu có tệp hình ảnh mới được tải lên
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        // Kiểm tra định dạng file ảnh
        if (in_array(strtolower($file_ext), $allowed)) {
            // Đặt tên duy nhất cho ảnh
            $image = time() . '_' . $_FILES['image']['name'];

            // Tải ảnh lên
            if (move_uploaded_file($_FILES['image']['tmp_name'], "images/$image")) {
                // Hình ảnh mới đã được tải lên
            } else {
                // 
                $error = "Failed to upload the image. Please try again.";
            }
        } else {
            $error = "Invalid file format. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
        // 
    }

    // Gọi hàm thêm sản phẩm mới
    $productController->createProduct($name, $description, $price, $image, $category_id, $discount, $discount_end_time);
    $_SESSION['success'] = "Product has been added successfully!";
    header("Location: /index.php?page=list");
    exit();
}

?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<h1 class="text-center text-2xl font-bold my-6">Add New Product</h1>

<div class="container mx-auto p-4 bg-white shadow-md rounded-lg">
    <form action="/index.php?page=add" method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Product Name</label>
            <input type="text" name="name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
        </div>
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
            <input type="number" name="price" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" min="0" step="0.01" required>
        </div>
        <div class="mb-4">
            <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="discount" class="block text-sm font-medium text-gray-700">Discount (%)</label>
            <input type="number" name="discount" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" min="0" max="100" step="0.01" placeholder="Enter discount (e.g., 15.50)">
        </div>
        <div class="mb-4">
            <label for="discount_end_time" class="block text-sm font-medium text-gray-700">Discount End Time</label>
            <input type="datetime-local" name="discount_end_time" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
        </div>
        <div class="mb-4">
            <label for="image" class="block text-sm font-medium text-gray-700">Product Image (optional)</label>
            <input type="file" name="image" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
        </div>
        <button type="submit" class="mt-4 w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-blue-700 transition duration-200">Add Product</button>
    </form>
</div>