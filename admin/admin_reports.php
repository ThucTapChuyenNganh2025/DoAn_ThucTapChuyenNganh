<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: dangnhapadmin.php");
    exit;
}

include '../config/connect.php';

// Xử lý hành động
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $report_id = isset($_POST['report_id']) ? (int)$_POST['report_id'] : 0;
    
    if ($report_id > 0) {
        if ($action === 'reviewed') {
            $update_sql = "UPDATE reports SET status = 'reviewed' WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param('i', $report_id);
            if ($update_stmt->execute()) {
                $message = 'Đã đánh dấu báo cáo là đã xem xét';
                $message_type = 'success';
            }
            $update_stmt->close();
        } elseif ($action === 'delete_product') {
            $get_sql = "SELECT product_id FROM reports WHERE id = ?";
            $get_stmt = $conn->prepare($get_sql);
            $get_stmt->bind_param('i', $report_id);
            $get_stmt->execute();
            $get_res = $get_stmt->get_result();
            
            if ($row = $get_res->fetch_assoc()) {
                $product_id = (int)$row['product_id'];
                
                $del_sql = "UPDATE products SET status = 'hidden' WHERE id = ?";
                $del_stmt = $conn->prepare($del_sql);
                $del_stmt->bind_param('i', $product_id);
                
                if ($del_stmt->execute()) {
                    $upd_all = "UPDATE reports SET status = 'reviewed' WHERE product_id = ?";
                    $upd_stmt = $conn->prepare($upd_all);
                    $upd_stmt->bind_param('i', $product_id);
                    $upd_stmt->execute();
                    $upd_stmt->close();
                    
                    $message = 'Đã ẩn sản phẩm và đánh dấu tất cả báo cáo liên quan';
                    $message_type = 'success';
                }
                $del_stmt->close();
            }
            $get_stmt->close();
        } elseif ($action === 'delete_permanently') {
            $get_sql = "SELECT product_id FROM reports WHERE id = ?";
            $get_stmt = $conn->prepare($get_sql);
            $get_stmt->bind_param('i', $report_id);
            $get_stmt->execute();
            $get_res = $get_stmt->get_result();
            
            if ($row = $get_res->fetch_assoc()) {
                $product_id = (int)$row['product_id'];
                
                $conn->query("DELETE FROM product_images WHERE product_id = $product_id");
                $conn->query("DELETE FROM favorites WHERE product_id = $product_id");
                $conn->query("DELETE FROM ratings WHERE product_id = $product_id");
                $conn->query("DELETE FROM reports WHERE product_id = $product_id");
                $conn->query("DELETE FROM messages WHERE conversation_id IN (SELECT id FROM conversations WHERE product_id = $product_id)");
                $conn->query("DELETE FROM conversations WHERE product_id = $product_id");
                
                $del_sql = "DELETE FROM products WHERE id = ?";
                $del_stmt = $conn->prepare($del_sql);
                $del_stmt->bind_param('i', $product_id);
                
                if ($del_stmt->execute()) {
                    $message = 'Đã xóa vĩnh viễn sản phẩm và tất cả dữ liệu liên quan';
                    $message_type = 'success';
                }
                $del_stmt->close();
            }
            $get_stmt->close();
        }
    }
}

// Lấy filter
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'pending';

// Đếm số lượng
$count_pending = $conn->query("SELECT COUNT(*) AS cnt FROM reports WHERE status = 'pending'")->fetch_assoc()['cnt'];
$count_reviewed = $conn->query("SELECT COUNT(*) AS cnt FROM reports WHERE status = 'reviewed'")->fetch_assoc()['cnt'];

// Lấy danh sách báo cáo
$reports_sql = "SELECT r.id, r.reason, r.status, r.created_at,
                       p.id AS product_id, p.title AS product_title, p.status AS product_status,
                       u.id AS reporter_id, u.name AS reporter_name,
                       seller.id AS seller_id, seller.name AS seller_name,
                       (SELECT COUNT(*) FROM reports r2 WHERE r2.product_id = r.product_id) AS total_reports
                FROM reports r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON r.reporter_id = u.id
                LEFT JOIN users seller ON p.seller_id = seller.id
                WHERE r.status = ?
                ORDER BY r.created_at DESC
                LIMIT 100";
$reports_stmt = $conn->prepare($reports_sql);
$reports_stmt->bind_param('s', $filter_status);
$reports_stmt->execute();
$reports_res = $reports_stmt->get_result();

$reports = [];
while ($row = $reports_res->fetch_assoc()) {
    $reports[] = $row;
}
$reports_stmt->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Báo Cáo Vi Phạm</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="../js/toast.js"></script>

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
            box-shadow: 0 0 20px rgba(0,0,0,0.05); border-top: 4px solid #e74a3b;
        }
        
        /* Report Card */
        .report-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid #aeb4c6;
            transition: all 0.2s;
        }
        .report-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .report-card.high-priority {
            border-left: 4px solid #e74a3b;
            background: linear-gradient(to right, rgba(231, 74, 59, 0.03), #fff);
        }
        .report-reason {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 8px;
            margin: 12px 0;
            border-left: 3px solid #4e73df;
        }
        .badge-reports {
            background: linear-gradient(45deg, #e74a3b, #be2617);
            color: #fff;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 15px;
            font-weight: 600;
        }
        
        /* Filter tabs */
        .filter-tabs .nav-link {
            color: #666;
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 10px 10px 0 0;
            background: #e9ecef;
            margin-right: 5px;
        }
        .filter-tabs .nav-link.active {
            color: #fff;
            background: linear-gradient(45deg, #4e73df, #224abe);
        }
        .filter-tabs .nav-link .badge {
            font-size: 10px;
            padding: 3px 8px;
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
    <a href="admin_users.php"><i class="fa-solid fa-users-gear"></i> Quản Lý User</a>
    <a href="admin_reports.php" class="active"><i class="fa-solid fa-flag"></i> Báo Cáo Vi Phạm</a>
    
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
            <h3 class="fw-bold text-dark mb-0">Quản Lý Báo Cáo Vi Phạm</h3>
            <span class="text-muted small">Xử lý các báo cáo từ người dùng về tin đăng vi phạm</span>
        </div>
    </div>
    
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show shadow-sm">
        <i class="fa-solid fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-4 border-start border-4 border-danger h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                        <i class="fa-solid fa-clock text-danger fs-3"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-muted text-uppercase">Chờ Xử Lý</h6>
                        <h3 class="mb-0 fw-bold"><?php echo $count_pending; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-4 border-start border-4 border-success h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="fa-solid fa-check-circle text-success fs-3"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-muted text-uppercase">Đã Xử Lý</h6>
                        <h3 class="mb-0 fw-bold"><?php echo $count_reviewed; ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-4 border-start border-4 border-primary h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="fa-solid fa-flag text-primary fs-3"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 text-muted text-uppercase">Tổng Báo Cáo</h6>
                        <h3 class="mb-0 fw-bold"><?php echo $count_pending + $count_reviewed; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Tabs -->
    <ul class="nav filter-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo $filter_status === 'pending' ? 'active' : ''; ?>" href="?status=pending">
                <i class="fa-solid fa-clock me-2"></i> Chờ Xử Lý 
                <span class="badge bg-<?php echo $filter_status === 'pending' ? 'light text-dark' : 'danger'; ?> ms-2"><?php echo $count_pending; ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $filter_status === 'reviewed' ? 'active' : ''; ?>" href="?status=reviewed">
                <i class="fa-solid fa-check-double me-2"></i> Đã Xử Lý 
                <span class="badge bg-<?php echo $filter_status === 'reviewed' ? 'light text-dark' : 'secondary'; ?> ms-2"><?php echo $count_reviewed; ?></span>
            </a>
        </li>
    </ul>
    
    <!-- Reports List -->
    <div class="card card-custom p-4">
        <?php if (empty($reports)): ?>
        <div class="text-center text-muted py-5">
            <i class="fa-solid fa-<?php echo $filter_status === 'pending' ? 'inbox' : 'check-circle text-success'; ?> fa-4x mb-3 opacity-50"></i>
            <h5><?php echo $filter_status === 'pending' ? 'Không có báo cáo nào cần xử lý' : 'Danh sách báo cáo đã xử lý trống'; ?></h5>
            <p class="text-muted"><?php echo $filter_status === 'pending' ? 'Tuyệt vời! Hiện tại không có tin đăng nào bị báo cáo.' : ''; ?></p>
        </div>
        <?php else: ?>
        
        <?php foreach ($reports as $report): ?>
        <div class="report-card <?php echo $report['total_reports'] >= 3 ? 'high-priority' : ''; ?>">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($report['product_title'] ?? 'Sản phẩm đã xóa'); ?></h6>
                        <?php if ($report['total_reports'] > 1): ?>
                        <span class="badge-reports">
                            <i class="fa-solid fa-exclamation-triangle me-1"></i><?php echo $report['total_reports']; ?> báo cáo
                        </span>
                        <?php endif; ?>
                        <?php if ($report['product_status'] === 'hidden'): ?>
                        <span class="badge bg-secondary">Đã ẩn</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="report-reason">
                        <small class="text-muted d-block mb-1"><i class="fa-solid fa-quote-left me-1"></i> Lý do báo cáo:</small>
                        <span><?php echo nl2br(htmlspecialchars($report['reason'])); ?></span>
                    </div>
                    
                    <div class="d-flex gap-4 text-muted small flex-wrap">
                        <span><i class="fa-solid fa-user-shield me-1"></i> Người báo cáo: <strong><?php echo htmlspecialchars($report['reporter_name']); ?></strong></span>
                        <span><i class="fa-solid fa-store me-1"></i> Người bán: <strong><?php echo htmlspecialchars($report['seller_name'] ?? 'N/A'); ?></strong></span>
                        <span><i class="fa-regular fa-clock me-1"></i> <?php echo date('d/m/Y H:i', strtotime($report['created_at'])); ?></span>
                    </div>
                </div>
                
                <div class="col-md-4 mt-3 mt-md-0">
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                        <?php if ($report['product_id']): ?>
                        <a href="/DoAn_ThucTapChuyenNganh/product.php?id=<?php echo $report['product_id']; ?>" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fa-solid fa-eye me-1"></i> Xem tin
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($filter_status === 'pending'): ?>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                            <input type="hidden" name="action" value="reviewed">
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="fa-solid fa-check"></i> Bỏ qua
                            </button>
                        </form>
                        
                        <?php if ($report['product_status'] !== 'hidden'): ?>
                        <form method="POST" class="d-inline" id="hide-form-<?php echo $report['id']; ?>">
                            <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                            <input type="hidden" name="action" value="delete_product">
                            <button type="button" class="btn btn-warning btn-sm" onclick="showConfirm('Ẩn sản phẩm này khỏi trang web?', function() { document.getElementById('hide-form-<?php echo $report['id']; ?>').submit(); }, null, { title: 'Xác nhận ẩn', confirmText: 'Ẩn sản phẩm', type: 'warning' })">
                                <i class="fa-solid fa-eye-slash"></i> Ẩn
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <form method="POST" class="d-inline" id="delete-form-<?php echo $report['id']; ?>">
                            <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                            <input type="hidden" name="action" value="delete_permanently">
                            <button type="button" class="btn btn-danger btn-sm" onclick="showConfirm('⚠️ XÓA VĨNH VIỄN sản phẩm này? Hành động này không thể hoàn tác!', function() { document.getElementById('delete-form-<?php echo $report['id']; ?>').submit(); }, null, { title: 'Xác nhận xóa', confirmText: 'Xóa vĩnh viễn', type: 'danger' })">
                                <i class="fa-solid fa-trash"></i> Xóa
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleMenu() {
    document.getElementById('sidebar').classList.toggle('show');
    document.getElementById('overlay').classList.toggle('active');
}
</script>
</body>
</html>
