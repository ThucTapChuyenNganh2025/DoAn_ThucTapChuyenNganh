<?php
session_start();
include '../config/connect.php'; // Đường dẫn đã sửa
// Giả lập đang đăng nhập là User ID 1 (Ông Nguyễn Văn A)
// Sau này TV2 làm xong Login thì xóa dòng này đi
if(!isset($_SESSION['user_id'])) { $_SESSION['user_id'] = 1; }
$my_id = $_SESSION['user_id'];

// Lấy tên người dùng để hiển thị
$user_name_q = $conn->query("SELECT name FROM users WHERE id = $my_id");
$user_name = $user_name_q ? $user_name_q->fetch_assoc()['name'] : 'Người dùng';

// Thống kê tin đăng của riêng người này
$total_my_products_q = $conn->query("SELECT COUNT(*) AS total FROM products WHERE seller_id = $my_id");
$total_my_products = $total_my_products_q ? $total_my_products_q->fetch_assoc()['total'] : 0;

$my_pending_products_q = $conn->query("SELECT COUNT(*) AS total FROM products WHERE seller_id = $my_id AND status = 'pending'");
$my_pending_products = $my_pending_products_q ? $my_pending_products_q->fetch_assoc()['total'] : 0;

$my_approved_products_q = $conn->query("SELECT COUNT(*) AS total FROM products WHERE seller_id = $my_id AND status = 'approved'");
$my_approved_products = $my_approved_products_q ? $my_approved_products_q->fetch_assoc()['total'] : 0;

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kênh Người Bán - Tổng Quan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS tương tự Admin nhưng màu sắc nhận diện khác */
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            height: 100vh; width: 250px; position: fixed; top: 0; left: 0;
            background-color: #f8f9fa; /* Nền trắng sáng */
            border-right: 1px solid #dee2e6; /* Viền xám nhạt */
            padding-top: 20px; color: #343a40; /* Chữ đen */
            box-shadow: 2px 0 5px rgba(0,0,0,0.05); /* Bóng nhẹ */
        }
        .sidebar a {
            padding: 15px 25px; text-decoration: none; font-size: 16px; color: #495057; /* Chữ xám đậm */
            display: block; transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #fff3cd; /* Nền vàng nhạt khi hover/active */
            color: #ffc107; /* Chữ vàng đậm */
            border-left: 4px solid #ffc107; /* Viền vàng */
            font-weight: bold;
        }
        .sidebar .brand {
            text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 30px; color: #ffc107; /* Màu vàng */
        }
        .main-content { margin-left: 250px; padding: 30px; }
        
        /* Card thống kê giống Admin nhưng màu sắc user-friendly hơn */
        .stat-card {
            border: none; border-radius: 10px; color: white; position: relative; overflow: hidden;
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card h3 { font-size: 36px; font-weight: bold; margin: 0; }
        .stat-card p { font-size: 16px; opacity: 0.8; margin: 0; }
        .stat-card .icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 50px; opacity: 0.3; }
        
        /* Các màu sắc cho User Dashboard */
        .bg-user-primary { background: linear-gradient(45deg, #17a2b8, #28a745); } /* Xanh dương - Xanh lá */
        .bg-user-secondary { background: linear-gradient(45deg, #6c757d, #adb5bd); } /* Xám */
        .bg-user-warning { background: linear-gradient(45deg, #ffc107, #ffcd38); } /* Vàng cam */
        .bg-user-success { background: linear-gradient(45deg, #28a745, #218838); } /* Xanh lá đậm */
        
        .card-custom {
            background: white; border: none; border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 5px solid #ffc107; /* Màu vàng */
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-store"></i> KÊNH NGƯỜI BÁN</div>
    <a href="user_dashboard.php" class="active"><i class="fa-solid fa-gauge me-2"></i> Tổng Quan</a>
    <a href="user_dangtin.php"><i class="fa-solid fa-pen-to-square me-2"></i> Đăng Tin Mới</a>
    <a href="user_quanlytin.php"><i class="fa-solid fa-list-check me-2"></i> Tin Đã Đăng</a>
    <a href="#"><i class="fa-solid fa-user-gear me-2"></i> Hồ Sơ Cá Nhân</a>
    <a href="#" class="text-danger mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng Xuất</a>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-secondary">Xin chào, <?php echo $user_name; ?>!</h3>
        <span class="text-muted"><i class="fa-regular fa-calendar"></i> Hôm nay: <?php echo date("d/m/Y"); ?></span>
    </div>

    <div class="alert alert-info border-0 shadow-sm" role="alert">
        <i class="fa-solid fa-lightbulb me-2"></i> Bạn có **<?php echo $my_pending_products; ?>** tin đang chờ duyệt. Hãy kiểm tra thường xuyên nhé!
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card bg-user-primary p-3">
                <div class="d-flex flex-column">
                    <h3><?php echo $total_my_products; ?></h3>
                    <p>Tổng Tin Của Bạn</p>
                </div>
                <i class="fa-solid fa-box icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-user-warning p-3">
                <div class="d-flex flex-column">
                    <h3><?php echo $my_pending_products; ?></h3>
                    <p>Tin Đang Chờ Duyệt</p>
                </div>
                <i class="fa-solid fa-clock icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-user-success p-3">
                <div class="d-flex flex-column">
                    <h3><?php echo $my_approved_products; ?></h3>
                    <p>Tin Đang Hiển Thị</p>
                </div>
                <i class="fa-solid fa-check-double icon"></i>
            </div>
        </div>
    </div>

    <div class="card card-custom p-4">
        <h4 class="mb-4 text-dark"><i class="fa-solid fa-newspaper text-warning me-2"></i> Tin Đăng Gần Đây</h4>
        <p class="text-muted">Xem nhanh các tin bạn vừa đăng. Bạn có thể chỉnh sửa hoặc xóa tại đây.</p>
        
        <table class="table table-hover align-middle">
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
                $recent_products_sql = "SELECT id, title, price, image, status FROM products WHERE seller_id = $my_id ORDER BY created_at DESC LIMIT 5";
                $recent_products_result = $conn->query($recent_products_sql);

                if ($recent_products_result && $recent_products_result->num_rows > 0) {
                    while($row = $recent_products_result->fetch_assoc()) {
                        $stt = '';
                        if($row['status']=='pending') $stt = '<span class="badge bg-warning text-dark">Đang chờ duyệt</span>';
                        elseif($row['status']=='approved') $stt = '<span class="badge bg-success">Đang hiển thị</span>';
                        elseif($row['status']=='rejected') $stt = '<span class="badge bg-danger">Bị từ chối</span>';
                        elseif($row['status']=='hidden') $stt = '<span class="badge bg-secondary">Đã ẩn</span>';

                        $img_src = !empty($row['image']) ? '../' . $row['image'] : 'https://via.placeholder.com/60?text=No+Image'; // Đường dẫn ảnh
                        
                        echo "<tr>";
                        echo "<td><img src='$img_src' class='rounded' style='width: 60px; height: 60px; object-fit: cover;'></td>";
                        echo "<td>" . $row['title'] . "</td>";
                        echo "<td class='text-danger fw-bold'>" . number_format($row['price']) . " đ</td>";
                        echo "<td>" . $stt . "</td>";
                        echo "<td>
                                <a href='user_sua_tin.php?id=" . $row['id'] . "' class='btn btn-sm btn-outline-primary me-1'><i class='fa-solid fa-pen'></i> Sửa</a>
                                <a href='xuly_xoa_tin.php?id=" . $row['id'] . "' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Bạn chắc chắn muốn xóa tin này?\")'><i class='fa-solid fa-trash'></i> Xóa</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Bạn chưa đăng tin nào gần đây. <a href='user_dangtin.php'>Đăng tin ngay!</a></td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>