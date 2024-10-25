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

    // Đăng ký người dùng mới
    public function register($name, $email, $password)
    {
        // Kiểm tra xem email đã tồn tại hay chưa
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            // Email đã tồn tại, trả về thông báo lỗi
            return "Email already exists. Please choose a different one.";
        }

        // Kiểm tra tính hợp lệ của dữ liệu
        if (empty($name) || empty($email) || empty($password)) {
            return "All fields are required.";
        }

        // Kiểm tra độ dài tối thiểu của mật khẩu
        if (strlen($password) < 6) {
            return "Password must be at least 6 characters long.";
        }

        // Nếu email chưa tồn tại, tiếp tục đăng ký
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashedPassword]);
            return "Registration successful!";
        } catch (PDOException $e) {
            // Xử lý lỗi nếu có
            return "Error occurred during registration: " . $e->getMessage();
        }
    }

    // Kiểm tra thông tin đăng nhập
    public function login($email, $password)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra mật khẩu
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Trả về thông tin người dùng nếu đăng nhập thành công
        }
        return false; // Sai thông tin đăng nhập
    }

    // Lấy thông tin người dùng dựa trên ID
    public function getUserById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
