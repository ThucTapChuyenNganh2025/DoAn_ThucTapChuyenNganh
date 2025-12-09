<?php
session_start();
include '../config/connect.php';

// Tạm thời set user_id để test
if(!isset($_SESSION['user_id'])) { 
    $_SESSION['user_id'] = 1; 
}

if(isset($_POST['btn_dangtin'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $price = $_POST['price'];
    $desc = $conn->real_escape_string($_POST['description']);
    $cate_id = $_POST['category_id'];
    $seller_id = $_SESSION['user_id'];

    // 1) Thêm sản phẩm vào bảng `products`
    $sql_product = "INSERT INTO products (seller_id, category_id, title, description, price, status)
                    VALUES ('$seller_id', '$cate_id', '$title', '$desc', '$price', 'pending')";

    if ($conn->query($sql_product) === TRUE) {
        // Lấy ID sản phẩm vừa tạo
        $product_id = $conn->insert_id;

        // Thiết lập thư mục uploads
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        // Xử lý upload nhiều ảnh (nếu có)
        $sort = 0;
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $names = is_array($_FILES['image']['name']) ? $_FILES['image']['name'] : array($_FILES['image']['name']);
            $tmps = is_array($_FILES['image']['tmp_name']) ? $_FILES['image']['tmp_name'] : array($_FILES['image']['tmp_name']);
            $errors = is_array($_FILES['image']['error']) ? $_FILES['image']['error'] : array($_FILES['image']['error']);

            foreach ($names as $key => $name) {
                if (empty($name) || (isset($errors[$key]) && $errors[$key] !== 0)) continue;

                $tmp = $tmps[$key];
                $new_name = time() . "_" . mt_rand(1000,9999) . "_" . basename($name);
                $target_file = $target_dir . $new_name;
                $db_path = "uploads/" . $new_name;

                if (move_uploaded_file($tmp, $target_file)) {
                    $sql_img = "INSERT INTO product_images (product_id, filename, sort_order)
                                VALUES ('$product_id', '$db_path', '$sort')";
                    $conn->query($sql_img);
                    $sort++;
                }
            }
        }

        echo "<script>alert('Đăng tin thành công!'); window.location.href='user_dashboard.php';</script>";
    } else {
        echo "Lỗi khi thêm sản phẩm: " . $conn->error;
    }
}
?>

<?php include_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <aside class="seller-aside">
                <div class="text-center mb-3 brand"><i class="fa-solid fa-store me-2"></i>Đăng Tin</div>
                <ul class="list-unstyled">
                    <li><a href="user_dashboard.php">Tổng Quan</a></li>
                    <li><a href="user_dangtin.php" class="active">Đăng Tin</a></li>
                    <li><a href="user_quanlytin.php">Tin Đã Đăng</a></li>
                </ul>
            </aside>
        </div>

        <div class="col-lg-9">
            <div class="card card-custom p-4" style="border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.06);">
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
                            <label>Giá bán (VNĐ)</label>
                            <input type="number" name="price" class="form-control" required placeholder="VD: 100000" min="1000" step="1000">
                        </div>
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
                        <label>Hình ảnh (có thể chọn nhiều)</label>
                        <input type="file" name="image[]" multiple class="form-control" required>
                        <div class="mt-2 text-muted small">Bạn có thể chọn nhiều hình cùng lúc.</div>
                    </div>
                </div>
            </div>
            
            <button type="submit" name="btn_dangtin" class="btn btn-cam w-100 py-2">ĐĂNG TIN NGAY</button>
        </form>
            </div>
        </div>
    </div>
    </div>

<?php include_once dirname(__DIR__) . '/includes/footer.php'; ?>
