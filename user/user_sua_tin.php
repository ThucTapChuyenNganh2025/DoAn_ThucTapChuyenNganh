<?php
session_start();
include '../config/connect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
$my_id = $_SESSION['user_id'];

// 2. Kiểm tra ID
if (!isset($_GET['id'])) {
    header("Location: user_dashboard.php"); // Nếu lỗi ID thì về Dashboard luôn
    exit;
}
$id = $_GET['id'];

// 3. Lấy dữ liệu cũ
$sql_get = "SELECT * FROM products WHERE id = $id AND seller_id = $my_id";
$result = $conn->query($sql_get);

if ($result->num_rows == 0) {
    echo "<script>alert('Tin không tồn tại hoặc bạn không có quyền sửa!'); window.location.href='user_dashboard.php';</script>";
    exit;
}
$row = $result->fetch_assoc();


// 4. XỬ LÝ LƯU VÀ QUAY VỀ DASHBOARD
if (isset($_POST['btn_update'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $price = $_POST['price'];
    $desc = $conn->real_escape_string($_POST['description']);
    $cate_id = $_POST['category_id'];

    // Nếu có upload ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = time() . "_" . basename($_FILES["image"]["name"]); 
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_db = "uploads/" . $file_name;
            
            // Xóa ảnh cũ từ product_images
            $sql_delete_old = "DELETE FROM product_images WHERE product_id = $id";
            $conn->query($sql_delete_old);
            
            // Thêm ảnh mới vào product_images
            $sql_insert_image = "INSERT INTO product_images (product_id, filename, sort_order) 
                                 VALUES ('$id', '$image_db', '0')";
            $conn->query($sql_insert_image);
        }
    }

    // Cập nhật thông tin sản phẩm (không có cột image)
    $sql_update = "UPDATE products 
                   SET title='$title', price='$price', description='$desc', category_id='$cate_id', status='pending' 
                   WHERE id=$id AND seller_id=$my_id";

    if ($conn->query($sql_update) === TRUE) {
        //QUAY VỀ USER_DASHBOARD.PHP ---
        echo "<script>
                alert('Cập nhật thành công! Tin đang chờ duyệt lại.'); 
                window.location.href='user_dashboard.php'; 
              </script>";
    } else {
        echo "Lỗi: " . $conn->error;
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
        
        .sidebar { 
            height: 100vh; width: 250px; position: fixed; top: 0; left: 0; 
            background-color: #ffffff; border-right: 1px solid #e0e0e0; padding-top: 20px; color: #333;
        }
        .sidebar a { 
            padding: 15px 25px; text-decoration: none; font-size: 16px; color: #555; 
            display: block; transition: 0.3s; 
        }
        .sidebar a:hover, .sidebar a.active { 
            background-color: #fff3cd; color: #ff9f43; border-left: 4px solid #ff9f43; font-weight: bold; 
        }
        .sidebar .brand { 
            text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 30px; color: #ff9f43; 
        }
        
        .main-content { margin-left: 250px; padding: 30px; }
        
        .card-custom { 
            border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
            border-top: 5px solid #ff9f43; 
        }
        .btn-cam { background-color: #ff9f43; color: white; border: none; font-weight: bold; }
        .btn-cam:hover { background-color: #e08e0b; color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-store"></i> KÊNH NGƯỜI BÁN</div>
    <a href="user_dashboard.php" class="active"><i class="fa-solid fa-gauge me-2"></i> Tổng Quan</a>
    <a href="user_dangtin.php"><i class="fa-solid fa-pen-to-square me-2"></i> Đăng Tin Mới</a>
    <a href="user_quanlytin.php"><i class="fa-solid fa-list me-2"></i> Tin Đã Đăng</a>
    <a href="#"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng Xuất</a>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-secondary">Chỉnh Sửa Tin Đăng</h3>
        <a href="user_dashboard.php" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Về Tổng Quan
        </a>
    </div>

    <div class="card card-custom p-4">
        <h4 class="mb-4 text-warning"><i class="fa-solid fa-pen-to-square"></i> Cập Nhật Thông Tin</h4>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="fw-bold">Tiêu đề tin</label>
                        <input type="text" name="title" class="form-control" required value="<?php echo $row['title']; ?>">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Giá bán (VNĐ)</label>
                            <input type="number" name="price" class="form-control" required 
                                   value="<?php echo $row['price']; ?>" 
                                   min="1000" step="1000" 
                                   oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Danh mục</label>
                            <select name="category_id" class="form-select" required>
                                <?php
                                $cats = $conn->query("SELECT * FROM categories");
                                while($c = $cats->fetch_assoc()) {
                                    $selected = ($c['id'] == $row['category_id']) ? 'selected' : '';
                                    echo "<option value='".$c['id']."' $selected>".$c['name']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="fw-bold">Mô tả chi tiết</label>
                        <textarea name="description" class="form-control" rows="6" required><?php echo $row['description']; ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="fw-bold mb-2">Ảnh hiện tại</label><br>
                        <?php 
                            // Lấy ảnh từ bảng product_images
                            $sql_img = "SELECT filename FROM product_images WHERE product_id = $id LIMIT 1";
                            $res_img = $conn->query($sql_img);
                            $img_row = $res_img->fetch_assoc();
                            $img_src = ($img_row && !empty($img_row['filename'])) ? '../' . $img_row['filename'] : 'https://via.placeholder.com/300x300?text=No+Image';
                        ?>
                        <div class="text-center border rounded p-2 bg-light">
                            <img src="<?php echo $img_src; ?>" class="img-fluid rounded" style="max-height: 250px; object-fit: cover;">
                        </div>
                        
                        <div class="mt-3">
                            <label class="fw-bold">Thay ảnh mới (Nếu muốn)</label>
                            <input type="file" name="image" class="form-control mt-1" accept="image/*">
                            <div class="text-muted small mt-1"><i class="fa-solid fa-circle-info"></i> Để trống nếu muốn giữ ảnh cũ.</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-end gap-2">
                <a href="user_dashboard.php" class="btn btn-secondary px-4">Hủy bỏ</a>
                <button type="submit" name="btn_update" class="btn btn-cam px-4 py-2">
                    <i class="fa-solid fa-floppy-disk me-2"></i> LƯU THAY ĐỔI
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>