<?php
// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
  header("Location: /login");
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
  header("Location: /order-success&order_id=$order_id");
  exit();
}
?>

<!-- Thông tin thanh toán -->
<h1 class="text-center mt-8 text-3xl font-bold text-blue-700 tracking-wide">Checkout</h1>

<div class="container mx-auto px-4 mt-4">
  <form method="POST" action="/checkout" id="checkout-form" class="bg-white shadow-lg border rounded-lg p-8 max-w-2xl mx-auto mb-6">

    <!-- Danh sách sản phẩm trong giỏ hàng -->
    <div class="overflow-x-auto mb-6">
      <table class="min-w-full border rounded-lg">
        <thead class="bg-gray-100 border-b">
          <tr class="text-sm text-gray-700 font-semibold">
            <th class="py-3 px-4 text-left">Product</th>
            <th class="py-3 px-4 text-center">Quantity</th>
            <th class="py-3 px-4 text-left">Price</th>
            <th class="py-3 px-4 text-left">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cartItems as $item): ?>
            <tr class="text-sm border-b hover:bg-gray-50">
              <td class="py-3 px-4"><?= htmlspecialchars($item['name']) ?></td>
              <td class="py-3 px-4 text-center"><?= htmlspecialchars($item['quantity']) ?></td>
              <td class="py-3 px-4">
                <?php if ($item['price_to_display'] < $item['price']): ?>
                  <span class="text-red-600 font-semibold">$<?= number_format($item['price_to_display'], 2) ?></span>
                <?php else: ?>
                  <span>$<?= number_format($item['price'], 2) ?></span>
                <?php endif; ?>
              </td>
              <td class="py-3 px-4">
                $<?= number_format($item['total_price'], 2) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Thông tin giao hàng -->
    <h2 class="text-lg font-bold mb-2">Shipping Information</h2>
    <div class="mb-6">
      <textarea name="address" id="address" class="w-full p-3 border rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400" required placeholder="Enter your shipping address..."></textarea>
    </div>

    <!-- Phương thức thanh toán -->
    <h2 class="text-lg font-bold mb-2">Payment Method</h2>
    <div class="mb-6">
      <select name="payment_method" class="w-full p-3 border rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        <option value="credit_card">Credit Card</option>
        <option value="paypal">Paypal</option>
        <option value="cash_on_delivery">Cash on Delivery</option>
      </select>
    </div>

    <!-- Tổng số tiền -->
    <div class="text-lg font-bold mt-4">
      Total Amount: <span class="text-red-500">$<?= number_format($totalAmount, 2) ?></span>
    </div>

    <input type="hidden" name="checkout" value="1">

    <!-- Các nút thao tác -->
    <div class="flex justify-between mt-6">
      <button type="button" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200" onclick="window.location.href='/cart'">Cancel</button>
      <button type="submit" onclick="confirmCheckout(event)" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">Place Order</button>
    </div>
  </form>
</div>

<script>
  function confirmCheckout(event) {
    const confirmOrder = confirm("Are you sure you want to place an order?");
    if (confirmOrder) {
      // Gửi biểu mẫu với ID xác định
      document.getElementById('checkout-form').submit();
    } else {
      // Ngăn chặn submit nếu người dùng nhấn "Hủy"
      event.preventDefault();
    }
  }
</script>