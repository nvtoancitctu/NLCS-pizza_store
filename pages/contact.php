<?php
// Xử lý dữ liệu khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Kiểm tra nếu các trường không rỗng
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Địa chỉ email của người nhận (admin hoặc hỗ trợ)
        $to = "admin@example.com";
        $subject = "Customer Contact Form Submission";
        $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
        $headers = "From: $email";

        // Gửi email
        if (mail($to, $subject, $body, $headers)) {
            $success = "Your message has been sent successfully!";
        } else {
            $error = "Sorry, something went wrong. Please try again.";
        }
    } else {
        $error = "All fields are required!";
    }
}
?>

<div class="container mx-auto p-6">
    <h1 class="text-4xl text-center font-bold text-gray-800 mb-6">Contact Us</h1>

    <!-- Hiển thị thông báo thành công hoặc lỗi -->
    <?php if (isset($success)): ?>
        <p class="bg-green-500 text-white p-4 rounded-lg text-center"><?php echo $success; ?></p>
    <?php elseif (isset($error)): ?>
        <p class="bg-red-500 text-white p-4 rounded-lg text-center"><?php echo $error; ?></p>
    <?php endif; ?>

    <form action="contact.php" method="POST" class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto">
        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Your Name:</label>
            <input type="text" id="name" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter your name">
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Your Email:</label>
            <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter your email">
        </div>
        <div class="mb-4">
            <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message:</label>
            <textarea id="message" name="message" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter your message"></textarea>
        </div>
        <div class="text-center">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Send Message
            </button>
        </div>
    </form>
</div>