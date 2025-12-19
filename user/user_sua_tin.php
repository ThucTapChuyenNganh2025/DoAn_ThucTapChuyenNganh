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
    echo '<!DOCTYPE html><html><head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="../js/toast.js"></script>
    </head><body>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            toastAndRedirect("Tin không tồn tại hoặc không thuộc quyền của bạn!", "error", "user_dashboard.php", 2000);
        });
    </script></body></html>';
    exit;
}
$row = $result->fetch_assoc();

// Lấy thông tin location hiện tại
$current_location = null;
if (!empty($row['location_id'])) {
    $loc_sql = "SELECT id, province, district FROM locations WHERE id = " . (int)$row['location_id'];
    $loc_res = $conn->query($loc_sql);
    if ($loc_res && $loc_res->num_rows > 0) {
        $current_location = $loc_res->fetch_assoc();
    }
}

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
    $location_id = isset($_POST['location_id']) && $_POST['location_id'] ? (int)$_POST['location_id'] : 'NULL';

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
            $sql_up = "UPDATE product_images SET filename='$new_path' WHERE product_id=$id ORDER BY sort_order ASC LIMIT 1";
            $conn->query($sql_up);
        } else {
            // Nếu không upload ảnh mới, không làm gì
        }
    }

    // Cập nhật thông tin sản phẩm (giữ nguyên status, không cần admin duyệt lại)
    $sql_update = "UPDATE products SET title='$title', price='$price', description='$desc', category_id='$cate_id', location_id=$location_id WHERE id=$id AND seller_id=$my_id";

    if ($conn->query($sql_update) === TRUE) {
        echo '<script src="../js/toast.js"></script>
              <script>
                document.addEventListener("DOMContentLoaded", function() {
                    toastAndRedirect("Cập nhật thành công!", "success", "user_dashboard.php", 1500);
                });
              </script>';
    } else {
        echo "Lỗi SQL: " . $conn->error;
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
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        background: linear-gradient(45deg, #dda20a, #c99107);
        color: #1a1a2e;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(246, 194, 62, 0.4);
    }
    
    .btn-back {
        background: #1a1a2e;
        color: #fff;
        border: none;
        padding: 14px 30px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-back:hover {
        background: #2d2d44;
        color: #fff;
        transform: translateY(-2px);
    }
    
    .current-image-box {
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 15px;
        background: #f8f9fa;
        text-align: center;
    }
    
    .current-image-box img {
        max-height: 200px;
        object-fit: cover;
        border-radius: 10px;
    }
    
    .upload-note {
        color: #888;
        font-size: 13px;
        margin-top: 8px;
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
                        <li><a href="user_dangtin.php"><i class="fa-solid fa-plus"></i> Đăng tin mới</a></li>
                        <li><a href="user_quanlytin.php" class="active"><i class="fa-solid fa-list"></i> Quản lý tin</a></li>
                        <li><a href="doimk.php"><i class="fa-solid fa-key"></i> Đổi mật khẩu</a></li>
                        <li><a href="dangxuat.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="form-card">
                    <div class="form-header">
                        <h2><i class="fa-solid fa-pen-to-square me-2"></i>Chỉnh Sửa Tin Đăng</h2>
                        <p>Cập nhật thông tin sản phẩm của bạn</p>
                    </div>
                    
                    <div class="form-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="form-label"><i class="fa-solid fa-tag me-2" style="color: #f6c23e;"></i>Tiêu đề</label>
                                        <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label"><i class="fa-solid fa-money-bill me-2" style="color: #f6c23e;"></i>Giá bán (VNĐ)</label>
                                                <input type="number" name="price" class="form-control" required value="<?php echo htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?>" min="1000" step="1000">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label"><i class="fa-solid fa-folder me-2" style="color: #f6c23e;"></i>Danh mục</label>
                                                <select name="category_id" class="form-select" required>
                                                    <?php
                                                    $cats = $conn->query("SELECT * FROM categories");
                                                    while($c = $cats->fetch_assoc()) {
                                                        $selected = ($c['id'] == $row['category_id']) ? 'selected' : '';
                                                        echo "<option value='".$c['id']."' $selected>".htmlspecialchars($c['name'])."</option>";
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
                                        <textarea name="description" class="form-control" rows="6"><?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label"><i class="fa-solid fa-image me-2" style="color: #f6c23e;"></i>Ảnh hiện tại</label>
                                        <div class="current-image-box">
                                            <img src="<?php echo $img_src; ?>" class="img-fluid">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label"><i class="fa-solid fa-upload me-2" style="color: #f6c23e;"></i>Thay ảnh mới</label>
                                        <input type="file" name="image" class="form-control">
                                        <div class="upload-note">
                                            <i class="fa-solid fa-circle-info me-1"></i>Để trống nếu giữ ảnh cũ
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <button type="submit" name="btn_update" class="btn-submit">
                                    <i class="fa-solid fa-floppy-disk me-2"></i>Lưu Thay Đổi
                                </button>
                                <a href="user_quanlytin.php" class="btn-back">
                                    <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load danh sách tỉnh/thành từ database
const currentLocationId = <?php echo json_encode($current_location ? (int)$current_location['id'] : null); ?>;
const currentProvince = <?php echo json_encode($current_location ? $current_location['province'] : ''); ?>;

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
                // Nếu tỉnh này khớp với tỉnh hiện tại, dùng currentLocationId
                const locId = (p === currentProvince && currentLocationId) ? currentLocationId : provinceMap[p];
                const selected = (p === currentProvince) ? 'selected' : '';
                provinceSelect.innerHTML += `<option value="${locId}" ${selected}>${p}</option>`;
            });
        }
    })
    .catch(err => console.error('Lỗi load locations:', err));
</script>

