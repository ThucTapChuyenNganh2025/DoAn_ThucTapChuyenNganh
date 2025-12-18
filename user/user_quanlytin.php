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

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <aside class="seller-aside">
                <div class="text-center mb-3 brand"><i class="fa-solid fa-store me-2"></i>Đăng Tin</div>
                <ul class="list-unstyled">
                    <li><a href="user_dashboard.php">Tổng Quan</a></li>
                    <li><a href="user_dangtin.php">Đăng Tin</a></li>
                    <li><a href="user_quanlytin.php" class="active">Tin Đã Đăng</a></li>
                </ul>
            </aside>
        </div>

        <div class="col-lg-9">
            <h2 class="dashboard-greeting">Quản Lý Tin Đã Đăng</h2>

            <div class="alert alert-info">
                <i class="fa-solid fa-info-circle"></i> Dưới đây là tất cả các tin bạn đã đăng. Bạn có thể chỉnh sửa hoặc xóa từng tin.
            </div>

            <div class="card card-custom p-4">
                <h4 class="mb-3 text-warning"><i class="fa-solid fa-list"></i> Danh Sách Tin Đã Đăng</h4>
                
                <style>
                    .table thead th {
                        font-weight: 800;
                        color: #222;
                        background: #f5f5f5;
                        border-bottom: 2px solid #ddd;
                        font-size: 13px;
                        padding: 12px 8px;
                    }
                    .table tbody td {
                        padding: 12px 8px;
                        border-bottom: 1px solid #eee;
                        color: #222;
                    }
                    .table tbody tr:hover {
                        background: #fafafa;
                    }
                    .table a {
                        color: #ff9f43;
                        font-weight: 700;
                        text-decoration: none;
                    }
                    .table a:hover {
                        text-decoration: underline;
                    }
                </style>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
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
                                    echo "<td style='color: #ff9f43; font-weight: 800;'><a href='../index.php?id={$row['id']}' target='_blank' style='color: #ff9f43;'>$title</a></td>";
                                    echo "<td style='color: #dc3545; font-weight: 800; font-size: 15px;'>$price đ</td>";
                                    echo "<td>$status_badge</td>";
                                    echo "<td><span class='badge bg-info' style='font-size: 12px; font-weight: 700; padding: 6px 10px; color: #fff;'>$fav_count <i class='fa-solid fa-heart'></i></span></td>";
                                    echo "<td style='color: #222; font-weight: 600;'>$date</td>";
                                    echo "<td>
                                            <a href='user_sua_tin.php?id={$row['id']}' class='btn btn-sm btn-outline-primary' style='font-weight: 700; font-size: 12px;'>
                                                <i class='fa-solid fa-pen'></i> Sửa
                                            </a>
                                            <a href='xuly_xoa_tin.php?id={$row['id']}' 
                                            class='btn btn-sm btn-outline-danger'
                                            onclick='return confirm(\"Bạn có chắc chắn muốn xóa tin này không?\")'>
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

            <div class="mt-4">
                <a href="user_dangtin.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Đăng Tin Mới
                </a>
            </div>
        </div>
    </div>
</div>

<?php include_once dirname(__DIR__) . '/includes/footer.php'; ?>
