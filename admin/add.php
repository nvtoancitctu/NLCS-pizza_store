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

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

        if (in_array(strtolower($file_ext), $allowed)) {
            $image = time() . '_' . $_FILES['image']['name'];
            if (move_uploaded_file($_FILES['image']['tmp_name'], "images/$image")) {
                // Image uploaded successfully
            } else {
                $error = "Failed to upload the image. Please try again.";
            }
        } else {
            $error = "Invalid file format. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    }

    $productController->createProduct($name, $description, $price, $image, $category_id, $discount, $discount_end_time);
    $_SESSION['success'] = "Product has been added successfully!";
    header("Location: /index.php?page=list");
    exit();
}
?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<h1 class="text-4xl font-extrabold text-center my-10 text-blue-700 drop-shadow-lg">Add New Product</h1>

<div class="flex justify-center">
    <div class="w-full max-w-2xl">
        <form action="/index.php?page=add" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Product Name</label>
                <input type="text" name="name" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500" required>
            </div>
            <div class="mb-2">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea name="description" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500" placeholder="Can be left empty"></textarea>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price</label>
                <input type="number" name="price" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500" min="0" max="100" step="0.01" placeholder="Enter price (e.g., 15,50)" required>
            </div>
            <div class="mb-4">
                <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                <select name="category_id" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="discount" class="block text-gray-700 text-sm font-bold mb-2">Discount Price</label>
                <input type="number" name="discount" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500" min="0" max="100" step="0.01" placeholder="Enter discount (e.g., 15,50)">
            </div>
            <!-- Thay đổi theo giờ của máy -->
            <div class="mb-4">
                <label for="discount_end_time" class="block text-gray-700 text-sm font-bold mb-2">Discount End Time (UTC)</label>
                <input type="datetime-local" id="discount_end_time" name="discount_end_time" class="border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:border-blue-500" required>
            </div>

            <script>
                document.getElementById('discount_end_time').addEventListener('change', function() {
                    const localDateTime = new Date(this.value);
                    const utcDateTime = new Date(localDateTime.getTime() + localDateTime.getTimezoneOffset() * 60000);
                    this.value = utcDateTime.toISOString().slice(0, 16);
                });
            </script>

            <div class="mb-4">
                <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Product Image (Optional)</label>
                <div class="flex items-center">
                    <input type="file" name="image" id="image" class="hidden" onchange="updateFileName(this)">
                    <label for="image" class="border rounded w-full py-2 px-3 text-gray-700 cursor-pointer hover:bg-blue-100 transition duration-200 focus:outline-none focus:border-blue-500">
                        <span id="file-name">Choose an image...</span>
                    </label>
                </div>
            </div>

            <script>
                function updateFileName(input) {
                    const fileName = input.files.length > 0 ? input.files[0].name : "Choose an image...";
                    document.getElementById("file-name").innerText = fileName;
                }
            </script>

            <div class="flex justify-center mt-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded">Add Product</button>
            </div>
        </form>
    </div>
</div>