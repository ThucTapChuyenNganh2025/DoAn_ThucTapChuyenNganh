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
    <title>Quản Lý Kho Sản Phẩm</title>
    
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
            box-shadow: 0 0 20px rgba(0,0,0,0.05); border-top: 4px solid #1cc88a; /* Xanh lá đặc trưng */
        }
        
        /* Ảnh sản phẩm */
        .product-img {
            width: 50px; height: 50px; object-fit: cover; border-radius: 8px;
            border: 1px solid #eee; transition: transform 0.2s; cursor: zoom-in;
        }
        .product-img:hover { transform: scale(2.5); z-index: 100; position: relative; border: 2px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }

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
    <a href="admin_sanpham.php" class="active"><i class="fa-solid fa-box"></i> Kho Sản Phẩm</a>
    <a href="admin_users.php"><i class="fa-solid fa-users-gear"></i> Quản Lý User</a>
    
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
            <h3 class="fw-bold text-dark mb-0">Quản Lý Kho Sản Phẩm</h3>
            <span class="text-muted small">Kiểm soát toàn bộ tin đăng trên hệ thống</span>
        </div>
        
        <div class="d-flex gap-2">
            <?php if(isset($_GET['status'])): ?>
                <a href="admin_sanpham.php" class="btn btn-outline-secondary shadow-sm rounded-pill px-3">
                    <i class="fa-solid fa-rotate-left me-1"></i> Xem Tất Cả
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mb-4">
        <?php
        include '../config/connect.php';
        // Đếm số lượng
        $total = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
        $approved = $conn->query("SELECT COUNT(*) as c FROM products WHERE status='approved'")->fetch_assoc()['c'];
        $pending = $conn->query("SELECT COUNT(*) as c FROM products WHERE status='pending'")->fetch_assoc()['c'];
        $rejected = $conn->query("SELECT COUNT(*) as c FROM products WHERE status='rejected'")->fetch_assoc()['c'];
        ?>
        
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm p-4 bg-white rounded-4 border-start border-4 border-primary h-100">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3"><i class="fa-solid fa-box text-primary fs-3"></i></div>
                            <div><h6 class="mb-1 text-muted text-uppercase">Tổng Sản Phẩm</h6><h3 class="mb-0 fw-bold"><?php echo $total; ?></h3></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm p-4 bg-white rounded-4 border-start border-4 border-success h-100">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3"><i class="fa-solid fa-check-circle text-success fs-3"></i></div>
                            <div><h6 class="mb-1 text-muted text-uppercase">Đang Hiển Thị</h6><h3 class="mb-0 fw-bold"><?php echo $approved; ?></h3></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm p-4 bg-white rounded-4 border-start border-4 border-warning h-100">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3"><i class="fa-solid fa-clock text-warning fs-3"></i></div>
                            <div><h6 class="mb-1 text-muted text-uppercase">Chờ Phê Duyệt</h6><h3 class="mb-0 fw-bold"><?php echo $pending; ?></h3></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card border-0 shadow-sm p-4 bg-white rounded-4 border-start border-4 border-danger h-100">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3"><i class="fa-solid fa-ban text-danger fs-3"></i></div>
                            <div><h6 class="mb-1 text-muted text-uppercase">Đã Từ Chối</h6><h3 class="mb-0 fw-bold"><?php echo $rejected; ?></h3></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 h-100">
                <h6 class="text-center fw-bold text-secondary mb-3">Tỷ Lệ Trạng Thái</h6>
                <div style="height: 200px; position: relative;">
                    <canvas id="productChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 fw-bold text-secondary">
                <i class="fa-solid fa-layer-group text-success me-2"></i> 
                <?php 
                    if(isset($_GET['status']) && $_GET['status'] == 'approved') echo "Danh Sách Đang Hiển Thị (Approved)";
                    else echo "Toàn Bộ Sản Phẩm";
                ?>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="productTable" width="100%">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">ID</th>
                        <th>Hình Ảnh</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Giá Bán</th>
                        <th>Người Bán</th>
                        <th>Trạng Thái</th>
                        <th class="text-center">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $sql = "SELECT products.*, users.name as seller_name,
                            (SELECT filename FROM product_images WHERE product_id = products.id ORDER BY sort_order ASC LIMIT 1) AS thumb
                            FROM products 
                            JOIN users ON products.seller_id = users.id";

                    if(isset($_GET['status'])) {
                        $status_filter = $_GET['status'];
                        $sql .= " WHERE products.status = '$status_filter'";
                    }

                    $sql .= " ORDER BY products.id DESC"; 
                    
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // Badge trạng thái
                            $statusBadge = '';
                            if($row['status'] == 'approved') $statusBadge = '<span class="badge bg-success rounded-pill px-3"><i class="fa-solid fa-check"></i> Đã Duyệt</span>';
                            elseif($row['status'] == 'pending') $statusBadge = '<span class="badge bg-warning text-dark rounded-pill px-3"><i class="fa-solid fa-clock"></i> Chờ Duyệt</span>';
                            elseif($row['status'] == 'rejected') $statusBadge = '<span class="badge bg-danger rounded-pill px-3"><i class="fa-solid fa-ban"></i> Từ Chối</span>';
                            else $statusBadge = '<span class="badge bg-secondary rounded-pill px-3">Ẩn</span>';

                            $img_src = !empty($row['thumb']) ? '../' . $row['thumb'] : 'https://via.placeholder.com/50';

                            echo "<tr>";
                            echo "<td class='text-center fw-bold text-muted'>#" . $row["id"] . "</td>";
                            echo "<td><img src='$img_src' class='product-img shadow-sm'></td>";
                            echo "<td class='fw-bold text-primary'>" . $row["title"] . "</td>";
                            echo "<td class='text-danger fw-bold'>" . number_format($row["price"]) . " đ</td>";
                            echo "<td><div class='d-flex align-items-center'><i class='fa-regular fa-user-circle me-2 text-muted'></i>" . $row["seller_name"] . "</div></td>";
                            echo "<td>" . $statusBadge . "</td>";
                            
                            echo "<td class='text-center'>
                                    
                                    
                                    <button onclick=\"confirmDelete(" . $row["id"] . ")\" class='btn btn-sm btn-outline-danger shadow-sm rounded-circle' style='width:32px;height:32px;padding:0;line-height:30px' title='Xóa tin'>
                                        <i class='fa-solid fa-trash'></i>
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

    // 1. DataTables Tiếng Việt
    $(document).ready(function () {
        $('#productTable').DataTable({
            language: {
                "decimal":        "",
                "emptyTable":     "Hiện chưa có sản phẩm nào",
                "info":           "Hiện _START_ đến _END_ của _TOTAL_ sản phẩm",
                "infoEmpty":      "Không có dữ liệu",
                "infoFiltered":   "(lọc từ _MAX_ sản phẩm)",
                "lengthMenu":     "Hiển thị _MENU_ dòng",
                "loadingRecords": "Đang tải...",
                "processing":     "Đang xử lý...",
                "search":         "Tìm nhanh:",
                "zeroRecords":    "Không tìm thấy kết quả nào",
                "paginate": { "first": "Đầu", "last": "Cuối", "next": ">", "previous": "<" }
            },
            pageLength: 5,
            order: [[0, 'desc']]
        });
    });

    // 2. Biểu Đồ Tròn (Chart.js)
    const ctx = document.getElementById('productChart');
    new Chart(ctx, {
        type: 'doughnut', // Biểu đồ hình vành khuyên
        data: {
            labels: ['Đã Duyệt', 'Chờ Duyệt', 'Từ Chối'],
            datasets: [{
                data: [<?php echo $approved; ?>, <?php echo $pending; ?>, <?php echo $rejected; ?>],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'], // Xanh, Vàng, Đỏ
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 3. SweetAlert2 Xóa
    function confirmDelete(id) {
        Swal.fire({
            title: 'Xóa vĩnh viễn?',
            text: "Hành động này không thể hoàn tác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa ngay!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `../api/xuly_xoa_sanpham.php?id=${id}`;
            }
        })
    }
</script>

</body>
</html>