<?php

class Cart
{
    private $conn;
    private $table = 'cart';

    public $id;
    public $user_id;
    public $product_id;
    public $quantity;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tất cả sản phẩm trong giỏ hàng của người dùng
    public function getCartItems($user_id)
    {
        $query = "SELECT c.id, c.product_id, c.quantity, p.name, p.price, p.image, p.discount 
                  FROM " . $this->table . " c JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm sản phẩm vào giỏ hàng
    public function addToCart($user_id, $product_id, $quantity)
    {
        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $query = "SELECT id FROM " . $this->table . " WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $product_id]);

        if ($stmt->rowCount() > 0) {
            // Nếu sản phẩm đã có, tăng số lượng
            $query = "UPDATE " . $this->table . " SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$quantity, $user_id, $product_id]);
        } else {
            // Nếu sản phẩm chưa có, thêm mới vào giỏ hàng
            $query = "INSERT INTO " . $this->table . " (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$user_id, $product_id, $quantity]);
        }
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateCartItem($cart_id, $quantity)
    {
        $query = "UPDATE " . $this->table . " SET quantity = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$quantity, $cart_id]);
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function deleteCartItem($cart_id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$cart_id]);
    }
    public function clearUserCart($user_id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id]);
    }

    // Tổng số lượng sản phẩm trong giỏ hàng
    public function getCartItemCount($user_id)
    {
        // Đảm bảo user_id là số nguyên
        $user_id = (int)$user_id;

        // Chuẩn bị câu lệnh với PDO
        $stmt = $this->conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Thực thi câu lệnh
        $stmt->execute();

        // Lấy kết quả
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ?? 0; // Trả về tổng hoặc 0 nếu không có sản phẩm
    }
}
