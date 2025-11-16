<?php
session_start();
include '../../config/connect.php';

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
    $stmt_check = $conn->prepare("SELECT id FROM products WHERE id = ? AND seller_id = ?");
    $stmt_check->bind_param("ii", $product_id, $seller_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $stmt_check->close();

    if ($result->num_rows > 0) {
        // Lấy tất cả ảnh liên quan để xóa file ảnh
        $stmt_img = $conn->prepare("SELECT filename FROM product_images WHERE product_id = ?");
        $stmt_img->bind_param("i", $product_id);
        $stmt_img->execute();
        $img_result = $stmt_img->get_result();
        $stmt_img->close();

        // 2. Xóa trong Database - xóa từ product_images trước (vì có foreign key)
        $stmt_del_img = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
        $stmt_del_img->bind_param("i", $product_id);
        $stmt_del_img->execute();
        $stmt_del_img->close();

        // 3. Xóa sản phẩm
        $stmt_del_prod = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt_del_prod->bind_param("i", $product_id);
        if ($stmt_del_prod->execute()) {
            
            // 4. Xóa file ảnh trong thư mục uploads (dọn rác)
            if ($img_result->num_rows > 0) {
                while($img_row = $img_result->fetch_assoc()) {
                    $image_path = "../../resources/views/" . $img_row['filename'];
                    if (!empty($img_row['filename']) && file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            }

            // Quay lại trang dashboard báo thành công
            echo "<script>alert('Đã xóa tin thành công!'); window.location.href='../../resources/views/user/user_dashboard.php';</script>";
        } else {
            echo "Lỗi Database: " . $conn->error;
        }
    } else {
        echo "<script>alert('Bạn không có quyền xóa tin này hoặc tin không tồn tại!'); window.location.href='../../resources/views/user/user_dashboard.php';</script>";
    }
} else {
    header("Location: ../../resources/views/user/user_dashboard.php");
}
?>