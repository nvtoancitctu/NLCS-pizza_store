<?php

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['success'] = "Please log in to add items to your cart!";
    header("Location: /login");
    exit();
}

// Khởi tạo CartController
$cartController = new CartController($conn);

// Lấy user_id từ phiên
$user_id = $_SESSION['user_id'];

// Lấy sản phẩm trong giỏ hàng
$cartItems = $cartController->viewCart($user_id);

// Xử lý cập nhật số lượng sản phẩm trong giỏ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];
    $cartController->updateCartItem($cart_id, $quantity);
    header("Location: /cart");
    exit();
}

// Xử lý xóa sản phẩm khỏi giỏ hàng
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['cart_id']) && is_numeric($_GET['cart_id'])) {
        $cart_id = (int) $_GET['cart_id'];
        $cartController->deleteCartItem($cart_id);
        header("Location: /cart");
        exit();
    } else {
        // 
        if ($_GET['action'] === 'delete') {
            echo "Invalid cart ID or action.";
        }
    }
}
?>

<h1 class="text-center mt-8 text-3xl font-bold text-blue-700 tracking-wide">Your Cart</h1></br>

<div class="container mx-auto">
    <?php if (!empty($cartItems)): ?>
        <table class="table-auto w-4/5 mx-auto mb-6 border border-gray-300 bg-white shadow-lg rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700">
                    <th class="px-6 py-3 text-left text-sm font-semibold">Product</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Price</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Quantity</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Total</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr class="border-t border-gray-300 hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 flex items-center">
                            <img src="/images/<?= htmlspecialchars($item['image']) ?>"
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                class="w-16 h-16 mr-4 rounded-md shadow-md">
                            <span class="text-gray-800 font-medium"><?= htmlspecialchars($item['name']) ?></span>
                        </td>
                        <td class="px-6 py-4 text-gray-800">
                            <?php if ($item['price_to_display'] < $item['price']): ?>
                                <div>
                                    <span class="text-xs text-gray-500 line-through">$<?= number_format($item['price'], 2) ?></span>
                                    <p class="text-sm text-red-600 mt-1 font-semibold">$<?= number_format($item['price_to_display'], 2) ?></p>
                                </div>
                            <?php else: ?>
                                <span class="text-sm text-gray-800 font-semibold">$<?= number_format($item['price'], 2) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="/cart" class="flex items-center">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1"
                                    class="border border-gray-300 rounded px-2 py-1 w-16 text-center focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <button type="submit" name="update"
                                    class="ml-2 bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600 transition duration-200 text-xs font-semibold">
                                    Update
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-gray-800 font-semibold text-center">
                            $<?= number_format($item['total_price'], 2) ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="/index.php?page=cart&action=delete&cart_id=<?= $item['id'] ?>"
                                class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 transition duration-200 text-xs font-semibold">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Hiển thị tổng giá giỏ hàng -->
        <div class="text-center font-bold text-lg mt-4 mb-2" style="margin-left: 500px;">
            <span>Total Price: </span>
            <span class="text-red-600 text-xl">$<?= number_format(array_sum(array_map(function ($item) {
                                                    // Tính giá tổng dựa trên giá giảm giá nếu có
                                                    $unitPrice = !empty($item['price_to_display']) ? $item['price_to_display'] : $item['price'];
                                                    return $unitPrice * $item['quantity'];
                                                }, $cartItems)), 2) ?>
            </span>
        </div>

        <div class="text-center mt-auto mb-4">
            <button type="button" class="bg-green-500 text-white px-5 py-2 rounded-lg transition duration-300 hover:bg-red-500 shadow-lg"
                onclick="window.location.href='/checkout'">Proceed to Checkout</button>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center p-4 rounded-xl bg-gray-200 text-blue-800">
            <p>Your cart is empty. Why not check out our delicious pizzas?</p>
            <button type="button" class="mt-4 bg-green-600 hover:bg-yellow-600 shadow-lg text-white px-5 py-2 rounded-lg transition duration-300 "
                onclick="window.location.href='/products'">Go to Products</button>
        </div>
    <?php endif; ?>
</div>