<?php
session_start();
include '../../config/connect.php';

// --- BẢO VỆ TRANG: CHỈ ADMIN MỚI CÓ QUYỀN TRUY CẬP ---
if (!isset($_SESSION['admin_id'])) {
    header("Location: dangnhapadmin.php");
    exit();
}

// Kiểm tra xem có ID sản phẩm và hành động được gửi lên không
if (isset($_GET['id']) && isset($_GET['action'])) {
    $product_id = (int)$_GET['id'];
    $action = $_GET['action'];

    // Xác định trạng thái mới dựa trên hành động
    $new_status = '';
    if ($action === 'approve') {
        $new_status = 'approved';
    } elseif ($action === 'reject') {
        $new_status = 'rejected';
    }

    // Nếu trạng thái mới hợp lệ, tiến hành cập nhật CSDL
    if (!empty($new_status)) {
        $stmt = $conn->prepare("UPDATE products SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $product_id);
        
        if ($stmt->execute()) {
            // Cập nhật thành công, quay lại trang duyệt tin
            header("Location: admin_duyettin.php");
            exit();
        } else {
            die("Lỗi khi cập nhật cơ sở dữ liệu: " . $stmt->error);
        }
    }
}

// Nếu không có ID hoặc action hợp lệ, quay về trang duyệt tin
header("Location: admin_duyettin.php");
exit();
?>