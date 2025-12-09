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

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <aside class="seller-aside">
                <div class="text-center mb-3 brand"><i class="fa-solid fa-store me-2"></i>Đăng Tin</div>
                <ul class="list-unstyled">
                    <li><a href="user_dashboard.php" class="active">Tổng Quan</a></li>
                    <li><a href="user_dangtin.php">Đăng Tin</a></li>
                    <li><a href="user_quanlytin.php">Tin Đã Đăng</a></li>
                </ul>
            </aside>
        </div>

        <div class="col-lg-9">
    <h2 class="dashboard-greeting">Xin chào, <?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?>!</h2>

    <div class="alert alert-info">
        <i class="fa-solid fa-bell"></i> Bạn có <b><?php echo $pending; ?></b> tin đang chờ duyệt. Hãy kiểm tra thường xuyên nhé!
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
            <div class="stat-card bg-yellow" style="color: #333;">
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
        <h4 class="mb-3 text-warning"><i class="fa-solid fa-newspaper"></i> Tin Đăng Gần Đây</h4>
        <p class="text-muted">Xem nhanh các tin bạn vừa đăng. Bạn có thể chỉnh sửa hoặc xóa tại đây.</p>
        
        <style>
            .dashboard-table thead th {
                font-weight: 800;
                color: #222;
                background: #f5f5f5;
                border-bottom: 2px solid #ddd;
                font-size: 13px;
                padding: 12px 8px;
            }
            .dashboard-table tbody td {
                padding: 12px 8px;
                border-bottom: 1px solid #eee;
                color: #222;
            }
            .dashboard-table tbody tr:hover {
                background: #fafafa;
            }
        </style>
        
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

                                <a href='xuly_xoa_tin.php?id={$row['id']}' 
                                class='btn btn-sm btn-outline-danger'
                                style='font-weight: 700; font-size: 12px;'
                                onclick='return confirm(\"Bạn có chắc chắn muốn xóa tin này không?\")'>
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

<?php include_once dirname(__DIR__) . '/includes/footer.php'; ?>
