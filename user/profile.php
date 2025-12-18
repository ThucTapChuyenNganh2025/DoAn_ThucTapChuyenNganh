<?php
session_start();
include '../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php?next=profile.php");
    exit();
}

$id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name, email, phone, bio, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Đếm số tin đã đăng
$count_posts = 0;
$stmt2 = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE seller_id = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($row = $res2->fetch_assoc()) {
    $count_posts = $row['total'];
}
$stmt2->close();
?>

<?php include_once dirname(__DIR__) . '/includes/header.php'; ?>

<style>
    .profile-page {
        padding: 40px 0;
        min-height: calc(100vh - 150px);
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    }
    
    .profile-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .profile-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
        padding: 40px 30px;
        text-align: center;
        position: relative;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        background: linear-gradient(45deg, #f6c23e, #dda20a);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 48px;
        color: #1a1a2e;
        border: 4px solid rgba(255,255,255,0.3);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    
    .profile-name {
        color: #fff;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .profile-email {
        color: rgba(255,255,255,0.7);
        font-size: 14px;
    }
    
    .profile-stats {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-top: 25px;
        padding-top: 25px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #f6c23e;
    }
    
    .stat-label {
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .profile-body {
        padding: 30px;
    }
    
    .info-section {
        margin-bottom: 25px;
    }
    
    .info-section h5 {
        color: #1a1a2e;
        font-weight: 700;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f6c23e;
        display: inline-block;
    }
    
    .info-item {
        display: flex;
        align-items: flex-start;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 15px;
    }
    
    .info-icon {
        width: 45px;
        height: 45px;
        background: linear-gradient(45deg, #1a1a2e, #2d2d44);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #f6c23e;
        font-size: 18px;
        margin-right: 15px;
        flex-shrink: 0;
    }
    
    .info-content {
        flex: 1;
    }
    
    .info-label {
        font-size: 12px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }
    
    .info-value {
        font-size: 16px;
        color: #333;
        font-weight: 500;
    }
    
    .info-value.bio {
        font-size: 14px;
        line-height: 1.6;
        color: #555;
    }
    
    .profile-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .btn-profile {
        flex: 1;
        min-width: 150px;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .btn-edit {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
        color: #1a1a2e;
        border: none;
    }
    
    .btn-edit:hover {
        background: linear-gradient(45deg, #dda20a, #c99107);
        color: #1a1a2e;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(246, 194, 62, 0.4);
    }
    
    .btn-password {
        background: #1a1a2e;
        color: #fff;
        border: none;
    }
    
    .btn-password:hover {
        background: #2d2d44;
        color: #fff;
        transform: translateY(-2px);
    }
    
    .btn-dashboard {
        background: transparent;
        color: #1a1a2e;
        border: 2px solid #1a1a2e;
    }
    
    .btn-dashboard:hover {
        background: #1a1a2e;
        color: #fff;
        transform: translateY(-2px);
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
</style>

<div class="profile-page">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="profile-sidebar">
                    <h5 class="sidebar-title"><i class="fa-solid fa-user-gear me-2"></i>Menu</h5>
                    <ul class="sidebar-menu">
                        <li><a href="profile.php" class="active"><i class="fa-solid fa-user"></i> Hồ sơ cá nhân</a></li>
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
                <div class="profile-card">
                    <!-- Profile Header -->
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h2 class="profile-name"><?= htmlspecialchars($user['name'] ?? 'Người dùng') ?></h2>
                        <p class="profile-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                        
                        <div class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-number"><?= $count_posts ?></div>
                                <div class="stat-label">Tin đã đăng</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?= $user['created_at'] ? date('d/m/Y', strtotime($user['created_at'])) : 'N/A' ?></div>
                                <div class="stat-label">Ngày tham gia</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Body -->
                    <div class="profile-body">
                        <div class="info-section">
                            <h5><i class="fa-solid fa-circle-info me-2"></i>Thông tin cá nhân</h5>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Họ và tên</div>
                                    <div class="info-value"><?= htmlspecialchars($user['name'] ?? 'Chưa cập nhật') ?></div>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Email</div>
                                    <div class="info-value"><?= htmlspecialchars($user['email'] ?? 'Chưa cập nhật') ?></div>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Số điện thoại</div>
                                    <div class="info-value"><?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật') ?></div>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fa-solid fa-quote-left"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Giới thiệu bản thân</div>
                                    <div class="info-value bio"><?= nl2br(htmlspecialchars($user['bio'] ?? 'Chưa có thông tin giới thiệu')) ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="profile-actions">
                            <a href="update_profile.php" class="btn-profile btn-edit">
                                <i class="fa-solid fa-pen-to-square me-2"></i>Cập nhật hồ sơ
                            </a>
                            <a href="doimk.php" class="btn-profile btn-password">
                                <i class="fa-solid fa-key me-2"></i>Đổi mật khẩu
                            </a>
                            <a href="user_dashboard.php" class="btn-profile btn-dashboard">
                                <i class="fa-solid fa-gauge me-2"></i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

