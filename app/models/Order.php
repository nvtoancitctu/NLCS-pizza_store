<?php

class Order
{
    private $conn;
    private $table = 'orders';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Tạo đơn hàng mới và lưu vào cơ sở dữ liệu
     *
     * @param int $user_id - ID người dùng
     * @param array $items - Mảng các sản phẩm trong đơn hàng
     * @param string $payment_method - Phương thức thanh toán
     * @param string $address - Địa chỉ giao hàng
     * @return int - ID của đơn hàng vừa tạo
     * @throws InvalidArgumentException - Nếu $items không phải là mảng
     */
    public function createOrder($user_id, $items, $payment_method, $address)
    {
        // Kiểm tra xem $items có phải là mảng không
        if (!is_array($items)) {
            throw new InvalidArgumentException('Items must be an array');
        }

        // Tính tổng tiền đơn hàng
        $total = 0;

        // Thêm các sản phẩm vào đơn hàng
        foreach ($items as $item) {
            // Lấy giá từ bảng products
            $productPrice = $this->getProductPrice($item['product_id']);
            $total += $productPrice * $item['quantity'];
        }

        // Thực hiện truy vấn để tạo đơn hàng
        $query = "INSERT INTO " . $this->table . " (user_id, total, payment_method, address) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $total, $payment_method, $address]);

        // Lấy ID đơn hàng vừa được tạo
        $order_id = $this->conn->lastInsertId();

        // Thêm các sản phẩm vào đơn hàng và cập nhật giá
        foreach ($items as $item) {
            $productPrice = $this->getProductPrice($item['product_id']);
            $this->addOrderItem($order_id, $item['product_id'], $item['quantity'], $productPrice);
            $this->updateProductPriceInOrderItems($order_id, $item['product_id'], $productPrice);
        }

        return $order_id;
    }

    /**
     * Thêm sản phẩm vào đơn hàng
     *
     * @param int $order_id - ID đơn hàng
     * @param int $product_id - ID sản phẩm
     * @param int $quantity - Số lượng sản phẩm
     * @param float $price - Giá sản phẩm
     * @return bool - Trạng thái thành công của thao tác
     */
    public function addOrderItem($order_id, $product_id, $quantity, $price)
    {
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$order_id, $product_id, $quantity, $price]);
    }

    /**
     * Cập nhật giá từng sản phẩm trong bảng order_items
     *
     * @param int $order_id - ID đơn hàng
     * @param int $product_id - ID sản phẩm cần cập nhật
     * @param float $price - Giá cập nhật cho sản phẩm
     * @return void
     */
    public function updateProductPriceInOrderItems($order_id, $product_id, $price)
    {
        $updateQuery = "UPDATE order_items 
                        SET price = :price 
                        WHERE order_id = :order_id AND product_id = :product_id";

        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bindParam(':price', $price, PDO::PARAM_STR);
        $updateStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $updateStmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $updateStmt->execute();
    }

    /**
     * Lấy giá sản phẩm, kiểm tra xem có giá giảm hay không
     *
     * @param int $product_id - ID sản phẩm
     * @return float - Giá của sản phẩm (có thể là giá gốc hoặc giá giảm)
     */
    public function getProductPrice($product_id)
    {
        $query = "SELECT price, discount, discount_end_time 
                  FROM products 
                  WHERE id = :product_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra xem sản phẩm có tồn tại hay không
        if (!$product) {
            throw new Exception("Product not found.");
        }

        $originalPrice = $product['price'];
        $discount = $product['discount'];
        $discountEndTime = $product['discount_end_time'];

        // Nếu có giảm giá và thời gian giảm giá còn hiệu lực, lấy giá giảm
        if ($discount > 0 && ($discountEndTime === null || $discountEndTime >= date('Y-m-d H:i:s'))) {
            return $discount; // Trả về giá giảm
        }

        return $originalPrice; // Trả về giá gốc
    }

    /**
     * Lấy chi tiết đơn hàng bao gồm các sản phẩm và tổng giá trị
     * @param int $order_id - ID đơn hàng
     * @param int $user_id - ID người dùng
     * @return array|null - Mảng chi tiết đơn hàng hoặc null nếu không tìm thấy
     */
    public function getOrderDetails($order_id, $user_id)
    {
        $query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $query = "SELECT 
                    oi.product_id, 
                    p.name, 
                    p.image, 
                    oi.quantity, 
                    p.price,
                    oi.price AS price_to_display,
                    (oi.price * oi.quantity) AS total_price

                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$order_id]);
        $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $orderTotal = array_sum(array_column($orderItems, 'total_price'));

        $order['items'] = $orderItems;
        $order['total'] = $orderTotal;

        return $order;
    }

    /**
     * Lấy danh sách đơn hàng của người dùng
     * @param int $user_id - ID người dùng
     * @return array - Danh sách đơn hàng
     */
    public function getOrdersByUserId($user_id)
    {
        $query = "SELECT * FROM orders WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết mặt hàng trong đơn hàng theo ID đơn hàng
     * @param int $order_id - ID đơn hàng
     * @return array - Danh sách mặt hàng trong đơn hàng
     */
    public function getOrderDetailsByOrderId($order_id)
    {
        $query_order_items = "SELECT 
                                p.name,
                                p.price,
                                oi.quantity,
                                oi.price AS price_to_display,
                                (oi.price * oi.quantity) AS total_price 
                            FROM 
                                order_items oi
                            JOIN 
                                products p ON oi.product_id = p.id 
                            WHERE 
                                oi.order_id = :order_id";

        $stmt_items = $this->conn->prepare($query_order_items);
        $stmt_items->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt_items->execute();

        return $stmt_items->fetchAll(PDO::FETCH_ASSOC);
    }
}
