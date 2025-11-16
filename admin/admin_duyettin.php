<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Trang Chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        
        /* Sidebar */
        .sidebar {
            height: 100vh; width: 250px; position: fixed; top: 0; left: 0;
            background-color: #343a40; padding-top: 20px; color: white; z-index: 1000;
        }
        .sidebar a {
            padding: 15px 25px; text-decoration: none; font-size: 16px; color: #cfd8dc;
            display: block; transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #495057; color: #ff9f43; border-left: 4px solid #ff9f43;
        }
        .sidebar .brand {
            text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 30px; color: #ff9f43;
        }
        .main-content { margin-left: 250px; padding: 30px; }
        
        /* Card thống kê */
        .stat-card {
            border: none; border-radius: 10px; color: white; position: relative; overflow: hidden;
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card h3 { font-size: 36px; font-weight: bold; margin: 0; }
        .stat-card p { font-size: 16px; opacity: 0.8; margin: 0; }
        .stat-card .icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 50px; opacity: 0.3; }
        
        .bg-primary-gradient { background: linear-gradient(45deg, #4099ff, #73b4ff); }
        .bg-success-gradient { background: linear-gradient(45deg, #2ed8b6, #59e0c5); }
        .bg-warning-gradient { background: linear-gradient(45deg, #FFB64D, #ffcb80); }
        .bg-danger-gradient { background: linear-gradient(45deg, #FF5370, #ff869a); }

        /* Table Card */
        .card-custom {
            background: white; border: none; border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 5px solid #ff9f43;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-shop"></i> CHỢ ADMIN</div>
    
    <a href="admin_duyettin.php" class="active"><i class="fa-solid fa-chart-line me-2"></i> Tổng Quan & Duyệt</a>
    <a href="admin_sanpham.php"><i class="fa-solid fa-box-open me-2"></i> Kho Sản Phẩm</a>
    <a href="admin_users.php"><i class="fa-solid fa-users me-2"></i> Quản Lý User</a> <a href="#"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng Xuất</a>
</div>

<div class="main-content">
    
    <?php
        include '../config/connect.php';
        
        // Nếu database trống, các hàm count có thể trả về null, nên dùng (int) để ép về 0
        $q1 = $conn->query("SELECT COUNT(*) as total FROM users");
        $count_users = $q1 ? $q1->fetch_assoc()['total'] : 0;
        
        $q2 = $conn->query("SELECT COUNT(*) as total FROM products");
        $count_products = $q2 ? $q2->fetch_assoc()['total'] : 0;
        
        $q3 = $conn->query("SELECT COUNT(*) as total FROM products WHERE status='pending'");
        $count_pending = $q3 ? $q3->fetch_assoc()['total'] : 0;
        
        $q4 = $conn->query("SELECT COUNT(*) as total FROM products WHERE status='approved'");
        $count_approved = $q4 ? $q4->fetch_assoc()['total'] : 0;
    ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-secondary">Dashboard Thống Kê</h3>
        <span class="text-muted"><i class="fa-regular fa-calendar"></i> Hôm nay: <?php echo date("d/m/Y"); ?></span>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-primary-gradient p-3">
                <div class="d-flex flex-column">
                    <h3><?php echo $count_users; ?></h3>
                    <p>Thành Viên</p>
                </div>
                <i class="fa-solid fa-users icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success-gradient p-3">
                <div class="d-flex flex-column">
                    <h3><?php echo $count_products; ?></h3>
                    <p>Tổng Tin Đăng</p>
                </div>
                <i class="fa-solid fa-box icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-warning-gradient p-3">
                <div class="d-flex flex-column">
                    <h3><?php echo $count_pending; ?></h3>
                    <p>Chờ Duyệt</p>
                </div>
                <i class="fa-solid fa-clock icon"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-danger-gradient p-3">
                <div class="d-flex flex-column">
                    <h3><?php echo $count_approved; ?></h3>
                    <p>Đang Hiển Thị</p>
                </div>
                <i class="fa-solid fa-check-circle icon"></i>
            </div>
        </div>
    </div>

    <div class="card card-custom p-4">
        <h4 class="mb-4 text-dark"><i class="fa-solid fa-list-check text-warning"></i> Danh Sách Cần Phê Duyệt</h4>

        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Sản Phẩm</th>
                    <th>Giá Bán</th>
                    <th>Người Bán</th>
                    <th>Trạng Thái</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT products.*, users.name as seller_name 
                        FROM products 
                        JOIN users ON products.seller_id = users.id 
                        WHERE products.status = 'pending'";
                
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>#" . $row["id"] . "</td>";
                        echo "<td class='fw-bold'>" . $row["title"] . "</td>";
                        echo "<td class='text-danger fw-bold'>" . number_format($row["price"]) . " đ</td>";
                        echo "<td>" . $row["seller_name"] . "</td>";
                        echo "<td><span class='badge bg-warning text-dark rounded-pill'>Pending</span></td>";
                        echo "<td>
                                <a href='../api/xuly_duyet.php?id=" . $row["id"] . "&action=approve' class='btn btn-sm btn-success shadow-sm'><i class='fa-solid fa-check'></i></a>
                                <a href='xuly_duyet.php?id=" . $row["id"] . "&action=reject' class='btn btn-sm btn-danger shadow-sm'><i class='fa-solid fa-trash'></i></a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center py-5 text-muted'>
                            <i class='fa-solid fa-mug-hot fa-3x mb-3'></i><br>
                            Tuyệt vời! Đã duyệt hết tin mới.
                          </td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>