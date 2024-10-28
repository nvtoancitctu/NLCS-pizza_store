<?php

class User
{
    private $conn;
    private $table = 'users';

    public $id;
    public $name;
    public $email;
    public $password;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Đăng ký người dùng mới
     * @param string $name - Tên người dùng
     * @param string $email - Địa chỉ email
     * @param string $password - Mật khẩu
     * @return string - Thông báo trạng thái
     */
    public function register($name, $email, $password)
    {
        // Kiểm tra email đã tồn tại
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            return "Email already exists.";
        }

        // Kiểm tra tính hợp lệ của dữ liệu
        if (empty($name) || empty($email) || empty($password)) {
            return "All fields are required.";
        }

        if (strlen($password) < 6) {
            return "Password must be at least 6 characters long.";
        }

        // Mã hóa mật khẩu và thực hiện đăng ký
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashedPassword]);
            return "Registration successful!";
        } catch (PDOException $e) {
            return "Error during registration: " . $e->getMessage();
        }
    }

    /**
     * Kiểm tra thông tin đăng nhập
     * @param string $email - Địa chỉ email
     * @param string $password - Mật khẩu
     * @return mixed - Thông tin người dùng nếu thành công, false nếu thất bại
     */
    public function login($email, $password)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Lấy thông tin người dùng theo ID
     * @param int $id - ID người dùng
     * @return array|null - Thông tin người dùng hoặc null
     */
    public function getUserById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Trả về thông tin người dùng
    }
}
