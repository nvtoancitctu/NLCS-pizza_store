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

    $discount = !empty($_POST['discount']) ? $_POST['discount'] : null;
    $discount_end_time = $_POST['discount_end_time'] ?? null;

    $currentProduct = $productController->getProductDetails($product_id);
    $image = $currentProduct['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($file_ext), $allowed)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], "images/$image")) {
                $productController->updateProduct($name, $description, $price, $image, $category_id, $discount, $discount_end_time, $product['id']);
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
        $productController->updateProduct($name, $description, $price, $image, $category_id, $discount, $discount_end_time, $product['id']);
        $_SESSION['success'] = "Product $product_id has been updated successfully!";
        header("Location: /index.php?page=list");
        exit();
    }
}
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<h1 class="text-4xl font-extrabold text-center my-10 text-blue-700 drop-shadow-lg">Edit Product</h1>

<div class="flex justify-center">
    <div class="w-full max-w-2xl">
        <form action="/index.php?page=edit&id=<?= htmlspecialchars($product['id']) ?>" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Product Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea name="description" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500" placeholder="Can be left empty"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price</label>
                <input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500" min="0" max="100" step="0.01" placeholder="Enter price (e.g., 15.50)" required>
            </div>
            <div class="mb-4">
                <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                <select name="category_id" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']) ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="discount" class="block text-gray-700 text-sm font-bold mb-2">Discount (%)</label>
                <input type="number" name="discount" value="<?= htmlspecialchars($product['discount']) ?>" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500" min="0" max="100" step="0.01" placeholder="Enter discount (e.g., 15.50)">
            </div>
            <div class="mb-4">
                <label for="discount_end_time" class="block text-gray-700 text-sm font-bold mb-2">Discount End Time (UTC)</label>
                <input type="datetime-local" id="discount_end_time" name="discount_end_time" value="<?= htmlspecialchars($product['discount_end_time']) ?>" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Product Image (Optional)</label>
                <div class="flex items-center">
                    <input type="file" name="image" id="image" class="hidden" onchange="updateFileName(this)">
                    <label for="image" class="border rounded w-full py-2 px-3 text-gray-700 cursor-pointer hover:bg-blue-100 transition duration-200 focus:outline-none focus:border-blue-500">
                        <span id="file-name">Choose an image...</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-center">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded">Update Product</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateFileName(input) {
        const fileName = input.files.length > 0 ? input.files[0].name : "Choose an image...";
        document.getElementById("file-name").innerText = fileName;
    }
</script>