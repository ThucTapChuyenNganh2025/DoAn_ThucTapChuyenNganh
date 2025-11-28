<?php
session_start();
include '../config/connect.php';
if(!isset($_SESSION['user_id'])) { $_SESSION['user_id'] = 1; }

if(isset($_POST['btn_dangtin'])) {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $cate_id = $_POST['category_id'];
    $seller_id = $_SESSION['user_id'];
    
    // Xử lý Upload ảnh
    $target_dir = "../uploads/"; // Lưu vào thư mục uploads ở ngoài (cùng cấp với user/)
    
    // Tạo thư mục nếu chưa có
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_name = basename($_FILES["image"]["name"]);
    // Đặt tên file ngẫu nhiên để tránh trùng (dùng timestamp)
    $target_file = $target_dir . time() . "_" . $file_name;
    
    // Đường dẫn để lưu vào DB (bỏ ../ đi để sau này hiển thị cho dễ)
    $db_image_path = "uploads/" . time() . "_" . $file_name;

    if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Lưu sản phẩm vào DB
        $sql = "INSERT INTO products (seller_id, category_id, title, description, price, status) 
                VALUES ('$seller_id', '$cate_id', '$title', '$desc', '$price', 'pending')";
        
        if($conn->query($sql) === TRUE) {
            // Lấy ID sản phẩm vừa tạo
            $product_id = $conn->insert_id;
            
            // Lưu ảnh vào bảng product_images
            $sql_image = "INSERT INTO product_images (product_id, filename, sort_order) 
                          VALUES ('$product_id', '$db_image_path', '0')";
            
            if($conn->query($sql_image) === TRUE) {
                echo "<script>alert('Đăng tin thành công!'); window.location.href='user_dashboard.php';</script>";
            } else {
                echo "Lỗi lưu ảnh: " . $conn->error;
            }
        } else {
            echo "Lỗi DB: " . $conn->error;
        }
    } else {
        echo "<script>alert('Lỗi upload ảnh!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Tin Mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Sidebar giống file dashboard */
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .sidebar { height: 100vh; width: 250px; position: fixed; top: 0; left: 0; background-color: #ffffff; border-right: 1px solid #e0e0e0; padding-top: 20px; }
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 16px; color: #555; display: block; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: #fff3cd; color: #ff9f43; border-left: 4px solid #ff9f43; font-weight: bold; }
        .sidebar .brand { text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 30px; color: #ff9f43; }
        .main-content { margin-left: 250px; padding: 30px; }
        
        .card-custom { border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-cam { background-color: #ff9f43; color: white; border: none; font-weight: bold; }
        .btn-cam:hover { background-color: #e08e0b; color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-store"></i> KÊNH NGƯỜI BÁN</div>
    <a href="user_dashboard.php"><i class="fa-solid fa-gauge me-2"></i> Tổng Quan</a>
    <a href="user_dangtin.php" class="active"><i class="fa-solid fa-pen-to-square me-2"></i> Đăng Tin Mới</a>
    <a href="user_quanlytin.php"><i class="fa-solid fa-list me-2"></i> Tin Đã Đăng</a>
    <a href="#" class="text-danger mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng Xuất</a>
</div>

<div class="main-content">
    <div class="card card-custom p-4">
        <h4 class="mb-4 text-warning"><i class="fa-solid fa-camera"></i> Đăng Bán Sản Phẩm</h4>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label>Tên sản phẩm</label>
                        <input type="text" name="title" class="form-control" required placeholder="Ví dụ: iPhone 15 Pro Max cũ...">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Giá bán (VNĐ)</label><input type="number" name="price" class="form-control" required placeholder="Nhập giá tiền (VD: 100000)" min="1000" step="1000" oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null">
                        <div class="col-md-6 mb-3">
                            <label>Danh mục</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn --</option>
                                <?php
                                $cats = $conn->query("SELECT * FROM categories");
                                while($c = $cats->fetch_assoc()){
                                    echo "<option value='".$c['id']."'>".$c['name']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Mô tả chi tiết</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Mô tả tình trạng, xuất xứ..."></textarea>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label>Hình ảnh (Bắt buộc)</label>
                        <input type="file" name="image" class="form-control" required accept="image/*">
                        <div class="mt-2 text-muted small">Chọn ảnh đẹp, rõ nét để bán nhanh hơn.</div>
                    </div>
                </div>
            </div>
            
            <button type="submit" name="btn_dangtin" class="btn btn-cam w-100 py-2">ĐĂNG TIN NGAY</button>
        </form>
    </div>
</div>

</body>
</html>