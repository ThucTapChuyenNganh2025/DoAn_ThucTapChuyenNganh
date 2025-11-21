<?php
session_start();
include '../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Không có ID sản phẩm");
}

$product_id = intval($_GET['id']);

// 1) Kiểm tra sản phẩm có thuộc user không
$check_sql = "SELECT id FROM products WHERE id = $product_id AND seller_id = $seller_id";
$check = $conn->query($check_sql);

if ($check->num_rows == 0) {
    echo "<script>alert('Bạn không có quyền xoá sản phẩm này!'); window.location.href='user_quanlytin.php';</script>";
    exit;
}

// 2) Lấy danh sách ảnh từ product_images
$sql_img = "SELECT filename FROM product_images WHERE product_id = $product_id";
$imgs = $conn->query($sql_img);

// 3) Xoá file ảnh thật trên server
while ($row = $imgs->fetch_assoc()) {
    $path = "../" . $row['filename']; // VD: ../uploads/xxxxx.jpg

    if (file_exists($path)) {
        unlink($path);
    }
}

// 4) Xoá ảnh trong bảng product_images
$conn->query("DELETE FROM product_images WHERE product_id = $product_id");

// 5) Xoá sản phẩm khỏi bảng products
$conn->query("DELETE FROM products WHERE id = $product_id");

// 6) Điều hướng về trang quản lý tin
echo "<script>alert('Xoá tin thành công!'); window.location.href='user_quanlytin.php';</script>";
exit;
?>
