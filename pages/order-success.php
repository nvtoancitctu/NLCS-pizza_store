<?php
require_once '../config.php';
require_once '../controllers/OrderController.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: /index.php?page=login");
  exit();
}

// Initialize OrderController
$orderController = new OrderController($conn);

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Get order_id from the query parameter
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
  $order_id = (int) $_GET['order_id'];
} else {
  echo "Invalid order ID.";
  exit();
}

// Fetch order details using the OrderController
$orderDetails = $orderController->getOrderDetails($order_id, $user_id);
?>

<h1 class="text-center mt-4 text-2xl font-bold text-blue-700">Order Confirmation</h1>
<div class="container w-3/5 mx-auto p-4 bg-gray-100 rounded-xl shadow-lg mb-4 mt-2">
  <?php if ($orderDetails): ?>
    <!-- Customer Information -->
    <div class="bg-white shadow-md rounded-xl p-4 mb-2 max-w-md mx-auto">
      <h2 class="text-xl font-semibold text-center mb-3 text-gray-800">Thank you for your order!</h2>
      <p class="text-center text-gray-600 mb-4">
        <span class="font-semibold text-blue-500">Order ID:</span> #<?= htmlspecialchars($orderDetails['id']) ?>
      </p>
      <ul class="text-sm text-gray-700 space-y-2">
        <li><strong>Status:</strong> <?= htmlspecialchars($orderDetails['status']) ?></li>
        <li><strong>Total:</strong> <span class="text-red-500 font-semibold">$<?= number_format($orderDetails['total'], 2) ?></span></li>
        <li><strong>Payment Method:</strong> <?= htmlspecialchars($orderDetails['payment_method']) ?></li>
        <li><strong>Shipping Address:</strong> <?= htmlspecialchars($orderDetails['address']) ?></li>
      </ul>
    </div>

    <!-- Order Items -->
    <h2 class="mb-4 text-xl font-semibold text-gray-700">Order Items</h2>
    <table class="table-auto w-full border border-gray-200 shadow-lg rounded-lg overflow-hidden">
      <thead class="bg-gradient-to-r from-yellow-100 to-yellow-200 text-gray-700">
        <tr>
          <th class="px-6 py-3 text-left font-semibold uppercase">Product</th>
          <th class="px-6 py-3 text-center font-semibold uppercase">Quantity</th>
          <th class="px-6 py-3 text-center font-semibold uppercase">Price</th>
          <th class="px-6 py-3 text-center font-semibold uppercase">Total</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <?php foreach ($orderDetails['items'] as $item): ?>
          <tr class="hover:bg-yellow-50 transition duration-150 ease-in-out">
            <td class="px-6 py-4 flex items-center">
              <img src="/images/<?= htmlspecialchars($item['image']) ?>"
                alt="<?= htmlspecialchars($item['name']) ?>" width="30"
                class="mr-4 rounded-md shadow-sm">
              <span class="text-gray-800 font-medium"><?= htmlspecialchars($item['name']) ?></span>
            </td>
            <td class="px-6 py-4 text-center text-gray-600"><?= htmlspecialchars($item['quantity']) ?></td>
            <td class="px-6 py-4 text-center text-gray-600">$<?= number_format($item['price'], 2) ?></td>
            <td class="px-6 py-4 text-center font-semibold text-gray-800">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="text-center mt-4">
      <button type="button" class="inline-block bg-blue-500 text-white px-6 py-2 rounded-full shadow-md hover:bg-blue-600"
        onclick="window.location.href='/index.php?page=home'">Back to Home</button>
    </div>
  <?php else: ?>
    <p class="text-center text-gray-500">Order not found or you are not authorized to view this order.</p>
  <?php endif; ?>
</div>