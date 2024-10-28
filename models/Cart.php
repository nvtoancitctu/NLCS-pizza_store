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

    // Khởi tạo lớp Cart với tham số kết nối cơ sở dữ liệu
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Lấy tất cả sản phẩm trong giỏ hàng của người dùng
     * @param int $user_id - ID của người dùng
     * @return array - Danh sách sản phẩm trong giỏ hàng
     */
    public function getCartItems($user_id)
    {
        $query = "SELECT 
                  c.id, 
                  c.product_id, 
                  c.quantity, 
                  p.name, 
                  p.price, 
                  p.image, 
                  
                  -- Hiển thị giá đã giảm nếu discount_end_time còn hiệu lực, ngược lại là giá gốc
                  CASE 
                      WHEN p.discount_end_time IS NOT NULL AND p.discount_end_time >= NOW() 
                      THEN p.discount 
                      ELSE p.price 
                  END AS price_to_display,

                  -- Tính tổng giá của từng sản phẩm dựa trên số lượng, áp dụng giá giảm nếu có và trong thời gian hiệu lực
                  CASE 
                      WHEN p.discount > 0 AND (p.discount_end_time IS NULL OR p.discount_end_time >= NOW()) 
                      THEN (p.discount * c.quantity) 
                      ELSE (p.price * c.quantity) 
                  END AS total_price,
                   
                  -- Tổng toàn bộ giá của giỏ hàng, tính trên từng sản phẩm với giá đã áp dụng giảm giá (nếu có)
                  SUM(
                      CASE 
                          WHEN p.discount > 0 AND (p.discount_end_time IS NULL OR p.discount_end_time >= NOW()) 
                          THEN (p.discount * c.quantity) 
                          ELSE (p.price * c.quantity) 
                      END
                    ) OVER() AS total_cart_price
              FROM " . $this->table . " c 
              JOIN products p ON c.product_id = p.id 
              WHERE c.user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thêm sản phẩm vào giỏ hàng, hoặc cập nhật nếu sản phẩm đã tồn tại
     * @param int $user_id - ID người dùng
     * @param int $product_id - ID sản phẩm
     * @param int $quantity - Số lượng sản phẩm
     * @return bool - Trạng thái thành công của việc thêm/cập nhật
     */
    public function addToCart($user_id, $product_id, $quantity)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $product_id]);

        if ($stmt->rowCount() > 0) {
            $query = "UPDATE " . $this->table . " SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$quantity, $user_id, $product_id]);
        } else {
            $query = "INSERT INTO " . $this->table . " (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$user_id, $product_id, $quantity]);
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     * @param int $cart_id - ID của mục giỏ hàng
     * @param int $quantity - Số lượng mới
     * @return bool - Trạng thái thành công của việc cập nhật
     */
    public function updateCartItem($cart_id, $quantity)
    {
        $query = "UPDATE " . $this->table . " SET quantity = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$quantity, $cart_id]);
    }

    /**
     * Xóa một sản phẩm khỏi giỏ hàng
     * @param int $cart_id - ID của mục giỏ hàng
     * @return bool - Trạng thái thành công của việc xóa
     */
    public function deleteCartItem($cart_id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$cart_id]);
    }

    /**
     * Xóa toàn bộ giỏ hàng của người dùng
     * @param int $user_id - ID của người dùng
     * @return bool - Trạng thái thành công của việc xóa
     */
    public function clearUserCart($user_id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$user_id]);
    }

    /**
     * Lấy tổng số lượng sản phẩm trong giỏ hàng của người dùng
     * @param int $user_id - ID của người dùng
     * @return int - Tổng số lượng sản phẩm
     */
    public function getCartItemCount($user_id)
    {
        $stmt = $this->conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0; // Trả về 0 nếu không có sản phẩm nào trong giỏ
    }
}
