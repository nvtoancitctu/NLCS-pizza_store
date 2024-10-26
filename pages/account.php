<?php
require_once '../config.php'; // Kết nối CSDL
require_once '../controllers/OrderController.php'; // Controller xử lý đơn hàng

// Kiểm tra xem người dùng đã đăng nhập chưa
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
    header("Location: /index.php?page=list"); // Điều hướng đến trang quản lý sản phẩm
    exit();
  }
}
?>

<!-- Profile Section -->
<div class="container mx-auto w-4/5 mt-10 mb-10 p-6 bg-white shadow-lg rounded-lg">
  <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Profile</h2>

  <div class="flex items-center justify-around p-6 bg-gray-50 shadow-lg rounded-xl w-4/5 mx-auto mb-6">
    <!-- User Name -->
    <div class="flex items-center space-x-4">
      <i class="fas fa-user text-2xl text-yellow-500"></i>
      <div>
        <p class="font-semibold text-gray-800">Name</p>
        <p class="text-gray-600"><?= htmlspecialchars($_SESSION['user_name']) ?></p>
      </div>
    </div>

    <!-- User Email -->
    <div class="flex items-center space-x-4 ml-4">
      <i class="fas fa-envelope text-2xl text-yellow-500"></i>
      <div>
        <p class="font-semibold text-gray-800">Email</p>
        <p class="text-gray-600"><?= htmlspecialchars($_SESSION['user_email']) ?></p>
      </div>
    </div>

    <!-- Admin Panel Button -->
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
      <form method="POST" action="/index.php?page=account" class="ml-4">
        <button type="submit" name="admin_panel" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-4 rounded-lg transition duration-200 shadow">
          Admin Panel
        </button>
      </form>
    <?php endif; ?>
  </div>

  <!-- Order History Section -->
  <h3 class="text-2xl font-bold text-center mt-8 text-gray-800">Order History</h3>
  <div class="mt-6">
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
      <ol class="list-decimal pl-5 space-y-6">
        <?php foreach ($orders as $order): ?>
          <li class="mb-6">
            <div class="border border-gray-200 rounded-xl p-5 bg-gray-100 shadow-sm">
              <!-- Thông tin đơn hàng -->
              <div class="w-2/5 p-3 bg-yellow-50 border border-gray-200 rounded-xl shadow-sm space-y-2">
                <p class="text-sm text-gray-700"><strong class="text-gray-800">Order ID:</strong> <span class="text-blue-600">#<?= htmlspecialchars($order['id']) ?></span></p>
                <p class="text-sm text-gray-700"><strong class="text-gray-800">Total:</strong> <span class="text-red-500 font-semibold">$<?= number_format($order['total'], 2) ?></span></p>
                <p class="text-sm text-gray-700"><strong class="text-gray-800">Payment Method:</strong> <span class="capitalize"><?= ucfirst($order['payment_method']) ?></span></p>
                <p class="text-sm text-gray-700"><strong class="text-gray-800">Status:</strong> <span class="capitalize"><?= ucfirst($order['status']) ?></span></p>
                <p class="text-sm text-gray-700"><strong class="text-gray-800">Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                <p class="text-sm text-gray-700"><strong class="text-gray-800">Order Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
              </div>

              <!-- Truy vấn chi tiết đơn hàng từ bảng order_items -->
              <?php
              $query_order_items = "SELECT * FROM order_items a JOIN products b ON a.product_id = b.id WHERE order_id = :order_id";
              $stmt_items = $conn->prepare($query_order_items);
              $stmt_items->bindParam(':order_id', $order['id'], PDO::PARAM_INT);
              $stmt_items->execute();
              $order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
              ?>

              <table class="table-auto w-full text-left mt-3 border border-gray-200 rounded-xl overflow-hidden shadow">
                <thead class="bg-yellow-100">
                  <tr>
                    <th class="border px-4 py-2 text-left font-semibold">Product Name</th>
                    <th class="border px-4 py-2 text-left font-semibold">Quantity</th>
                    <th class="border px-4 py-2 text-left font-semibold">Price</th>
                    <th class="border px-4 py-2 text-left font-semibold">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($order_items as $item): ?>
                    <tr class="hover:bg-gray-50 transition ease-in-out">
                      <td class="border px-4 py-2"><?= htmlspecialchars($item['name']) ?></td>
                      <td class="border px-4 py-2"><?= htmlspecialchars($item['quantity']) ?></td>
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
      <p class="text-center text-gray-500 mt-4">No orders found.</p>
    <?php endif; ?>
  </div>

  <!-- Logout Button -->
  <form method="POST" id="logout-form" class="flex justify-center mt-8">
    <button type="submit" name="logout" class="bg-red-500 text-white px-5 py-2 rounded-md hover:bg-red-600 transition duration-200 shadow">
      Logout
    </button>
  </form>
</div>