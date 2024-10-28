<?php

// Khởi tạo kết nối đến cơ sở dữ liệu
$conn = new mysqli($host, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy ID người dùng từ session, có thể là NULL nếu người dùng không đăng nhập
$user_id = $_SESSION['user_id'] ?? null;

// Xử lý khi form được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy và làm sạch dữ liệu từ form
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Kiểm tra nếu các trường không rỗng
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Chuẩn bị câu lệnh SQL chèn dữ liệu vào bảng contact
        $stmt = $conn->prepare("INSERT INTO contact (user_id, name, email, message) VALUES (?, ?, ?, ?)");

        // Kiểm tra xem việc chuẩn bị truy vấn có thành công không
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error); // Báo lỗi chi tiết nếu chuẩn bị thất bại
        }

        // Ràng buộc dữ liệu với câu truy vấn
        $stmt->bind_param("isss", $user_id, $name, $email, $message);

        // Thực thi câu truy vấn
        if ($stmt->execute()) {
            echo "<script>alert('Your message has been submitted and stored in our database!');</script>";
        } else {
            echo "<script>alert('Failed to save your message. Error: " . $stmt->error . "');</script>";
        }

        // Đóng statement sau khi thực hiện
        $stmt->close();
    } else {
        $error = "All fields are required!";
    }
}

// Đóng kết nối cơ sở dữ liệu sau khi xử lý xong
$conn->close();
?>

<!-- Form liên hệ được căn giữa trong container, với thiết kế responsive và hộp thoại -->
<div class="container mx-auto p-6">
    <h1 class="text-4xl text-center font-bold text-gray-900 mb-8">Contact Us</h1>
    <form action="/index.php?page=contact" method="POST"
        class="bg-white p-10 rounded-xl shadow-lg max-w-lg mx-auto transition duration-300 ease-in-out hover:shadow-2xl">
        <!-- Trường nhập tên -->
        <div class="mb-6">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Your Name:</label>
            <input type="text" id="name" name="name"
                class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150"
                placeholder="Enter your name" required>
        </div>
        <!-- Trường nhập email -->
        <div class="mb-6">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Your Email:</label>
            <input type="email" id="email" name="email"
                class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150"
                placeholder="Enter your email" required>
        </div>
        <!-- Trường nhập tin nhắn -->
        <div class="mb-6">
            <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message:</label>
            <textarea id="message" name="message" rows="3"
                class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150"
                placeholder="Enter your message" required></textarea>
        </div>
        <!-- Nút gửi tin nhắn -->
        <div class="text-center">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out transform hover:scale-105">
                Send Message
            </button>
        </div>
    </form>
</div>