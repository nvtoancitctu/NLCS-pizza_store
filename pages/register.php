<?php
require_once '../config.php';
require_once '../controllers/UserController.php';

$user = new UserController($conn);

// Khởi tạo biến để lưu thông báo lỗi và thành công
$error = '';
$success = '';

// Kiểm tra xem form đã được gửi hay chưa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Gọi hàm đăng ký và lưu kết quả
    $result = $user->register($name, $email, $password);

    // Kiểm tra xem có thông báo thành công hay lỗi không
    if (strpos($result, 'successful') !== false) {
        $_SESSION['success'] = "Registration successful! You can now log in.";
        header("Location: /index.php?page=login");
        exit();
    } else {
        $error = $result;
    }
}

?>

<h1 class="text-center text-4xl font-bold mt-10 text-gray-900">Register</h1>

<div class="container mx-auto max-w-md p-8 bg-white shadow-lg rounded-xl mt-8 mb-8">
    <form method="POST" action="/index.php?page=register">
        <div class="mb-6">
            <label for="name" class="block text-gray-700 font-bold mb-2">Name:</label>
            <input type="text" name="name" class="shadow-sm appearance-none border border-gray-300 rounded-md w-full p-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-150" required>
        </div>
        <div class="mb-6">
            <label for="email" class="block text-gray-700 font-bold mb-2">Email:</label>
            <input type="email" name="email" class="shadow-sm appearance-none border border-gray-300 rounded-md w-full p-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-150" required>
        </div>
        <div class="mb-6">
            <label for="password" class="block text-gray-700 font-bold mb-2">Password:</label>
            <input type="password" name="password" class="shadow-sm appearance-none border border-gray-300 rounded-md w-full p-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-150" required>
        </div>
        <div class="mb-6">
            <label for="confirm_password" class="block text-gray-700 font-bold mb-2">Confirm Password:</label>
            <input type="password" name="confirm_password" class="shadow-sm appearance-none border border-gray-300 rounded-md w-full p-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-150" required>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="text-center">
            <button type="submit" class="w-3/5 text-center p-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-md transition duration-200">Register</button>
        </div>
    </form>
    <p class="text-center mt-6 text-gray-600">Already have an account? <a href="/index.php?page=login" class="text-blue-600 hover:underline">Login here</a></p>
</div>