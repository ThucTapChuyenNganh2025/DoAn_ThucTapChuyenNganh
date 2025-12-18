<?php
session_start();
include '../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php?next=doimk.php");
    exit();
}

$id = $_SESSION['user_id'];
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'] ?? ''; 
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Lấy mật khẩu hiện tại từ DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Kiểm tra validation
    if (empty($old) || empty($new) || empty($confirm)) {
        $message = 'Vui lòng điền đầy đủ thông tin!';
        $message_type = 'danger';
    } elseif (!password_verify($old, $user['password'])) {
        $message = 'Mật khẩu hiện tại không đúng!';
        $message_type = 'danger';
    } elseif (strlen($new) < 6) {
        $message = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
        $message_type = 'danger';
    } elseif ($new !== $confirm) {
        $message = 'Mật khẩu mới không khớp!';
        $message_type = 'danger';
    } else {
        // Hash mật khẩu mới
        $new_hashed = password_hash($new, PASSWORD_BCRYPT);

        // Cập nhật mật khẩu
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_hashed, $id);
        
        if ($stmt->execute()) {
            $message = 'Đổi mật khẩu thành công!';
            $message_type = 'success';
        } else {
            $message = 'Có lỗi xảy ra, vui lòng thử lại!';
            $message_type = 'danger';
        }
        $stmt->close();
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
    
    .password-tips {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
    }
    
    .password-tips h6 {
        color: #1a1a2e;
        font-weight: 700;
        margin-bottom: 15px;
    }
    
    .password-tips ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .password-tips li {
        color: #666;
        margin-bottom: 5px;
        font-size: 14px;
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
                        <li><a href="doimk.php" class="active"><i class="fa-solid fa-key"></i> Đổi mật khẩu</a></li>
                        <li><a href="dangxuat.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="form-card">
                    <div class="form-header">
                        <h2><i class="fa-solid fa-key me-2"></i>Đổi Mật Khẩu</h2>
                        <p>Bảo mật tài khoản của bạn bằng mật khẩu mới</p>
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
                                <label class="form-label"><i class="fa-solid fa-lock"></i>Mật khẩu hiện tại</label>
                                <input type="password" name="old_password" class="form-control" required placeholder="Nhập mật khẩu hiện tại">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label"><i class="fa-solid fa-lock"></i>Mật khẩu mới</label>
                                <input type="password" name="new_password" class="form-control" required placeholder="Nhập mật khẩu mới">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label"><i class="fa-solid fa-check-double"></i>Nhập lại mật khẩu mới</label>
                                <input type="password" name="confirm_password" class="form-control" required placeholder="Xác nhận mật khẩu mới">
                            </div>
                            
                            <div class="password-tips">
                                <h6><i class="fa-solid fa-lightbulb me-2"></i>Gợi ý mật khẩu an toàn:</h6>
                                <ul>
                                    <li>Sử dụng ít nhất 6 ký tự</li>
                                    <li>Kết hợp chữ hoa, chữ thường và số</li>
                                    <li>Thêm ký tự đặc biệt như @, #, $, !</li>
                                    <li>Không sử dụng thông tin cá nhân dễ đoán</li>
                                </ul>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6 mb-3">
                                    <button type="submit" class="btn-submit">
                                        <i class="fa-solid fa-shield me-2"></i>Đổi mật khẩu
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

