<?php
session_start();

// Kiểm tra nếu người dùng đã đăng nhập và là admin
if (isset($_SESSION['username']) && $_SESSION['username'] == 'admin') {
    echo "Chào mừng bạn đến trang admin!";
} else {
    echo "Bạn không có quyền truy cập trang này!";
    header("Location: login.php");  // Chuyển hướng về trang đăng nhập nếu không có quyền
    exit();
}
