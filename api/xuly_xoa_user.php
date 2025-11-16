<?php
include '../config/connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Không cho xóa ID số 1 (Thường là Super Admin)
    if($id == 1) {
        echo "<script>alert('Không thể xóa tài khoản Super Admin!'); window.location.href='admin_users.php';</script>";
        exit;
    }

    $sql = "DELETE FROM users WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../admin/admin_users.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>