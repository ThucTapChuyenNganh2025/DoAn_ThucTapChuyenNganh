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
    $location_id = isset($_POST['location_id']) && $_POST['location_id'] ? (int)$_POST['location_id'] : 'NULL';
    $seller_id = $_SESSION['user_id'];

    // 1) Thêm sản phẩm vào bảng `products`
    $sql_product = "INSERT INTO products (seller_id, category_id, location_id, title, description, price, status)
                    VALUES ('$seller_id', '$cate_id', $location_id, '$title', '$desc', '$price', 'pending')";

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

<style>
    .profile-page {
        padding: 40px 0;
        min-height: calc(100vh - 150px);
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    }
    
    /* Sidebar */
    .profile-sidebar {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        padding: 25px;
    }
    
    .sidebar-title {
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eee;
    }
    
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar-menu li {
        margin-bottom: 5px;
    }
    
    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: #555;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .sidebar-menu a:hover,
    .sidebar-menu a.active {
        background: #f6c23e;
        color: #1a1a2e;
        font-weight: 600;
    }
    
    .sidebar-menu a i {
        margin-right: 12px;
        width: 20px;
        text-align: center;
    }
    
    .sidebar-menu a.text-danger {
        color: #dc3545 !important;
    }
    
    .sidebar-menu a.text-danger:hover {
        background: #dc3545;
        color: #fff !important;
    }
    
    /* Form Card */
    .form-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .form-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
        padding: 30px;
        text-align: center;
    }
    
    .form-header h2 {
        color: #fff;
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }
    
    .form-header p {
        color: rgba(255,255,255,0.7);
        margin: 10px 0 0;
        font-size: 14px;
    }
    
    .form-body {
        padding: 40px;
    }
    
    .form-group {
        margin-bottom: 25px;
    }
    
    .form-label {
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 8px;
        display: block;
    }
    
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #f6c23e;
        box-shadow: 0 0 0 3px rgba(246, 194, 62, 0.15);
    }
    
    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }
    
    .btn-submit {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
        color: #1a1a2e;
        border: none;
        padding: 14px 30px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        width: 100%;
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        background: linear-gradient(45deg, #dda20a, #c99107);
        color: #1a1a2e;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(246, 194, 62, 0.4);
    }
    
    .upload-box {
        border: 2px dashed #ddd;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .upload-box:hover {
        border-color: #f6c23e;
        background: #fffef7;
    }
    
    .upload-box i {
        font-size: 40px;
        color: #ddd;
        margin-bottom: 10px;
    }
</style>

<div class="profile-page">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="profile-sidebar">
                    <h5 class="sidebar-title"><i class="fa-solid fa-user-gear me-2"></i>Menu</h5>
                    <ul class="sidebar-menu">
                        <li><a href="profile.php"><i class="fa-solid fa-user"></i> Hồ sơ cá nhân</a></li>
                        <li><a href="user_dashboard.php"><i class="fa-solid fa-gauge"></i> Tổng quan</a></li>
                        <li><a href="user_dangtin.php" class="active"><i class="fa-solid fa-plus"></i> Đăng tin mới</a></li>
                        <li><a href="user_quanlytin.php"><i class="fa-solid fa-list"></i> Quản lý tin</a></li>
                        <li><a href="doimk.php"><i class="fa-solid fa-key"></i> Đổi mật khẩu</a></li>
                        <li><a href="dangxuat.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="form-card">
                    <div class="form-header">
                        <h2><i class="fa-solid fa-camera me-2"></i>Đăng Bán Sản Phẩm</h2>
                        <p>Thêm sản phẩm mới để bán trên website</p>
                    </div>
                    
                    <div class="form-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="form-label"><i class="fa-solid fa-tag me-2" style="color: #f6c23e;"></i>Tên sản phẩm</label>
                                        <input type="text" name="title" class="form-control" required placeholder="Ví dụ: iPhone 15 Pro Max cũ...">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label"><i class="fa-solid fa-money-bill me-2" style="color: #f6c23e;"></i>Giá bán (VNĐ)</label>
                                                <input type="number" name="price" class="form-control" required placeholder="VD: 100000" min="1000" step="1000">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label"><i class="fa-solid fa-folder me-2" style="color: #f6c23e;"></i>Danh mục</label>
                                                <select name="category_id" class="form-select" required>
                                                    <option value="">-- Chọn danh mục --</option>
                                                    <?php
                                                    $cats = $conn->query("SELECT * FROM categories");
                                                    while($c = $cats->fetch_assoc()){
                                                        echo "<option value='".$c['id']."'>".$c['name']."</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label"><i class="fa-solid fa-location-dot me-2" style="color: #f6c23e;"></i>Tỉnh/Thành phố</label>
                                                <select name="location_id" id="provinceSelect" class="form-select" required>
                                                    <option value="">-- Chọn tỉnh/thành --</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label"><i class="fa-solid fa-align-left me-2" style="color: #f6c23e;"></i>Mô tả chi tiết</label>
                                        <textarea name="description" class="form-control" rows="5" placeholder="Mô tả tình trạng, xuất xứ..."></textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label"><i class="fa-solid fa-images me-2" style="color: #f6c23e;"></i>Hình ảnh sản phẩm</label>
                                        <div class="upload-box">
                                            <i class="fa-solid fa-cloud-arrow-up"></i>
                                            <p class="text-muted mb-2">Kéo thả hoặc click để chọn ảnh</p>
                                            <input type="file" name="image[]" multiple class="form-control" required>
                                        </div>
                                        <div class="mt-2 text-muted small">
                                            <i class="fa-solid fa-circle-info me-1"></i>Bạn có thể chọn nhiều hình cùng lúc.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="btn_dangtin" class="btn-submit">
                                <i class="fa-solid fa-paper-plane me-2"></i>ĐĂNG TIN NGAY
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load danh sách tỉnh/thành từ database
fetch('../favorites/get_locations.php')
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success' && data.locations) {
            const provinceSelect = document.getElementById('provinceSelect');
            // Lấy danh sách tỉnh duy nhất và lấy location_id đầu tiên của mỗi tỉnh
            const provinceMap = {};
            data.locations.forEach(l => {
                if (!provinceMap[l.province]) {
                    provinceMap[l.province] = l.id;
                }
            });
            
            const provinces = Object.keys(provinceMap).sort();
            provinces.forEach(p => {
                provinceSelect.innerHTML += `<option value="${provinceMap[p]}">${p}</option>`;
            });
        }
    })
    .catch(err => console.error('Lỗi load locations:', err));
</script>


