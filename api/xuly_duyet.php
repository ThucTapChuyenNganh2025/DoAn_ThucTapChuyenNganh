<?php
include '../config/connect.php';

// Lấy ID và Hành động từ đường link (URL)
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    $status = '';

    // Kiểm tra xem bấm nút nào
    if ($action == 'approve') {
        $status = 'approved';
    } elseif ($action == 'reject') {
        $status = 'rejected';
    }

    // Cập nhật vào database
    if ($status != '') {
        $sql = "UPDATE products SET status = '$status' WHERE id = $id";
        
        if ($conn->query($sql) === TRUE) {
            // Cập nhật xong thì quay lại trang danh sách
            header("Location: ../admin/admin_duyettin.php");
        } else {
            echo "Lỗi: " . $conn->error;
        }
    }
}
?>