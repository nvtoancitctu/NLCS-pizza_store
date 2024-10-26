<?php
if (!isset($_SESSION['user_id'])) {
  header("Location: /index.php?page=login"); // Điều hướng đến trang đăng nhập
  exit();
}

require_once '../config.php';
require_once '../controllers/CartController.php';
require_once '../controllers/OrderController.php';

// Khởi tạo các controller
$cartController = new CartController($conn);
$orderController = new OrderController($conn);

// Lấy user_id từ phiên
$user_id = $_SESSION['user_id'];

// Lấy các sản phẩm trong giỏ hàng của người dùng
$cartItems = $cartController->viewCart($user_id);
if (empty($cartItems)) {

  header("Location: /index.php?page=cart&error=empty");
  exit();
}

// Tính tổng số tiền
$totalAmount = array_reduce($cartItems, function ($carry, $item) {
  return $carry + ($item['price'] * $item['quantity']);
}, 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
  $address = trim(strip_tags($_POST['address'])); // Làm sạch địa chỉ
  $payment_method = $_POST['payment_method']; // Cân nhắc việc xác thực đầu vào này

  // Gọi OrderController để tạo một đơn hàng mới
  $order_id = $orderController->createOrder($user_id, $totalAmount, $payment_method, $address);

  // Kiểm tra xem product_id có được cung cấp không
  foreach ($cartItems as $item) {

    if (!isset($item['product_id']) || empty($item['product_id'])) {
      echo "Product ID is missing for an item!";
      var_dump($item); // Đầu ra gỡ lỗi
      exit();
    }
    // 
  }

  // Lưu các mặt hàng đơn hàng
  foreach ($cartItems as $item) {
    $orderController->addOrderItem($order_id, $item['product_id'], $item['quantity'], $item['price']);
  }

  // Xóa giỏ hàng sau khi đặt hàng thành công
  $cartController->clearCart($user_id);

  // Điều hướng đến trang thành công đơn hàng
  header("Location: /index.php?page=order-success&order_id=$order_id");
  exit();
}

?>

<!--  -->
<h1 class="text-center mt-8 text-3xl font-extrabold text-blue-700 tracking-wide">Checkout</h1>

<div class="container mx-auto px-4 mt-4">
  <form method="POST" action="/index.php?page=checkout"
    class="bg-white shadow-md border rounded-2xl p-6 mx-auto max-w-xl mb-4">
    <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>
    <div class="mb-4">
      <!-- <label for="address" class="block text-black-500 text-sm mb-2">Address:</label> -->
      <textarea name="address" id="address"
        class="form-control border border-gray-300 rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
    </div>

    <h2 class="text-xl font-semibold mb-4">Payment Method</h2>
    <div class="mb-4">
      <select name="payment_method"
        class="border border-gray-300 rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        <option value="credit_card">Credit Card</option>
        <option value="paypal">Paypal</option>
        <option value="cash_on_delivery">COD</option>
      </select>
    </div>
    <!--  -->
    <div class="overflow-x-auto">
      <table class="min-w-full border border-black shadow-md rounded-lg">
        <caption class="text-l text-center text-blue-600">Products in Cart</caption>
        <thead class="bg-gray-300 border-b">
          <tr class="text-sm">
            <th class="py-3 px-4 text-left text-gray-600">Product</th>
            <th class="py-3 px-4 text-center text-gray-600">Quantity</th>
            <th class="py-3 px-4 text-center text-gray-600">Price</th>
            <th class="py-3 px-4 text-center text-gray-600">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cartItems as $item): ?>
            <tr class="text-xs border-b hover:bg-gray-100 transition duration-200">
              <td class="py-2 px-4"><?= htmlspecialchars($item['name']) ?></td>
              <td class="text-center py-2"><?= htmlspecialchars($item['quantity']) ?></td>
              <td class="text-center py-2">$<?= number_format($item['price'], 2) ?></td>
              <td class="text-center py-2">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h3 class="text-xl font-bold mt-2">Total Amount: <span class="text-red-600">$<?= number_format($totalAmount, 2) ?></span></h3>
    <div class="flex justify-between mt-6">
      <button type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded"
        onclick="window.location.href='/index.php?page=cart'">Cancel</button>
      <button type="submit" name="checkout" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">Place Order</button>
    </div>
  </form>
</div>

<!--  -->