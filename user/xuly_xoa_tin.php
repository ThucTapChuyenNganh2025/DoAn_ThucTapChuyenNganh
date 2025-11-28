<?php
session_start();
include '../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); // Hoặc trang login
    exit;
}

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $seller_id = $_SESSION['user_id'];

    // 1. Kiểm tra quyền sở hữu (QUAN TRỌNG)
    // Chỉ xóa nếu ID sản phẩm khớp VÀ người bán đúng là người đang đăng nhập
    $check_sql = "SELECT id FROM products WHERE id = $product_id AND seller_id = $seller_id";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        // Lấy tất cả ảnh liên quan để xóa file ảnh
        $img_sql = "SELECT filename FROM product_images WHERE product_id = $product_id";
        $img_result = $conn->query($img_sql);

        // 2. Xóa trong Database - xóa từ product_images trước (vì có foreign key)
        $del_img_sql = "DELETE FROM product_images WHERE product_id = $product_id";
        $conn->query($del_img_sql);

        // 3. Xóa sản phẩm
        $del_sql = "DELETE FROM products WHERE id = $product_id";
        if ($conn->query($del_sql) === TRUE) {
            
            // 4. Xóa file ảnh trong thư mục uploads (dọn rác)
            if ($img_result->num_rows > 0) {
                while($img_row = $img_result->fetch_assoc()) {
                    $image_path = "../" . $img_row['filename'];
                    if (!empty($img_row['filename']) && file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            }

            // Quay lại trang dashboard báo thành công
            echo "<script>alert('Đã xóa tin thành công!'); window.location.href='user_dashboard.php';</script>";
        } else {
            echo "Lỗi Database: " . $conn->error;
        }
    } else {
        echo "<script>alert('Bạn không có quyền xóa tin này hoặc tin không tồn tại!'); window.location.href='user_dashboard.php';</script>";
    }
} else {
    header("Location: user_dashboard.php");
}
?>