<?php
session_start();
include '../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php?next=update_profile.php");
    exit();
}

$id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Xử lý form cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    
    if (empty($name)) {
        $message = 'Vui lòng nhập họ tên!';
        $message_type = 'danger';
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, bio = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $phone, $bio, $id);
        
        if ($stmt->execute()) {
            $message = 'Cập nhật hồ sơ thành công!';
            $message_type = 'success';
        } else {
            $message = 'Có lỗi xảy ra, vui lòng thử lại!';
            $message_type = 'danger';
        }
        $stmt->close();
    }
}

// Lấy thông tin user hiện tại
$stmt = $conn->prepare("SELECT name, email, phone, bio, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
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
        display: flex;
        align-items: center;
    }
    
    .form-label i {
        margin-right: 8px;
        color: #f6c23e;
    }
    
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #f6c23e;
        box-shadow: 0 0 0 3px rgba(246, 194, 62, 0.15);
    }
    
    .form-control:disabled {
        background: #f8f9fa;
        cursor: not-allowed;
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
    
    .btn-back {
        background: #1a1a2e;
        color: #fff;
        border: none;
        padding: 14px 30px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 16px;
        width: 100%;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        text-align: center;
    }
    
    .btn-back:hover {
        background: #2d2d44;
        color: #fff;
        transform: translateY(-2px);
    }
    
    .alert {
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 25px;
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
                        <li><a href="user_quanlytin.php"><i class="fa-solid fa-list"></i> Quản lý tin</a></li>
                        <li><a href="doimk.php"><i class="fa-solid fa-key"></i> Đổi mật khẩu</a></li>
                        <li><a href="dangxuat.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="form-card">
                    <div class="form-header">
                        <h2><i class="fa-solid fa-pen-to-square me-2"></i>Cập Nhật Hồ Sơ</h2>
                        <p>Chỉnh sửa thông tin cá nhân của bạn</p>
                    </div>
                    
                    <div class="form-body">
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $message_type ?>">
                                <i class="fa-solid fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-2"></i>
                                <?= $message ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="form-group">
                                <label class="form-label"><i class="fa-solid fa-envelope"></i>Email</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
                                <small class="text-muted">Email không thể thay đổi</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label"><i class="fa-solid fa-user"></i>Họ và tên</label>
                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required placeholder="Nhập họ và tên">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label"><i class="fa-solid fa-phone"></i>Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="Nhập số điện thoại">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label"><i class="fa-solid fa-quote-left"></i>Giới thiệu bản thân</label>
                                <textarea name="bio" class="form-control" placeholder="Viết vài dòng giới thiệu về bạn..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <button type="submit" class="btn-submit">
                                        <i class="fa-solid fa-save me-2"></i>Lưu thay đổi
                                    </button>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <a href="profile.php" class="btn-back">
                                        <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

