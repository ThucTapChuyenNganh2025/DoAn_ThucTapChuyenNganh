<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sản Phẩm - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php
include '../config/connect.php';

// 1. LẤY DỮ LIỆU CŨ ĐỂ HIỆN LÊN FORM
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    if(!$row) {
        echo "Không tìm thấy sản phẩm!"; exit;
    }
}

// 2. XỬ LÝ KHI BẤM NÚT LƯU
if (isset($_POST['btn_luu'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    // Cập nhật vào Database
    $sql_update = "UPDATE products SET title='$title', price='$price', status='$status' WHERE id=$id";
    
    if ($conn->query($sql_update) === TRUE) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href='admin_sanpham.php';</script>";
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Chỉnh Sửa Sản Phẩm #<?php echo $row['id']; ?></h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên Sản Phẩm</label>
                            <input type="text" name="title" class="form-control" value="<?php echo $row['title']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Giá Bán (VNĐ)</label>
                            <input type="number" name="price" class="form-control" value="<?php echo $row['price']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng Thái</label>
                            <select name="status" class="form-select">
                                <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Chờ duyệt (Pending)</option>
                                <option value="approved" <?php if($row['status']=='approved') echo 'selected'; ?>>Đã duyệt (Approved)</option>
                                <option value="rejected" <?php if($row['status']=='rejected') echo 'selected'; ?>>Từ chối (Rejected)</option>
                                <option value="hidden" <?php if($row['status']=='hidden') echo 'selected'; ?>>Đã ẩn (Hidden)</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="btn_luu" class="btn btn-primary">Lưu Thay Đổi</button>
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