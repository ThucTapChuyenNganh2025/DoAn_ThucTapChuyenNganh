<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: dangnhapadmin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Duyệt Tin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { background-color: #f0f2f5; font-family: 'Nunito', sans-serif; overflow-x: hidden; }
        
        /* Sidebar Pro */
        .sidebar {
            height: 100vh; width: 260px; position: fixed; top: 0; left: 0;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            padding-top: 25px; z-index: 1000; box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            transition: 0.3s;
            display: flex; flex-direction: column;
        }
        
        .sidebar a {
            padding: 15px 25px; text-decoration: none; font-size: 15px; color: #aeb4c6;
            display: flex; align-items: center; transition: 0.3s; font-weight: 500;
            border-left: 4px solid transparent;
        }
        
        .sidebar i { width: 35px; min-width: 35px; text-align: center; margin-right: 10px; font-size: 18px; }

        .sidebar a:hover, .sidebar a.active {
            background-color: rgba(255,255,255,0.05); color: #fff; border-left: 4px solid #4e73df;
        }
        
        .sidebar .brand {
            text-align: center; font-size: 24px; font-weight: 900; margin-bottom: 30px;
            color: #ffffff !important; text-transform: uppercase; letter-spacing: 1px;
            text-shadow: 0px 2px 4px rgba(0,0,0,0.5);
        }
        
        .logout-btn {
            margin-top: auto; margin-bottom: 30px;
            background-color: rgba(220, 53, 69, 0.1); color: #dc3545 !important;
        }
        .logout-btn:hover {
            background-color: #dc3545 !important; color: white !important; border-left: 4px solid #dc3545 !important;
        }

        .main-content { margin-left: 260px; padding: 30px; transition: 0.3s; }

        /* Card thống kê */
        .stat-card {
            border: none; border-radius: 15px; color: white; position: relative; overflow: hidden;
            transition: all 0.3s ease; cursor: pointer; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-card:hover { transform: translateY(-7px); box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
        .stat-card h3 { font-size: 32px; font-weight: 800; margin: 0; }
        .stat-card p { font-size: 14px; text-transform: uppercase; opacity: 0.9; margin: 0; }
        .stat-card .icon { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 45px; opacity: 0.2; }
        
        .bg-gradient-1 { background: linear-gradient(45deg, #4e73df, #224abe); }
        .bg-gradient-2 { background: linear-gradient(45deg, #1cc88a, #13855c); }
        .bg-gradient-3 { background: linear-gradient(45deg, #f6c23e, #dda20a); color: #fff; }
        .bg-gradient-4 { background: linear-gradient(45deg, #e74a3b, #be2617); }

        .card-custom {
            background: white; border: none; border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05); overflow: hidden;
            border-top: 4px solid #ff9f43; /* Màu cam cho trang duyệt tin */
        }
        
        @media (max-width: 768px) {
            .sidebar { left: -260px; }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0; padding: 15px; }
            .overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; }
            .overlay.active { display: block; }
        }
        a.card-link { text-decoration: none; }
    </style>
</head>
<body>

<div class="overlay" id="overlay" onclick="toggleMenu()"></div>

<div class="sidebar" id="sidebar">
    <div class="brand"><i class="fa-solid fa-bolt"></i> CHỢ ĐIỆN TỬ</div>
    
    <a href="admin_duyettin.php" class="active"><i class="fa-solid fa-chart-pie"></i> Tổng Quan</a>
    <a href="admin_sanpham.php"><i class="fa-solid fa-box"></i> Kho Sản Phẩm</a>
    <a href="admin_users.php"><i class="fa-solid fa-users-gear"></i> Quản Lý User</a>
    <a href="admin_reports.php"><i class="fa-solid fa-flag"></i> Báo Cáo Vi Phạm</a>
    
    <a href="dangxuatadmin.php" class="logout-btn">
        <i class="fa-solid fa-right-from-bracket"></i> Đăng Xuất
    </a>
</div>

<div class="main-content">
    
    <button class="btn btn-light shadow-sm d-md-none mb-3" onclick="toggleMenu()">
        <i class="fa-solid fa-bars"></i>
    </button>

    <?php
        include '../config/connect.php';
        $q1 = $conn->query("SELECT COUNT(*) as total FROM users"); $count_users = $q1 ? (int)$q1->fetch_assoc()['total'] : 0;
        $q2 = $conn->query("SELECT COUNT(*) as total FROM products"); $count_products = $q2 ? (int)$q2->fetch_assoc()['total'] : 0;
        $q3 = $conn->query("SELECT COUNT(*) as total FROM products WHERE status='pending'"); $count_pending = $q3 ? (int)$q3->fetch_assoc()['total'] : 0;
        $q4 = $conn->query("SELECT COUNT(*) as total FROM products WHERE status='approved'"); $count_approved = $q4 ? (int)$q4->fetch_assoc()['total'] : 0;
    ?>

    <div class="row mb-4">
        <div class="col-12 col-md-6 col-lg-3 mb-3">
            <a href="admin_users.php" class="card-link">
                <div class="card stat-card bg-gradient-1 p-4">
                    <div class="d-flex flex-column">
                        <h3><?php echo $count_users; ?></h3>
                        <p>Thành Viên</p>
                    </div>
                    <i class="fa-solid fa-users icon"></i>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-3 mb-3">
            <a href="admin_sanpham.php" class="card-link">
                <div class="card stat-card bg-gradient-2 p-4">
                    <div class="d-flex flex-column">
                        <h3><?php echo $count_products; ?></h3>
                        <p>Tổng Tin Đăng</p>
                    </div>
                    <i class="fa-solid fa-layer-group icon"></i>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-3 mb-3">
            <a href="admin_duyettin.php" class="card-link">
                <div class="card stat-card bg-gradient-3 p-4">
                    <div class="d-flex flex-column">
                        <h3><?php echo $count_pending; ?></h3>
                        <p>Chờ Duyệt</p>
                    </div>
                    <i class="fa-solid fa-hourglass-half icon"></i>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-3 mb-3">
            <a href="admin_sanpham.php?status=approved" class="card-link">
                <div class="card stat-card bg-gradient-4 p-4">
                    <div class="d-flex flex-column">
                        <h3><?php echo $count_approved; ?></h3>
                        <p>Đang Hiển Thị</p>
                    </div>
                    <i class="fa-solid fa-check-double icon"></i>
                </div>
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3 text-secondary"><i class="fa-solid fa-chart-simple text-primary"></i> Biểu Đồ Tăng Trưởng</h5>
                <div style="height: 300px;">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom p-4">
        <h5 class="mb-4 fw-bold text-dark"><i class="fa-solid fa-clipboard-check text-warning me-2"></i> Danh Sách Cần Phê Duyệt</h5>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="dataTable">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Sản Phẩm</th>
                        <th>Giá Bán</th>
                        <th>Người Bán</th>
                        <th class="text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT products.*, users.name as seller_name 
                            FROM products 
                            JOIN users ON products.seller_id = users.id 
                            WHERE products.status = 'pending' ORDER BY created_at DESC";
                    
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $img_url = !empty($row['image']) ? '../' . $row['image'] : 'https://via.placeholder.com/50';
                            echo "<tr>";
                            echo "<td class='fw-bold text-muted'>#" . $row["id"] . "</td>";
                            echo "<td><img src='$img_url' width='50' height='50' class='rounded shadow-sm' style='object-fit:cover'></td>";
                            echo "<td class='fw-bold text-primary'>" . $row["title"] . "</td>";
                            echo "<td class='text-danger fw-bold'>" . number_format($row["price"]) . " đ</td>";
                            echo "<td><div class='d-flex align-items-center'><div class='rounded-circle bg-light d-flex justify-content-center align-items-center me-2' style='width:30px;height:30px;font-weight:bold'>" . substr($row['seller_name'],0,1) . "</div>" . $row["seller_name"] . "</div></td>";
                            
                            // --- CẬP NHẬT NÚT BẤM DUYỆT & TỪ CHỐI TẠI ĐÂY ---
                            echo "<td class='text-center'>
                                    <button onclick=\"confirmAction('approve', " . $row['id'] . ")\" class='btn btn-sm btn-success shadow-sm px-3 rounded-pill me-2'>
                                        <i class='fa-solid fa-check me-1'></i> Duyệt
                                    </button>
                                    
                                    <button onclick=\"confirmAction('reject', " . $row['id'] . ")\" class='btn btn-sm btn-danger shadow-sm px-3 rounded-pill'>
                                        <i class='fa-solid fa-xmark me-1'></i> Từ chối
                                    </button>
                                  </td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function toggleMenu() {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('overlay').classList.toggle('active');
    }

    $(document).ready(function () {
        $('#dataTable').DataTable({
            language: { search: "Tìm nhanh:", lengthMenu: "Hiện _MENU_ dòng", info: "Trang _PAGE_ / _PAGES_", paginate: { first: "First", last: "Last", next: ">", previous: "<" }, zeroRecords: "Không có tin nào chờ duyệt!" },
            pageLength: 5
        });
    });

    const ctx = document.getElementById('myChart');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Thành Viên', 'Tổng Tin', 'Chờ Duyệt', 'Đã Duyệt'],
            datasets: [{
                label: 'Số liệu',
                data: [<?php echo $count_users; ?>, <?php echo $count_products; ?>, <?php echo $count_pending; ?>, <?php echo $count_approved; ?>],
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b'],
                borderRadius: 5
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    // HÀM XỬ LÝ SỰ KIỆN NÚT BẤM (SWEETALERT2)
    function confirmAction(action, id) {
        let titleText = action === 'approve' ? 'Duyệt tin đăng này?' : 'Từ chối tin đăng này?';
        let btnText = action === 'approve' ? 'Duyệt ngay' : 'Từ chối';
        let btnColor = action === 'approve' ? '#28a745' : '#d33';
        let iconType = action === 'approve' ? 'question' : 'warning';

        Swal.fire({
            title: titleText,
            text: "Trạng thái sản phẩm sẽ được cập nhật!",
            icon: iconType,
            showCancelButton: true,
            confirmButtonColor: btnColor,
            cancelButtonColor: '#3085d6',
            confirmButtonText: btnText,
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                // Chuyển hướng sang API xử lý
                window.location.href = `../api/xuly_duyet.php?id=${id}&action=${action}`;
            }
        })
    }
</script>

</body>
</html>