<?php
$servername = "localhost";
$username = "root";      // Mặc định XAMPP user là root
$password = "";          // Mặc định XAMPP pass là rỗng
$dbname = "webchotot";  

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
  die("Kết nối thất bại: " . $conn->connect_error);
}
// Dòng dưới để hiển thị tiếng Việt không bị lỗi font
mysqli_set_charset($conn, 'UTF8');
?>