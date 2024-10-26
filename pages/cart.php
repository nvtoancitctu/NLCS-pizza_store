<?php
require_once '../controllers/CartController.php';
require_once '../config.php'; // Kết nối CSDL

if (!isset($_SESSION['user_id'])) {
    $_SESSION['success'] = "Please log in to add items to your cart!";
    header("Location: /index.php?page=login"); // Điều hướng về trang đăng nhập
    exit();
}
// Khởi tạo CartController
$cartController = new CartController($conn);

// Giả sử user_id được lưu trong session (để đơn giản, bạn có thể lấy user_id từ session)
$user_id = $_SESSION['user_id'];

// Lấy sản phẩm trong giỏ hàng
$cartItems = $cartController->viewCart($user_id);

// Xử lý cập nhật số lượng sản phẩm trong giỏ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];
    $cartController->updateCartItem($cart_id, $quantity);
    header("Location: /index.php?page=cart");
    exit();
}

// Xử lý xóa sản phẩm khỏi giỏ hàng
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['cart_id']) && is_numeric($_GET['cart_id'])) {
        $cart_id = (int) $_GET['cart_id']; // Convert $cart_id to an integer
        $cartController->deleteCartItem($cart_id);
        header("Location: /index.php?page=cart");
        exit();
    } else {
        // Only display the error if the action is specifically delete but invalid
        if ($_GET['action'] === 'delete') {
            echo "Invalid cart ID or action.";
        }
    }
}
?>

<h1 class="text-center mt-8 text-3xl font-extrabold text-blue-700 tracking-wide">Your Cart</h1></br>

<div class="container mx-auto">
    <?php if (!empty($cartItems)): ?>
        <table class="table-auto w-4/5 mx-auto mb-4 border border-gray-500 bg-white">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-4 py-2">Product</th>
                    <th class="px-4 py-2">Price</th>
                    <th class="px-4 py-2">Quantity</th>
                    <th class="px-4 py-2">Total</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr class="border-t border-gray-300 hover:bg-gray-100 transition duration-200">
                        <td class="px-4 py-2 flex items-center">
                            <img src="/images/<?= htmlspecialchars($item['image']) ?>"
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                class="w-12 h-12 mr-2 rounded-md shadow-md">
                            <span class="font-semibold text-gray-800"><?= htmlspecialchars($item['name']) ?></span>
                        </td>

                        <td class="px-4 py-2">
                            <?php if ($item['discount'] > 0): ?>
                                <div>
                                    <p class="text-sm font-semibold text-gray-500 line-through">$<?= htmlspecialchars($item['price']); ?></p>
                                    <p class="text-lg font-semibold text-red-600 mt-1">$<?= htmlspecialchars($item['discount']); ?></p>
                                </div>
                            <?php else: ?>
                                <h3 class="text-lg font-semibold text-gray-800">$<?= htmlspecialchars($item['price']); ?></h3>
                            <?php endif; ?>
                        </td>

                        <td class="px-4 py-2">
                            <form method="POST" action="/index.php?page=cart" class="flex items-center">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1"
                                    class="border border-gray-300 rounded px-2 py-1 w-16 text-center">
                                <button type="submit" name="update"
                                    class="ml-2 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition duration-200">
                                    Update
                                </button>
                            </form>
                        </td>

                        <td class="px-4 py-2 font-semibold text-gray-800 text-lg">
                            <?php
                            $totalPrice = $item['discount'] > 0 ? $item['discount'] * $item['quantity'] : $item['price'] * $item['quantity'];
                            ?>
                            <span class="<?= $item['discount'] > 0 ? 'text-red-600 font-semibold' : 'text-gray-800 font-semibold' ?>">
                                $<?= htmlspecialchars($totalPrice) ?>
                            </span>
                        </td>

                        <td class="px-4 py-2">
                            <a href="/index.php?page=cart&action=delete&cart_id=<?= $item['id'] ?>"
                                class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition duration-200">
                                Delete
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
            <!--  -->
        </table>
        <div class="text-center mt-auto mb-2">
            <button type="button" class="bg-green-500 text-white px-5 py-2 rounded-lg transition duration-300 hover:bg-red-500 shadow-lg"
                onclick="window.location.href='/index.php?page=checkout'">Proceed to Checkout</button>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center p-4 rounded-xl bg-gray-200 text-blue-800">
            <p>Your cart is empty. Why not check out our delicious pizzas?</p>
            <button type="button" class="mt-4 bg-green-600 hover:bg-yellow-600 shadow-lg text-white px-5 py-2 rounded-lg transition duration-300 "
                onclick="window.location.href='/index.php?page=products'">Go to Products</button>
        </div>
    <?php endif; ?>
</div>