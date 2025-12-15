<?php
// 1. Đổi localhost thành "db" (Tên này phải trùng với service mysql trong docker-compose.yml)
$servername = "db"; 

$username = "root";

// 2. Điền mật khẩu root mà bạn đã khai báo trong file docker-compose.yml
// (Thường là "123", "root" hoặc "password". Bạn kiểm tra lại file docker-compose nhé)
$password = "123"; 

$dbname = "ban_sach";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // echo "Kết nối thành công!"; // Bỏ comment dòng này nếu muốn test
} catch(PDOException $e) {
    // Trong môi trường Production (đi thi), nên ẩn lỗi chi tiết để bảo mật
    // Nhưng đang debug thì cứ để thế này cũng được
    die("Lỗi kết nối Database: " . $e->getMessage());
}
?>