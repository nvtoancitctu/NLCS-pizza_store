<?php

// Điều hướng đến trang login 
if (!isset($_SESSION['user_id'])) {
  header("Location: /login");
  exit(); // Dừng thực thi nếu không có user_id
}

// Khởi tạo orderController
$orderController = new OrderController($conn);

// Lấy user_id từ session
$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) && is_numeric($_GET['order_id']) ? (int) $_GET['order_id'] : null; // Lấy order_id từ URL

// Truy vấn lấy chi tiết đơn hàng
$orderDetails = $orderController->getOrderDetails($order_id, $user_id); // Gọi phương thức để lấy chi tiết đơn hàng
?>

<div class="container w-3/5 mx-auto p-6 bg-gray-50 rounded-2xl shadow-xl mb-6 mt-6">
  <h1 class="text-center text-3xl mb-6 font-extrabold text-blue-600">Order Confirmation</h1>

  <?php if ($orderDetails): ?> <!-- Kiểm tra xem có chi tiết đơn hàng hay không -->
    <div class="bg-yellow-50 shadow-lg rounded-2xl p-6 mb-6">
      <h2 class="text-xl font-semibold text-center mb-4 text-gray-800">Thank you for your order!</h2>
      <p class="text-center text-gray-600 mb-4">
        <span class="font-semibold text-blue-600">Order ID:</span> #<?= htmlspecialchars($orderDetails['id']) ?>
      </p>
      <ul class="text-sm text-gray-700 space-y-2 leading-6">
        <li><strong>Status:</strong> <?= htmlspecialchars($orderDetails['status']) ?></li>
        <li><strong>Total:</strong> <span class="text-red-500 font-semibold">$<?= number_format($orderDetails['total'], 2) ?></span></li>
        <li><strong>Payment Method:</strong> <?= htmlspecialchars($orderDetails['payment_method']) ?></li>
        <li><strong>Shipping Address:</strong> <?= htmlspecialchars($orderDetails['address']) ?></li>
      </ul>
    </div>

    <!-- Hiển thị danh sách các sản phẩm trong đơn hàng -->
    <table class="table-auto w-full border border-gray-200 shadow-xl rounded-lg overflow-hidden">
      <thead class="bg-gradient-to-r from-yellow-100 to-yellow-300 text-gray-700">
        <tr>
          <th class="px-6 py-3 text-left font-semibold uppercase">Product</th>
          <th class="px-6 py-3 text-center font-semibold uppercase">Quantity</th>
          <th class="px-6 py-3 text-center font-semibold uppercase">Price</th>
          <th class="px-6 py-3 text-center font-semibold uppercase">Total</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <?php foreach ($orderDetails['items'] as $item): ?> <!-- Lặp qua từng sản phẩm trong đơn hàng -->
          <tr class="hover:bg-yellow-50 transition-all duration-200 ease-in-out">
            <td class="px-6 py-4 flex items-center">
              <img src="/images/<?= htmlspecialchars($item['image']) ?>"
                alt="<?= htmlspecialchars($item['name']) ?>"
                width="40"
                class="mr-4 rounded-md shadow">
              <span class="text-gray-800 font-medium"><?= htmlspecialchars($item['name']) ?></span>
            </td>
            <td class="px-6 py-4 text-center text-gray-600"><?= htmlspecialchars($item['quantity']) ?></td>
            <td class="px-6 py-4 text-center text-gray-600">
              <?php if ($item['price_to_display'] < $item['price']): ?>
                <span class="line-through text-gray-500 text-sm">$<?= number_format($item['price'], 2) ?></span>
                <span class="text-red-600 text-base font-semibold">$<?= number_format($item['price_to_display'], 2) ?></span>
              <?php else: ?>
                <span>$<?= number_format($item['price'], 2) ?></span>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4 text-center font-semibold text-gray-800">
              $<?= number_format($item['total_price'], 2) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  <?php else: ?>
    <!-- Nếu không có chi tiết đơn hàng -->
    <p class="text-center text-gray-500">Order not found or you are not authorized to view this order.</p>
  <?php endif; ?>
</div>

<div class="text-center mb-4">
  <button type="button" class="font-semibold inline-block bg-blue-500 text-white px-8 py-3 rounded-full shadow-md hover:bg-blue-600"
    onclick="window.location.href='/home'">Back to Home</button>
</div>