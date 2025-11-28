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

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kênh Người Bán - Tổng Quan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        
        /* Sidebar màu trắng sáng cho người bán */
        .sidebar {
            height: 100vh; width: 250px; position: fixed; top: 0; left: 0;
            background-color: #ffffff; border-right: 1px solid #e0e0e0;
            padding-top: 20px; color: #333;
        }
        .sidebar a {
            padding: 15px 25px; text-decoration: none; font-size: 16px; color: #555;
            display: block; transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #fff3cd; color: #ff9f43; /* Màu vàng cam */
            border-left: 4px solid #ff9f43; font-weight: bold;
        }
        .sidebar .brand {
            text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 30px; color: #ff9f43;
        }
        .main-content { margin-left: 250px; padding: 30px; }
        
        /* Card thống kê */
        .stat-card { border: none; border-radius: 10px; color: white; padding: 20px; position: relative; overflow: hidden; }
        .stat-card h3 { font-size: 32px; margin: 0; font-weight: bold; }
        .stat-card .icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 50px; opacity: 0.3; }
        
        .bg-blue { background-color: #17a2b8; }
        .bg-yellow { background-color: #ffc107; color: #333 !important; } /* Chữ đen cho nền vàng */
        .bg-green { background-color: #28a745; }
        
        .card-custom { border: none; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 5px solid #ff9f43; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-store"></i> KÊNH NGƯỜI BÁN</div>
    <a href="user_dashboard.php" class="active"><i class="fa-solid fa-gauge me-2"></i> Tổng Quan</a>
    <a href="user_dangtin.php"><i class="fa-solid fa-pen-to-square me-2"></i> Đăng Tin Mới</a>
    <a href="user_quanlytin.php"><i class="fa-solid fa-list me-2"></i> Tin Đã Đăng</a>
    <a href="#" class="text-danger mt-5"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng Xuất</a>
</div>

<div class="main-content">
    <h3 class="text-secondary mb-4">Xin chào, <?php echo $user_name; ?>!</h3>

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
        
        <table class="table align-middle">
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
                // Query lấy tin mới nhất
                $sql = "SELECT * FROM products WHERE seller_id = $my_id ORDER BY id DESC LIMIT 5";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $stt = $row['status'] == 'pending' ? '<span class="badge bg-warning text-dark">Chờ duyệt</span>' : 
                               ($row['status'] == 'approved' ? '<span class="badge bg-success">Đã duyệt</span>' : '<span class="badge bg-danger">Từ chối</span>');
                        
                        // Lấy ảnh từ bảng product_images
                        $img_query = $conn->query("SELECT filename FROM product_images WHERE product_id = " . $row['id'] . " LIMIT 1");
                        $img_row = $img_query->fetch_assoc();
                        $img_url = ($img_row && !empty($img_row['filename'])) ? '../' . $img_row['filename'] : 'https://via.placeholder.com/50';

                        echo "<tr>";
                        echo "<td><img src='$img_url' width='50' height='50' class='rounded border'></td>";
                        echo "<td class='fw-bold'>" . $row['title'] . "</td>";
                        echo "<td class='text-danger'>" . number_format($row['price']) . " đ</td>";
                        echo "<td>$stt</td>";
                        echo "<td>
                                <a href='user_sua_tin.php?id=" . $row['id'] . "' class='btn btn-sm btn-outline-primary'><i class='fa-solid fa-pen'></i> Sửa</a>
                                
                                <a href='xuly_xoa_tin.php?id=" . $row['id'] . "' 
                                   class='btn btn-sm btn-outline-danger'
                                   onclick='return confirm(\"Bạn có chắc chắn muốn xóa tin này không?\")'>
                                   <i class='fa-solid fa-trash'></i> Xóa
                                </a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center text-muted'>Chưa có tin nào!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>