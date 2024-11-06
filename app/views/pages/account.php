<?php

// Kiểm tra xem người dùng đã đăng nhập chưa, nếu chưa sẽ điều hướng về trang đăng nhập
if (!isset($_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['user_id'])) {
  header("Location: /login");
  exit();
}

// Lấy user_id từ session để sử dụng trong việc lấy dữ liệu đơn hàng
$user_id = $_SESSION['user_id'];
$orderController = new OrderController($conn);
$orders = $orderController->getOrdersByUserId($user_id);

// Xử lý điều kiện khi người dùng nhấn vào nút Admin Panel hoặc Logout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['admin_panel']) && $_SESSION['user_role'] === 'admin') {
    // Điều hướng đến trang quản lý sản phẩm nếu người dùng có quyền admin
    header("Location: /admin/list");
    exit();
  }
  if (isset($_POST['logout'])) {
    // Đăng xuất và chuyển về trang đăng nhập
    session_destroy();
    header("Location: /login");
    exit();
  }
}
?>

<!-- Profile Section -->
<div class="container mx-auto w-4/5 mt-10 mb-10 p-6 bg-white shadow-lg rounded-lg">
  <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Profile</h2>
  <div class="flex items-center justify-around p-6 bg-gray-50 shadow-lg rounded-xl w-4/5 mx-auto mb-6">
    <!-- Thông tin người dùng (Tên, Email) -->
    <div class="flex items-center space-x-4">
      <i class="fas fa-user text-2xl text-yellow-500"></i>
      <div>
        <p class="font-semibold text-gray-800">Name</p>
        <p class="text-gray-600"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
      </div>
    </div>
    <div class="flex items-center space-x-4 ml-4">
      <i class="fas fa-envelope text-2xl text-yellow-500"></i>
      <div>
        <p class="font-semibold text-gray-800">Email</p>
        <p class="text-gray-600"><?= htmlspecialchars($_SESSION['user_email']) ?></p>
      </div>
    </div>
    <!-- Nút Admin Panel chỉ hiển thị nếu người dùng là admin -->
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
      <form method="POST" action="/account" class="ml-4">
        <button type="submit" name="admin_panel" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded-lg transition duration-200 shadow">Admin Panel</button>
      </form>
    <?php endif; ?>
  </div>

  <!-- Order History Section -->
  <h3 class="text-2xl font-bold text-center mt-8 text-gray-800">Order History</h3>
  <div class="mt-6">
    <?php if ($orders): ?>
      <ol class="list-decimal pl-5 space-y-6">
        <?php foreach ($orders as $order): ?>
          <?php $orderdetails = $orderController->getOrderDetailsByOrderId($order['id']); ?>
          <li class="mb-6">
            <div class="border border-gray-200 rounded-xl p-5 bg-blue-50 shadow-sm">
              <!-- Thông tin đơn hàng cơ bản -->
              <div class="w-2/5 p-3 bg-white border border-gray-200 rounded-xl shadow-sm space-y-2">
                <p class="text-sm text-gray-700"><strong>Order ID:</strong> <span class="text-blue-600">#<?= htmlspecialchars($order['id']) ?></span></p>
                <p class="text-sm text-gray-700"><strong>Total:</strong> <span class="text-red-500 font-semibold">$<?= number_format($order['total'], 2) ?></span></p>
                <p class="text-sm text-gray-700"><strong>Payment Method:</strong> <span class="capitalize"><?= ucfirst($order['payment_method']) ?></span></p>
                <p class="text-sm text-gray-700"><strong>Status:</strong> <span class="capitalize"><?= ucfirst($order['status'] ?? 'unknown') ?></span></p>
                <p class="text-sm text-gray-700"><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                <p class="text-sm text-gray-700"><strong>Order Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
              </div>
              <!-- Thông tin chi tiết các sản phẩm trong đơn hàng -->
              <table class="table-auto w-full text-left mt-3 border border-gray-50 rounded-xl shadow bg-white">
                <thead class="bg-yellow-100">
                  <tr>
                    <th class="border px-4 py-2">Product Name</th>
                    <th class="border px-4 py-2 text-center">Quantity</th>
                    <th class="border px-4 py-2 text-center">Price</th>
                    <th class="border px-4 py-2 text-center">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orderdetails as $item): ?>
                    <tr class="hover:bg-gray-200 transition text-center">
                      <td class="border px-4 py-2 text-left"><?= htmlspecialchars($item['name']) ?></td>
                      <td class="border px-4 py-2"><?= htmlspecialchars($item['quantity']) ?></td>
                      <td class="border px-4 py-2">
                        <!-- Hiển thị giá discount màu đỏ nếu có giảm giá, ngược lại là giá thường -->
                        <?= $item['price_to_display'] < $item['price'] ? "<span class='text-red-500'>$" . number_format($item['price_to_display'], 2) . "</span>" : "$" . number_format($item['price'], 2) ?>
                      </td>
                      <td class="border px-4 py-2">$<?= number_format($item['total_price'], 2) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
    <?php else: ?>
      <!-- Thông báo khi không có đơn hàng nào -->
      <p class="text-center text-gray-500 mt-4">No orders found.</p>
    <?php endif; ?>
  </div>

  <!-- Logout Button -->
  <form method="POST" class="flex justify-center mt-8">
    <button type="submit" name="logout" onclick="confirmLogout(event)" class="bg-red-500 text-white px-5 py-2 rounded-md hover:bg-red-600 transition duration-200 shadow">Logout</button>
  </form>
</div>

<script>
  function confirmLogout(event) {
    // Hiển thị hộp thoại xác nhận
    const userConfirmed = confirm("Are you sure you want to logout?");
    if (userConfirmed) {
      // Người dùng xác nhận thì submit form
      document.getElementById('logout-form').submit();
    } else {
      // Ngăn chặn submit nếu người dùng nhấn "Hủy"
      event.preventDefault();
    }
  }
</script>