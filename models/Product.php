<?php

class Product
{
    private $conn;
    private $table = 'products';

    public $id;
    public $name;
    public $description;
    public $price;
    public $image;
    public $category_id;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // ------------------------------------------
    // Phương thức CRUD
    // ------------------------------------------

    /**
     * Thêm sản phẩm mới
     * @param string $name
     * @param string $description
     * @param float $price
     * @param string $image
     * @param int $category_id
     * @param float|null $discount
     * @param string|null $discount_end_time
     * @return bool
     */
    public function createProduct($name, $description, $price, $image, $category_id, $discount = null, $discount_end_time = null)
    {
        $query = "INSERT INTO " . $this->table . " (name, description, price, image, category_id, discount, discount_end_time) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $description, $price, $image, $category_id, $discount, $discount_end_time]);
    }

    /**
     * Lấy tất cả sản phẩm
     * @return array
     */
    public function getAllProducts()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy sản phẩm theo ID
     * @param int $id
     * @return array|null
     */
    public function getProductById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật sản phẩm
     * @param int $id
     * @param string $name
     * @param string $description
     * @param float $price
     * @param string $image
     * @param int $category_id
     * @param float|null $discount
     * @param string|null $discount_end_time
     * @return bool
     */
    public function updateProduct($id, $name, $description, $price, $image, $category_id, $discount, $discount_end_time)
    {
        $query = "UPDATE " . $this->table . " SET name = ?, description = ?, price = ?, image = ?,
                                            category_id = ?, discount = ?, discount_end_time = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            // In lỗi khi chuẩn bị câu lệnh không thành công
            echo "Failed to prepare statement: " . $this->conn->error;
            return false;
        }

        $result = $stmt->execute([$name, $description, $price, $image, $category_id, $discount, $discount_end_time, $id]);

        if (!$result) {
            // In lỗi khi thực thi câu lệnh không thành công
            echo "Failed to execute statement: " . $stmt->error;
        }

        return $result;
    }

    /**
     * Xóa sản phẩm
     * @param int $id
     * @return bool
     */
    public function deleteProduct($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // ------------------------------------------
    // Phương thức bổ sung
    // ------------------------------------------

    /**
     * Lấy 3 sản phẩm ngẫu nhiên
     * @param int $limit
     * @return array
     */
    public function getRandomProducts($limit = 3)
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY RAND() LIMIT " . intval($limit);
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy sản phẩm đang giảm giá với thời gian còn lại
     * @return array
     */
    public function getDiscountProduct()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE discount IS NOT NULL AND discount_end_time > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách tất cả các danh mục
     * @return array
     */
    public function getCategories()
    {
        $query = "SELECT * FROM categories";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy sản phẩm theo danh mục
     * @param int $category_id
     * @return array
     */
    public function getProductsByCategory($category_id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm sản phẩm theo từ khóa
     * @param string $searchTerm
     * @return array
     */
    public function searchProducts($searchTerm)
    {
        $query = "SELECT * FROM products 
                    WHERE name LIKE :searchTerm
                    OR id LIKE :searchTerm
                    OR description LIKE :searchTerm 
                    OR price LIKE :searchTerm 
                    OR discount LIKE :searchTerm";
        $stmt = $this->conn->prepare($query);
        // Thêm ký tự "%" vào từ khóa để tìm kiếm bất kỳ từ nào có chứa $searchTerm
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách các danh mục khác nhau
     * @return array
     */
    public function getDistinctCategories()
    {
        $query = "SELECT DISTINCT id, name FROM categories";
        $stmt = $this->conn->query($query);
        $categories = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $row;
        }

        return $categories;
    }
}
