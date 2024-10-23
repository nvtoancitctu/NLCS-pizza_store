<?php
require_once '../controllers/CartController.php';
require_once '../config.php'; // Kết nối CSDL
if (!isset($_SESSION['user_id'])) {
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

<h1 class="text-center mt-4 font-bold text-2xl">Your Cart</h1></br>

<div class="container">
    <?php if (!empty($cartItems)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><img src="/images/<?= htmlspecialchars($item['image']) ?>"
                                alt="<?= htmlspecialchars($item['name']) ?>"
                                width="60"><?= htmlspecialchars($item['name']) ?></td>

                        <td>$<?= htmlspecialchars($item['discount']) ?></td>
                        <td>
                            <form method="POST" action="/index.php?page=cart">
                                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1">
                                <button type="submit" name="update" class="btn btn-primary">Update</button>
                            </form>
                        </td>
                        <td>$<?= htmlspecialchars($item['discount'] * $item['quantity']) ?></td>
                        <td><a href="/index.php?page=cart&action=delete&cart_id=<?= $item['id'] ?>"
                                class="btn btn-danger">Delete</a></td>
                    </tr>

                <?php endforeach; ?>
            </tbody>

        </table>
        <div class="text-center mt-4">
            <a href="/index.php?page=checkout" class="btn btn-success">Proceed to Checkout</a></br>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <p>Your cart is empty. Why not check out our delicious pizzas?</p></br>
            <a href="/index.php?page=products" class="btn btn-primary mt-3">Go to Products</a></br>
        </div>
    <?php endif; ?>
</div>