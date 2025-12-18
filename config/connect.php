<?php
$db_host = "localhost";
$db_user = "root";       // Mặc định XAMPP user là root
$db_pass = "";           // Mặc định XAMPP pass là rỗng
$db_name = "webchotot";  

// Tạo kết nối
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Kiểm tra kết nối
if ($conn->connect_error) {
  die("Kết nối thất bại: " . $conn->connect_error);
}
// Dòng dưới để hiển thị tiếng Việt không bị lỗi font
mysqli_set_charset($conn, 'UTF8');
?>