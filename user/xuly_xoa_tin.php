<?php
session_start();
include '../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// Kiểm tra tham số
if (!isset($_GET['id'])) {
    header("Location: user_quanlytin.php");
    exit;
}

$product_id = intval($_GET['id']);

// 1) Kiểm tra sản phẩm có thuộc user không
$check_sql = "SELECT id FROM products WHERE id = $product_id AND seller_id = $seller_id";
$check = $conn->query($check_sql);

if (!$check || $check->num_rows == 0) {
    echo '<!DOCTYPE html><html><head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="../js/toast.js"></script>
    </head><body>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            toastAndRedirect("Bạn không có quyền xoá sản phẩm này!", "error", "user_quanlytin.php", 2000);
        });
    </script></body></html>';
    exit;
}

// 2) Lấy danh sách ảnh từ product_images
$sql_img = "SELECT filename FROM product_images WHERE product_id = $product_id";
$imgs = $conn->query($sql_img);

// 3) Xoá file ảnh thật trên server
if ($imgs && $imgs->num_rows > 0) {
    while ($row = $imgs->fetch_assoc()) {
        $path = "../" . $row['filename']; // VD: ../uploads/xxxxx.jpg
        if (file_exists($path)) {
            @unlink($path);
        }
    }
}

// 4) Xoá ảnh trong bảng product_images
$conn->query("DELETE FROM product_images WHERE product_id = $product_id");

// 5) Xoá sản phẩm khỏi bảng products
$conn->query("DELETE FROM products WHERE id = $product_id");

// 6) Điều hướng về trang quản lý tin
echo '<!DOCTYPE html><html><head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../js/toast.js"></script>
</head><body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        toastAndRedirect("Xoá tin thành công!", "success", "user_quanlytin.php", 1500);
    });
</script></body></html>';
exit;
?>
