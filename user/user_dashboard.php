<?php
session_start();
// Sửa đường dẫn include để tìm đúng file connect.php trong thư mục config
include '../config/connect.php'; 

// Giả lập User ID = 1
if(!isset($_SESSION['user_id'])) { $_SESSION['user_id'] = 1; }
$my_id = $_SESSION['user_id'];

// Lấy tên người dùng
$user_name = "Người dùng";
$u_query = $conn->query("SELECT name FROM users WHERE id = $my_id");
if($u_query && $u_query->num_rows > 0) {
    $user_name = $u_query->fetch_assoc()['name'];
}

// Thống kê (Dùng hàm COUNT)
$total = $conn->query("SELECT COUNT(*) as c FROM products WHERE seller_id = $my_id")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM products WHERE seller_id = $my_id AND status='pending'")->fetch_assoc()['c'];
$approved = $conn->query("SELECT COUNT(*) as c FROM products WHERE seller_id = $my_id AND status='approved'")->fetch_assoc()['c'];
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
    
    /* Dashboard Greeting */
    .dashboard-greeting {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 20px;
    }
    
    /* Stat Cards */
    .stat-card {
        background: #fff;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        position: relative;
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .stat-card h3 {
        font-size: 36px;
        font-weight: 800;
        margin-bottom: 5px;
    }
    
    .stat-card p {
        font-size: 14px;
        margin: 0;
        opacity: 0.9;
    }
    
    .stat-card .icon {
        position: absolute;
        right: 15px;
        bottom: 15px;
        font-size: 40px;
        opacity: 0.2;
    }
    
    .stat-card.bg-blue {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: #fff;
    }
    
    .stat-card.bg-yellow {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
        color: #1a1a2e;
    }
    
    .stat-card.bg-green {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: #fff;
    }
    
    /* Card Custom */
    .card-custom {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border: none;
    }
    
    .card-custom h4 {
        color: #f6c23e;
        font-weight: 700;
    }
    
    /* Dashboard Table */
    .dashboard-table thead th {
        font-weight: 700;
        color: #1a1a2e;
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        font-size: 13px;
        padding: 15px 10px;
    }
    
    .dashboard-table tbody td {
        padding: 15px 10px;
        border-bottom: 1px solid #eee;
        color: #333;
        vertical-align: middle;
    }
    
    .dashboard-table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .alert {
        border-radius: 10px;
        padding: 15px 20px;
    }

    @media (max-width: 576px) {
      .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        max-width: 100%;
      }
      table {
        min-width: 600px;
        font-size: 13px;
      }
      th, td {
        padding: 6px 4px !important;
        vertical-align: middle !important;
      }
      .btn, .btn-sm {
        font-size: 12px !important;
        padding: 4px 8px !important;
      }
      .table thead th {
        white-space: nowrap;
      }
      .card-custom {
        overflow-x: unset !important;
        max-width: 100vw;
      }
    }

    .menu-fixed, .sidebar-fixed, .sidebar.menu-fixed {
      position: sticky;
      top: 24px;
      z-index: 10;
    }
    @media (max-width: 991.98px) {
      .menu-fixed, .sidebar-fixed, .sidebar.menu-fixed {
        position: static;
        top: unset;
      }
    }

    @media (min-width: 992px) {
      .profile-sidebar {
        position: fixed;
        top: 90px;
        left: 0;
        height: calc(100vh - 90px);
        overflow-y: auto;
        z-index: 10;
        width: 320px;
      }
      .profile-page .col-lg-3 {
        width: 320px;
        flex: 0 0 320px;
        max-width: 320px;
      }
      .profile-page .col-lg-9 {
        margin-left: 320px;
        width: calc(100% - 320px);
        max-width: calc(100% - 320px);
      }
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
                        <li><a href="user_dashboard.php" class="active"><i class="fa-solid fa-gauge"></i> Tổng quan</a></li>
                        <li><a href="user_dangtin.php"><i class="fa-solid fa-plus"></i> Đăng tin mới</a></li>
                        <li><a href="user_quanlytin.php"><i class="fa-solid fa-list"></i> Quản lý tin</a></li>
                        <li><a href="doimk.php"><i class="fa-solid fa-key"></i> Đổi mật khẩu</a></li>
                        <li><a href="dangxuat.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <h2 class="dashboard-greeting"><i class="fa-solid fa-hand-wave me-2" style="color: #f6c23e;"></i>Xin chào, <?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?>!</h2>

    <div class="alert alert-info">
        <i class="fa-solid fa-bell me-2"></i> Bạn có <b><?php echo $pending; ?></b> tin đang chờ duyệt. Hãy kiểm tra thường xuyên nhé!
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card bg-blue">
                <h3><?php echo $total; ?></h3>
                <p>Tổng Tin Của Bạn</p>
                <i class="fa-solid fa-box icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-yellow">
                <h3><?php echo $pending; ?></h3>
                <p>Tin Đang Chờ Duyệt</p>
                <i class="fa-solid fa-clock icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-green">
                <h3><?php echo $approved; ?></h3>
                <p>Tin Đang Hiển Thị</p>
                <i class="fa-solid fa-check-circle icon"></i>
            </div>
        </div>
    </div>

    <div class="card card-custom p-4">
        <h4 class="mb-3"><i class="fa-solid fa-newspaper me-2"></i> Tin Đăng Gần Đây</h4>
        <p class="text-muted">Xem nhanh các tin bạn vừa đăng. Bạn có thể chỉnh sửa hoặc xóa tại đây.</p>
        
        <div class="table-responsive">
        <table class="table align-middle dashboard-table">
            <thead class="table-light">
                <tr>
                    <th>Ảnh</th>
                    <th>Sản Phẩm</th>
                    <th>Giá</th>
                    <th>Trạng Thái</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Lấy tin mới nhất kèm ảnh thumbnail
                $sql = "
                    SELECT p.*, 
                        (SELECT filename 
                            FROM product_images 
                            WHERE product_id = p.id 
                            ORDER BY sort_order ASC LIMIT 1) AS thumb
                    FROM products p
                    WHERE p.seller_id = $my_id
                    ORDER BY p.id DESC
                    LIMIT 5
                ";

                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $stt = $row['status'] == 'pending' ? '<span class="badge bg-warning text-dark" style="font-size: 12px; font-weight: 700; padding: 6px 10px;">Chờ duyệt</span>' : 
                               ($row['status'] == 'approved' ? '<span class="badge bg-success" style="font-size: 12px; font-weight: 700; padding: 6px 10px;">Đã duyệt</span>' : '<span class="badge bg-danger" style="font-size: 12px; font-weight: 700; padding: 6px 10px;">Từ chối</span>');
                        
                        // Xử lý ảnh (thumb được lấy bởi subquery trong SQL)
                        $img_url = !empty($row['thumb']) ? '../' . $row['thumb'] : 'https://via.placeholder.com/50';
                        $title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
                        $price = number_format($row['price']);

                        echo "<tr>";
                        echo "<td><img src='$img_url' width='50' height='50' class='rounded border' style='object-fit:cover;'></td>";
                        echo "<td style='color: #ff9f43; font-weight: 800; font-size: 14px;'>$title</td>";
                        echo "<td style='color: #dc3545; font-weight: 800; font-size: 15px;'>$price đ</td>";
                        echo "<td>$stt</td>";
                        echo "<td>
                                <a href='user_sua_tin.php?id={$row['id']}' class='btn btn-sm btn-outline-primary' style='font-weight: 700; font-size: 12px;'>
                                    <i class='fa-solid fa-pen'></i> Sửa
                                </a>

                                <a href='javascript:void(0)' 
                                class='btn btn-sm btn-outline-danger'
                                style='font-weight: 700; font-size: 12px;'
                                onclick='confirmDelete(\"Bạn có chắc chắn muốn xóa tin này không?\", \"xuly_xoa_tin.php?id={$row['id']}\")' >
                                <i class='fa-solid fa-trash'></i> Xóa
                                </a>
                            </td>";
                        echo "</tr>";
                    }
                }
                else {
                    echo "<tr><td colspan='5' class='text-center text-muted'>Chưa có tin nào!</td></tr>";
                }
                ?>
            </tbody>
        </table>
        </div>
    </div>
            </div>
        </div>
    </div>
</div>

