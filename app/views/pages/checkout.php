<?php
// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
  header("Location: /index.php?page=login");
  exit();
}

// Khởi tạo các controller
$cartController = new CartController($conn);
$orderController = new OrderController($conn);

// Lấy user_id từ phiên
$user_id = $_SESSION['user_id'];

// Lấy các sản phẩm trong giỏ hàng của người dùng
$cartItems = $cartController->viewCart($user_id);

if (empty($cartItems)) {
  header("Location: /index.php?page=cart&error=empty"); // Nếu giỏ hàng trống, điều hướng về trang giỏ hàng với thông báo lỗi
  exit();
}

// Lấy tổng giá trị giỏ hàng từ `total_cart_price`
$totalAmount = $cartItems[0]['total_cart_price'] ?? 0; // Sử dụng giá trị tổng giỏ hàng từ sản phẩm đầu tiên

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) { // Kiểm tra xem có yêu cầu đặt hàng hay không
  $address = trim(strip_tags($_POST['address'])); // Làm sạch địa chỉ
  $payment_method = $_POST['payment_method']; // Lấy phương thức thanh toán (cần xác thực đầu vào này)

  // Gọi OrderController để tạo một đơn hàng mới
  $order_id = $orderController->createOrder($user_id, $cartItems, $payment_method, $address);

  // Xóa giỏ hàng sau khi đặt hàng thành công
  $cartController->clearCart($user_id);

  // Điều hướng đến trang thành công đơn hàng
  header("Location: /index.php?page=order-success&order_id=$order_id");
  exit();
}
?>

<!-- Thông tin thanh toán -->
<h1 class="text-center mt-8 text-3xl font-extrabold text-blue-700 tracking-wide">Checkout</h1>

<div class="container mx-auto px-4 mt-4">
  <form method="POST" action="/index.php?page=checkout" id="checkout-form" class="bg-white shadow-md border rounded-2xl p-6 mx-auto max-w-xl mb-4">
    <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>
    <div class="mb-4">
      <textarea name="address" id="address" class="form-control border border-gray-300 rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea> <!-- Nhập địa chỉ giao hàng -->
    </div>

    <h2 class="text-xl font-semibold mb-4">Payment Method</h2>
    <div class="mb-4">
      <select name="payment_method" class="border border-gray-300 rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        <option value="credit_card">Credit Card</option>
        <option value="paypal">Paypal</option>
        <option value="cash_on_delivery">COD</option>
      </select>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full border border-black shadow-md rounded-lg">
        <thead class="bg-gray-300 border-b">
          <tr class="text-sm">
            <th class="py-3 px-4 text-left text-gray-600">Product</th>
            <th class="py-3 px-4 text-center text-gray-600">Quantity</th>
            <th class="py-3 px-4 text-left text-gray-600">Price</th>
            <th class="py-3 px-4 text-left text-gray-600">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cartItems as $item): ?>
            <tr class="text-xs border-b hover:bg-gray-100 transition duration-200">
              <td class="py-2 px-4"><?= htmlspecialchars($item['name']) ?></td>
              <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['quantity']) ?></td>
              <td class="py-2 px-4">
                <?php if ($item['price_to_display'] < $item['price']): ?>
                  <p class="text-red-600">$<?= number_format($item['price_to_display'], 2) ?></p>
                <?php else: ?>
                  <p>$<?= number_format($item['price'], 2) ?></p>
                <?php endif; ?>
              </td>
              <td class="py-2 px-4">
                $<?= number_format($item['total_price'], 2) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h3 class="text-xl font-bold mt-4">
      Total Amount: <span class="text-red-600">$<?= number_format($totalAmount, 2) ?></span>
    </h3>

    <input type="hidden" name="checkout" value="1">

    <div class="flex justify-between mt-6">
      <button type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" onclick="window.location.href='/index.php?page=cart'">Cancel</button> <!-- Nút hủy -->
      <button type="button" onclick="confirmCheckout()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Place Order</button> <!-- Nút đặt hàng -->
    </div>
  </form>
</div>

<script>
  function confirmCheckout() {
    const confirmOrder = confirm("Are you sure you want to place an order?");
    if (confirmOrder) {
      // Gửi biểu mẫu với ID xác định
      document.getElementById('checkout-form').submit();
    }
  }
</script>