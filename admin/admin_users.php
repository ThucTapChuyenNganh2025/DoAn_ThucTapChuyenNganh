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
    <title>Quản Lý Thành Viên</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { background-color: #f0f2f5; font-family: 'Nunito', sans-serif; overflow-x: hidden; }
        
        /* --- SIDEBAR CHUẨN (Đồng bộ) --- */
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
        
        /* Nút Đăng Xuất */
        .logout-btn {
            margin-top: auto; margin-bottom: 30px;
            background-color: rgba(220, 53, 69, 0.1); color: #dc3545 !important;
        }
        .logout-btn:hover {
            background-color: #dc3545 !important; color: white !important; border-left: 4px solid #dc3545 !important;
        }

        .main-content { margin-left: 260px; padding: 30px; transition: 0.3s; }

        /* Card Custom */
        .card-custom {
            background: white; border: none; border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05); border-top: 4px solid #17a2b8; /* Màu xanh dương User */
        }
        
        .avatar-circle {
            width: 45px; height: 45px; background-color: #e9ecef; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-weight: bold; color: #495057;
            font-size: 18px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .sidebar { left: -260px; }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0; padding: 15px; }
            .overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999; }
            .overlay.active { display: block; }
        }
    </style>
</head>
<body>

<div class="overlay" id="overlay" onclick="toggleMenu()"></div>

<div class="sidebar" id="sidebar">
    <div class="brand"><i class="fa-solid fa-bolt"></i> CHỢ ĐIỆN TỬ</div>
    
    <a href="admin_duyettin.php"><i class="fa-solid fa-chart-pie"></i> Tổng Quan</a>
    <a href="admin_sanpham.php"><i class="fa-solid fa-box"></i> Kho Sản Phẩm</a>
    <a href="admin_users.php" class="active"><i class="fa-solid fa-users-gear"></i> Quản Lý User</a>
    
    <a href="dangxuatadmin.php" class="logout-btn">
        <i class="fa-solid fa-right-from-bracket"></i> Đăng Xuất
    </a>
</div>

<div class="main-content">
    
    <button class="btn btn-light shadow-sm d-md-none mb-3" onclick="toggleMenu()">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Quản Lý Thành Viên</h3>
            <span class="text-muted small">Danh sách người dùng đã đăng ký vào hệ thống</span>
        </div>
    </div>

    <div class="row mb-4">
        <?php
        include '../config/connect.php';
        // Đếm số lượng
        $total_users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
        $verified = $conn->query("SELECT COUNT(*) as c FROM users WHERE is_verified=1")->fetch_assoc()['c'];
        $unverified = $conn->query("SELECT COUNT(*) as c FROM users WHERE is_verified=0")->fetch_assoc()['c'];
        
        // Đếm người mới trong ngày
        $today = date('Y-m-d');
        $new_today = $conn->query("SELECT COUNT(*) as c FROM users WHERE DATE(created_at) = '$today'")->fetch_assoc()['c'];
        ?>
        
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-4 bg-white rounded-4 border-start border-4 border-primary h-100">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3"><i class="fa-solid fa-users text-primary fs-3"></i></div>
                            <div><h6 class="mb-1 text-muted text-uppercase">Tổng Thành Viên</h6><h3 class="mb-0 fw-bold"><?php echo $total_users; ?></h3></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-4 bg-white rounded-4 border-start border-4 border-success h-100">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3"><i class="fa-solid fa-user-check text-success fs-3"></i></div>
                            <div><h6 class="mb-1 text-muted text-uppercase">Đã Xác Thực</h6><h3 class="mb-0 fw-bold"><?php echo $verified; ?></h3></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card border-0 shadow-sm p-4 bg-white rounded-4 border-start border-4 border-info h-100">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3"><i class="fa-solid fa-user-plus text-info fs-3"></i></div>
                            <div><h6 class="mb-1 text-muted text-uppercase">Mới Hôm Nay</h6><h3 class="mb-0 fw-bold">+<?php echo $new_today; ?></h3></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
                <h6 class="text-center fw-bold text-secondary mb-3">Tỷ Lệ Xác Thực</h6>
                <div style="height: 180px; position: relative; display: flex; justify-content: center;">
                    <canvas id="userChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="fa-solid fa-users-gear text-primary me-2"></i> Tài Khoản Hệ Thống
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="userTable" width="100%">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">STT</th>
                        <th>Họ Tên</th>
                        <th>Liên Hệ</th>
                        <th>Ngày Tham Gia</th>
                        <th>Trạng Thái</th>
                        <th class="text-center">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM users ORDER BY id DESC";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        $stt = 1;
                        while($row = $result->fetch_assoc()) {
                            $firstLetter = strtoupper(substr($row['name'], 0, 1));
                            
                            $verifyBadge = $row['is_verified'] 
                                ? '<span class="badge bg-success rounded-pill px-3"><i class="fa-solid fa-check-circle me-1"></i> Đã xác thực</span>' 
                                : '<span class="badge bg-secondary rounded-pill px-3">Chưa xác thực</span>';

                            echo "<tr>";
                            echo "<td class='text-center fw-bold text-muted'>" . $stt++ . "</td>";
                            echo "<td>
                                    <div class='d-flex align-items-center'>
                                        <div class='avatar-circle me-3 shadow-sm'>$firstLetter</div>
                                        <div>
                                            <span class='fw-bold text-dark'>" . $row["name"] . "</span>
                                            <div class='small text-muted'>ID: #" . $row["id"] . "</div>
                                        </div>
                                    </div>
                                  </td>";
                            echo "<td>
                                    <div><i class='fa-regular fa-envelope text-primary me-2'></i> " . $row["email"] . "</div>
                                    <div class='mt-1 text-muted small'><i class='fa-solid fa-phone me-2'></i> " . ($row["phone"] ? $row["phone"] : '---') . "</div>
                                  </td>";
                            echo "<td>" . date("d/m/Y", strtotime($row["created_at"])) . "</td>";
                            echo "<td>" . $verifyBadge . "</td>";
                            
                            echo "<td class='text-center'>
                                    <button onclick=\"confirmDelete(" . $row['id'] . ")\" class='btn btn-sm btn-outline-danger shadow-sm px-3 rounded-pill'>
                                        <i class='fa-solid fa-user-xmark me-1'></i> Xóa
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

    // 1. DataTables
    $(document).ready(function () {
        $('#userTable').DataTable({
            language: {
                "decimal":        "",
                "emptyTable":     "Chưa có thành viên nào",
                "info":           "Hiện _START_ đến _END_ của _TOTAL_ thành viên",
                "infoEmpty":      "Không có dữ liệu",
                "infoFiltered":   "(lọc từ _MAX_ thành viên)",
                "lengthMenu":     "Hiện _MENU_ dòng",
                "loadingRecords": "Đang tải...",
                "processing":     "Đang xử lý...",
                "search":         "Tìm thành viên:",
                "zeroRecords":    "Không tìm thấy kết quả",
                "paginate": { "first": "Đầu", "last": "Cuối", "next": ">", "previous": "<" }
            },
            pageLength: 10
        });
    });

    // 2. Chart
    const ctx = document.getElementById('userChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Đã xác thực', 'Chưa xác thực'],
            datasets: [{
                data: [<?php echo $verified; ?>, <?php echo $unverified; ?>],
                backgroundColor: ['#28a745', '#6c757d'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 3. SweetAlert Delete
    function confirmDelete(id) {
        Swal.fire({
            title: 'Xóa tài khoản này?',
            text: "Cảnh báo: Toàn bộ tin đăng của họ cũng sẽ bị xóa vĩnh viễn!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa ngay!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `../api/xuly_xoa_user.php?id=${id}`;
            }
        })
    }
</script>

</body>
</html>