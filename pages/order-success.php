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

<h1 class="text-center mt-4 text-2xl font-bold">Order Confirmation</h1>
<div class="container w-3/5 mx-auto p-2">
  <?php if ($orderDetails): ?>
    <div class="bg-white shadow-md rounded-lg p-6 mb-4">
      <p class="text-lg mb-2">Thank you for your order! Your order ID is <strong class="text-blue-600">#<?= htmlspecialchars($orderDetails['id']) ?></strong>.</p>
      <p class="mb-1"><strong>Status:</strong> <span class="text-gray-700"><?= htmlspecialchars($orderDetails['status']) ?></span></p>
      <p class="mb-1"><strong>Total:</strong> <span class="text-red-500 font-bold">$<?= htmlspecialchars(number_format($orderDetails['total'], 2)) ?></span></p>
      <p class="mb-1"><strong>Payment Method:</strong> <span class="text-gray-700"><?= htmlspecialchars($orderDetails['payment_method']) ?></span></p>
      <p class="mb-1"><strong>Shipping Address:</strong> <span class="text-gray-700"><?= htmlspecialchars($orderDetails['address']) ?></span></p>
    </div>

    <h2 class="mb-4 text-xl font-semibold">Order Items</h2>
    <table class="table-auto w-full mx-auto border-2 border-gray-800">
      <thead>
        <tr class="bg-gray-100">
          <th class="border-2 border-gray-800 px-4 py-2 text-left">Product</th>
          <th class="border-2 border-gray-800 px-4 py-2 text-left">Quantity</th>
          <th class="border-2 border-gray-800 px-4 py-2 text-left">Price</th>
          <th class="border-2 border-gray-800 px-4 py-2 text-left">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orderDetails['items'] as $item): ?>
          <tr>
            <td class="border-gray-800 px-4 py-2 flex items-center">
              <img src="/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="50" class="mr-2">
              <?= htmlspecialchars($item['name']) ?>
            </td>
            <td class="border-2 border-gray-800 px-4 py-2"><?= htmlspecialchars($item['quantity']) ?></td>
            <td class="border-2 border-gray-800 px-4 py-2">$<?= htmlspecialchars(number_format($item['price'], 2)) ?></td>
            <td class="border-2 border-gray-800 px-4 py-2">$<?= htmlspecialchars(number_format($item['price'] * $item['quantity'], 2)) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <a href="/index.php?page=home" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Back to Home</a>
  <?php else: ?>
    <p>Order not found or you are not authorized to view this order.</p>
  <?php endif; ?>
</div>