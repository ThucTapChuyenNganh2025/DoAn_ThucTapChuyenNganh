<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản Phẩm Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php
include '../config/connect.php';

// 1. XỬ LÝ KHI BẤM NÚT "THÊM MỚI"
if (isset($_POST['btn_them'])) {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $seller_id = $_POST['seller_id']; // Admin chọn người bán
    $category_id = $_POST['category_id']; // Admin chọn danh mục
    $status = 'approved'; // Admin thêm thì cho duyệt luôn

    // Câu lệnh Insert
    $sql = "INSERT INTO products (title, price, seller_id, category_id, status, created_at) 
            VALUES ('$title', '$price', '$seller_id', '$category_id', '$status', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Thêm sản phẩm thành công!'); window.location.href='admin_sanpham.php';</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Thêm Sản Phẩm Mới</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên Sản Phẩm</label>
                            <input type="text" name="title" class="form-control" placeholder="Ví dụ: iPhone 15 Pro Max" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá Bán (VNĐ)</label>
                                <input type="number" name="price" class="form-control" placeholder="Nhập giá tiền" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh Mục</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php
                                    // Lấy danh mục từ database đổ vào đây
                                    $cats = $conn->query("SELECT * FROM categories");
                                    while($c = $cats->fetch_assoc()){
                                        echo "<option value='".$c['id']."'>".$c['name']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Người Bán (Chủ sở hữu)</label>
                            <select name="seller_id" class="form-select" required>
                                <option value="">-- Chọn thành viên đăng bán --</option>
                                <?php
                                // Lấy danh sách user để admin chọn giùm
                                $users = $conn->query("SELECT * FROM users");
                                while($u = $users->fetch_assoc()){
                                    echo "<option value='".$u['id']."'>".$u['name']." (".$u['email'].")</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="btn_them" class="btn btn-success">Xác Nhận Thêm</button>
                            <a href="admin_sanpham.php" class="btn btn-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>