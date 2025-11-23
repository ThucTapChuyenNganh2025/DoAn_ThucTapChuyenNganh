<?php
session_start();
include '../config/connect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
$my_id = $_SESSION['user_id'];

// 2. Kiểm tra ID sản phẩm
if (!isset($_GET['id'])) {
    header("Location: user_dashboard.php");
    exit;
}
$id = intval($_GET['id']);

// 3. Lấy dữ liệu sản phẩm
$sql_get = "SELECT * FROM products WHERE id = $id AND seller_id = $my_id";
$result = $conn->query($sql_get);

if ($result->num_rows == 0) {
    echo "<script>alert('Tin không tồn tại hoặc không thuộc quyền của bạn!'); window.location.href='user_dashboard.php';</script>";
    exit;
}
$row = $result->fetch_assoc();

// 4. Lấy ảnh thumbnail từ bảng product_images
$sql_img = "SELECT filename FROM product_images WHERE product_id = $id ORDER BY sort_order ASC LIMIT 1";
$img_row = $conn->query($sql_img)->fetch_assoc();

$img_src = (!empty($img_row['filename']))
            ? '../' . $img_row['filename']
            : 'https://via.placeholder.com/300x300?text=No+Image';

// 5. Khi nhấn nút cập nhật
if (isset($_POST['btn_update'])) {

    $title = $conn->real_escape_string($_POST['title']);
    $price = $_POST['price'];
    $desc  = $conn->real_escape_string($_POST['description']);
    $cate_id = $_POST['category_id'];

    // CHECK nếu upload ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

        $target_dir = "../uploads/";
        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

            $new_path = "uploads/" . $file_name;

            // Lấy ảnh cũ để xóa file cũ
            if (!empty($img_row['filename']) && file_exists("../" . $img_row['filename'])) {
                unlink("../" . $img_row['filename']);
            }

            // Cập nhật filename trong product_images
            $conn->query("
                UPDATE product_images 
                SET filename='$new_path' 
                WHERE product_id=$id 
                ORDER BY sort_order ASC LIMIT 1
            ");
        }
    }

    // Cập nhật thông tin sản phẩm (KHÔNG UPDATE IMAGE)
    $sql_update = "
        UPDATE products 
        SET title='$title', price='$price', description='$desc', category_id='$cate_id', status='pending'
        WHERE id=$id AND seller_id=$my_id
    ";

    if ($conn->query($sql_update) === TRUE) {
        echo "<script>
                alert('Cập nhật thành công! Tin sẽ chờ duyệt lại.');
                window.location.href='user_dashboard.php';
              </script>";
    } else {
        echo "Lỗi SQL: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Tin Đăng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .sidebar { height: 100vh; width: 250px; position: fixed; top: 0; left: 0; background: #fff; border-right: 1px solid #ddd; padding-top: 20px; }
        .sidebar a { padding: 15px 25px; font-size: 16px; color: #555; display: block; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background: #fff3cd; color: #ff9f43; border-left: 4px solid #ff9f43; }
        .main-content { margin-left: 250px; padding: 30px; }
        .card-custom { border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 5px solid #ff9f43; }
        .btn-cam { background-color: #ff9f43; color: white; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="brand text-center fw-bold fs-4 text-warning"><i class="fa-solid fa-store"></i> KÊNH NGƯỜI BÁN</div>
    <a href="user_dashboard.php" class="active"><i class="fa-solid fa-gauge me-2"></i> Tổng Quan</a>
    <a href="user_dangtin.php"><i class="fa-solid fa-pen-to-square me-2"></i> Đăng Tin Mới</a>
    <a href="user_quanlytin.php"><i class="fa-solid fa-list me-2"></i> Tin Đã Đăng</a>
</div>

<div class="main-content">
    <h3 class="mb-4 text-secondary">Chỉnh Sửa Tin Đăng</h3>

    <div class="card card-custom p-4">
        <h4 class="mb-3 text-warning"><i class="fa-solid fa-pen-to-square"></i> Cập Nhật Thông Tin</h4>

        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">

                    <label class="fw-bold">Tiêu đề</label>
                    <input type="text" name="title" class="form-control mb-3" required value="<?php echo $row['title']; ?>">

                    <label class="fw-bold">Giá bán (VNĐ)</label>
                    <input type="number" name="price" class="form-control mb-3" required value="<?php echo $row['price']; ?>">

                    <label class="fw-bold">Danh mục</label>
                    <select name="category_id" class="form-select mb-3" required>
                        <?php
                        $cats = $conn->query("SELECT * FROM categories");
                        while($c = $cats->fetch_assoc()) {
                            $sel = ($c['id'] == $row['category_id']) ? 'selected' : '';
                            echo "<option value='{$c['id']}' $sel>{$c['name']}</option>";
                        }
                        ?>
                    </select>

                    <label class="fw-bold">Mô tả</label>
                    <textarea name="description" class="form-control mb-3" rows="6"><?php echo $row['description']; ?></textarea>

                </div>

                <div class="col-md-4">
                    <label class="fw-bold mb-2">Ảnh hiện tại</label>
                    <div class="border rounded p-2 bg-light text-center">
                        <img src="<?php echo $img_src; ?>" class="img-fluid rounded" style="max-height:250px; object-fit:cover;">
                    </div>

                    <label class="fw-bold mt-3">Thay ảnh mới</label>
                    <input type="file" name="image" class="form-control mt-1">
                    <small class="text-muted">Để trống nếu giữ ảnh cũ.</small>
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" name="btn_update" class="btn btn-cam px-4 py-2">
                    <i class="fa-solid fa-floppy-disk me-2"></i> Lưu Thay Đổi
                </button>
            </div>

        </form>
    </div>
</div>

</body>
</html>
