<?php
session_start();
include '../config/connect.php';

// Check if logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php?next=/user/user_quanlytin.php');
    exit;
}

$my_id = $_SESSION['user_id'];

// Get user name
$user_name = "Người dùng";
$u_query = $conn->query("SELECT name FROM users WHERE id = $my_id");
if($u_query && $u_query->num_rows > 0) {
    $user_name = $u_query->fetch_assoc()['name'];
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
    
    /* Card Custom */
    .card-custom {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
    }
    
    .card-header-custom {
        background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
        padding: 25px 30px;
    }
    
    .card-header-custom h4 {
        color: #fff;
        font-weight: 700;
        margin: 0;
    }
    
    .card-header-custom p {
        color: rgba(255,255,255,0.7);
        margin: 5px 0 0;
        font-size: 14px;
    }
    
    .card-body-custom {
        padding: 30px;
    }
    
    /* Table Styles */
    .table-custom thead th {
        font-weight: 700;
        color: #1a1a2e;
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        font-size: 13px;
        padding: 15px 12px;
    }
    
    .table-custom tbody td {
        padding: 15px 12px;
        border-bottom: 1px solid #eee;
        color: #333;
        vertical-align: middle;
    }
    
    .table-custom tbody tr:hover {
        background: #f8f9fa;
    }
    
    .product-title {
        color: #f6c23e;
        font-weight: 700;
        text-decoration: none;
    }
    
    .product-title:hover {
        color: #dda20a;
        text-decoration: underline;
    }
    
    .product-price {
        color: #dc3545;
        font-weight: 700;
        font-size: 15px;
    }
    
    .btn-new-post {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
        color: #1a1a2e;
        border: none;
        padding: 12px 25px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }
    
    .btn-new-post:hover {
        background: linear-gradient(45deg, #dda20a, #c99107);
        color: #1a1a2e;
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(246, 194, 62, 0.4);
    }
    
    .alert {
        border-radius: 10px;
        padding: 15px 20px;
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
                        <li><a href="user_dashboard.php"><i class="fa-solid fa-gauge"></i> Tổng quan</a></li>
                        <li><a href="user_dangtin.php"><i class="fa-solid fa-plus"></i> Đăng tin mới</a></li>
                        <li><a href="user_quanlytin.php" class="active"><i class="fa-solid fa-list"></i> Quản lý tin</a></li>
                        <li><a href="doimk.php"><i class="fa-solid fa-key"></i> Đổi mật khẩu</a></li>
                        <li><a href="dangxuat.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="alert alert-info mb-4">
                    <i class="fa-solid fa-info-circle me-2"></i> Dưới đây là tất cả các tin bạn đã đăng. Bạn có thể chỉnh sửa hoặc xóa từng tin.
                </div>

                <div class="card card-custom">
                    <div class="card-header-custom">
                        <h4><i class="fa-solid fa-list me-2"></i>Quản Lý Tin Đã Đăng</h4>
                        <p>Xem và quản lý tất cả tin đăng của bạn</p>
                    </div>
                    
                    <div class="card-body-custom">
                        <div class="table-responsive">
                            <table class="table table-custom align-middle">
                                <thead>
                                    <tr>
                                        <th>Ảnh</th>
                                        <th>Sản Phẩm</th>
                                        <th>Giá</th>
                                        <th>Trạng Thái</th>
                                        <th>Yêu Thích</th>
                                        <th>Ngày Đăng</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                            <?php
                            // Get all products of user with favorite count
                            $sql = "
                                SELECT p.*, 
                                    (SELECT filename 
                                        FROM product_images 
                                        WHERE product_id = p.id 
                                        ORDER BY sort_order ASC LIMIT 1) AS thumb,
                                    (SELECT COUNT(*) FROM favorites WHERE product_id = p.id) AS fav_count
                                FROM products p
                                WHERE p.seller_id = $my_id
                                ORDER BY p.id DESC
                            ";

                            $result = $conn->query($sql);

                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $status_badge = '';
                                    if($row['status'] == 'pending') {
                                        $status_badge = '<span class="badge bg-warning text-dark" style="font-size: 12px; font-weight: 700; padding: 6px 10px;">Chờ duyệt</span>';
                                    } elseif($row['status'] == 'approved') {
                                        $status_badge = '<span class="badge bg-success" style="font-size: 12px; font-weight: 700; padding: 6px 10px;">Đã duyệt</span>';
                                    } else {
                                        $status_badge = '<span class="badge bg-danger" style="font-size: 12px; font-weight: 700; padding: 6px 10px;">Từ chối</span>';
                                    }
                                    
                                    // Image handling
                                    $img_url = !empty($row['thumb']) ? '../' . $row['thumb'] : 'https://via.placeholder.com/50';
                                    $title = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
                                    $price = number_format($row['price']);
                                    $fav_count = $row['fav_count'];
                                    $date = date('d/m/Y', strtotime($row['created_at']));

                                    echo "<tr>";
                                    echo "<td><img src='$img_url' width='50' height='50' class='rounded border' style='object-fit:cover;'></td>";
                                    echo "<td><a href='../index.php?id={$row['id']}' target='_blank' class='product-title'>$title</a></td>";
                                    echo "<td class='product-price'>$price đ</td>";
                                    echo "<td>$status_badge</td>";
                                    echo "<td><span style='display: inline-flex; align-items: center; gap: 5px; background: linear-gradient(135deg, #ff6b6b, #ee5a5a); color: #fff; font-size: 13px; font-weight: 700; padding: 8px 14px; border-radius: 20px; box-shadow: 0 2px 8px rgba(238, 90, 90, 0.3);'><i class='fa-solid fa-heart'></i> $fav_count</span></td>";
                                    echo "<td style='font-weight: 500;'>$date</td>";
                                    echo "<td>
                                            <a href='user_sua_tin.php?id={$row['id']}' class='btn btn-sm btn-outline-primary' style='font-weight: 600; font-size: 12px;'>
                                                <i class='fa-solid fa-pen'></i> Sửa
                                            </a>
                                            <a href='javascript:void(0)' 
                                            class='btn btn-sm btn-outline-danger'
                                            style='font-weight: 600; font-size: 12px;'
                                            onclick='confirmDelete(\"Bạn có chắc chắn muốn xóa tin này không?\", \"xuly_xoa_tin.php?id={$row['id']}\")' >
                                            <i class='fa-solid fa-trash'></i> Xóa
                                            </a>
                                        </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center text-muted py-5'>Bạn chưa đăng tin nào!</td></tr>";
                            }
                            ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="user_dangtin.php" class="btn-new-post">
                        <i class="fa-solid fa-plus me-2"></i>Đăng Tin Mới
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

