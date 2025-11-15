<?php
include 'connect.php';

// Kiểm tra xem có ID sản phẩm gửi lên không
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Lệnh SQL xóa sản phẩm theo ID
    $sql = "DELETE FROM products WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Xóa thành công thì quay lại trang danh sách
        header("Location: admin_sanpham.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
} else {
    // Nếu không có ID thì đá về trang chủ
    header("Location: admin_sanpham.php");
}
?>