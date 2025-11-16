<?php
include '../config/connect.php'; // Ra ngoài 1 cấp, vào config

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        // Xóa xong quay về admin (Ra ngoài 1 cấp, vào admin)
        header("Location: ../admin/admin_sanpham.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>