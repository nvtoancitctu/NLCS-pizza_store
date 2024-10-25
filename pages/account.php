<?php
require_once '../config.php'; // Kết nối CSDL
require_once '../controllers/OrderController.php'; // Controller xử lý đơn hàng

if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_email'])) {
  header("Location: /index.php?page=login");
  exit();
}

// Khởi tạo OrderController
$orderController = new OrderController($conn);

// Lấy danh sách đơn hàng của người dùng
$orderItems = $orderController->getOrdersByUserId($_SESSION['user_id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_panel'])) {
  if ($_SESSION['user_role'] === 'admin') {
    header("Location: /index.php?admin=list"); // Điều hướng đến trang quản lý sản phẩm
    exit();
  }
}
?>

<!-- Profile Section -->
<div class="container mx-auto w-4/5 mt-10 mb-10 p-6 bg-white shadow-lg rounded-lg">
  <h2 class="text-3xl font-bold text-center mb-6">Profile</h2>

  <div class="flex items-center justify-between p-8 bg-white shadow-md rounded-lg w-3/5 mx-auto mb-6">
    <!-- User Name -->
    <div class="flex items-center space-x-4">
      <i class="fas fa-user text-2xl text-blue-500"></i>
      <div>
        <p class="font-semibold text-gray-800">Name</p>
        <p class="text-gray-600"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
      </div>
    </div>

    <!-- User Email & Admin Panel Button -->
    <div class="flex items-center space-x-4 ml-4">
      <i class="fas fa-envelope text-2xl text-blue-500"></i>
      <div>
        <p class="font-semibold text-gray-800">Email</p>
        <p class="text-gray-600"><?= htmlspecialchars($_SESSION['user_email']) ?></p>
      </div>
    </div>

    <!-- Admin Panel Button -->
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
      <form method="POST" action="/index.php?page=account">
        <button type="submit" name="admin_panel"
          class="ml-4 bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded transition duration-200">
          Admin Panel</button>
      </form>
    <?php endif; ?>
  </div>

  <!-- Order History Section -->
  <h3 class="text-2xl font-bold text-center mt-6">Order History</h3>
  <div class="mt-4">
    <?php
    // Lấy user_id từ session
    $user_id = $_SESSION['user_id'];

    // Truy vấn đơn hàng từ bảng orders
    $query_orders = "SELECT * FROM orders WHERE user_id = :id";
    $stmt_orders = $conn->prepare($query_orders);
    $stmt_orders->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt_orders->execute();
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

    // Kiểm tra nếu có kết quả trả về
    if (count($orders) > 0):
    ?>
      <ol class="list-decimal pl-5 space-y-4">
        <?php foreach ($orders as $order): ?>
          <li class="mb-4">
            <div class="border border-gray-300 rounded-lg p-4">
              <strong>Order ID:</strong> <?= $order['id'] ?><br>
              <strong>Total:</strong> $<?= number_format($order['total'], 2) ?><br>
              <strong>Payment Method:</strong> <?= ucfirst($order['payment_method']) ?><br>
              <strong>Status:</strong> <?= ucfirst($order['status']) ?><br>
              <strong>Address:</strong> <?= htmlspecialchars($order['address']) ?><br>
              <strong>Order Datetime:</strong> <?= $order['created_at'] ?><br>

              <!-- Truy vấn chi tiết đơn hàng từ bảng order_items -->
              <?php
              $query_order_items = "SELECT * FROM order_items a JOIN products b ON a.product_id = b.id WHERE order_id = :order_id";
              $stmt_items = $conn->prepare($query_order_items);
              $stmt_items->bindParam(':order_id', $order['id'], PDO::PARAM_INT);
              $stmt_items->execute();
              $order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
              ?>
              <table class="table-auto w-full text-left mt-2 border border-gray-300 rounded-lg overflow-hidden shadow-md">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="border px-4 py-2 border-b text-left">Product Name</th>
                    <th class="border px-4 py-2 border-b text-left">Quantity</th>
                    <th class="border px-4 py-2 border-b text-left">Price</th>
                    <th class="border px-4 py-2 border-b text-left">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($order_items as $item): ?>
                    <tr class="hover:bg-gray-50">
                      <td class="border px-4 py-2"><?= htmlspecialchars($item['name']) ?></td>
                      <td class="border px-4 py-2"><?= $item['quantity'] ?></td>
                      <td class="border px-4 py-2">$<?= number_format($item['price'], 2) ?></td>
                      <td class="border px-4 py-2">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
    <?php else: ?>
      <p class="text-center text-gray-500">No orders found.</p>
    <?php endif; ?>
  </div>

  <!-- Logout Button -->
  <form method="POST" id="logout-form" class="flex justify-center mt-6">
    <button type="submit" name="logout" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition duration-200">Logout</button>
  </form>
</div>