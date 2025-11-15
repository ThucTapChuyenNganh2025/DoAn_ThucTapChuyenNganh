<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kho Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* GIỮ NGUYÊN CSS NHƯ TRANG DUYỆT TIN ĐỂ ĐỒNG BỘ */
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        
        .sidebar {
            height: 100vh; width: 250px; position: fixed; top: 0; left: 0;
            background-color: #343a40; padding-top: 20px; color: white;
        }
        .sidebar a {
            padding: 15px 25px; text-decoration: none; font-size: 16px; color: #cfd8dc;
            display: block; transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057; color: #ff9f43; border-left: 4px solid #ff9f43;
        }
        /* Class active này để đánh dấu mình đang ở trang Kho Sản Phẩm */
        .sidebar a.active {
            background-color: #495057; color: white; border-left: 4px solid #ff9f43;
        }
        .sidebar .brand {
            text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 30px; color: #ff9f43;
        }
        .main-content { margin-left: 250px; padding: 30px; }
        
        /* Hộp viền cam */
        .card-custom {
            background: white; border: none; border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-top: 5px solid #ff9f43; /* Viền cam thương hiệu */
        }
        .table thead { background-color: #343a40; color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-shop"></i> CHỢ ADMIN</div>
    <a href="admin_duyettin.php"><i class="fa-solid fa-check-double me-2"></i> Duyệt Tin Mới</a>
    <a href="admin_sanpham.php" class="active"><i class="fa-solid fa-box-open me-2"></i> Kho Sản Phẩm</a>
    <a href="#"><i class="fa-solid fa-users me-2"></i> Quản Lý User</a>
    <a href="#"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng Xuất</a>
</div>

<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-secondary">Quản Lý Sản Phẩm</h3>
        <button class="btn btn-warning text-white shadow-sm"><i class="fa-solid fa-plus"></i> Thêm Mới (Demo)</button>
    </div>

    <div class="card card-custom p-4">
        <h4 class="mb-4 text-dark"><i class="fa-solid fa-database text-warning"></i> Tất Cả Sản Phẩm Trong Hệ Thống</h4>

        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Giá Bán</th>
                    <th>Người Bán</th>
                    <th>Trạng Thái</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'connect.php';
                
                // Lấy tất cả sản phẩm, sắp xếp mới nhất lên đầu
                $sql = "SELECT products.*, users.name as seller_name 
                        FROM products 
                        JOIN users ON products.seller_id = users.id 
                        ORDER BY products.id DESC";
                
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Xử lý màu sắc trạng thái
                        $statusBadge = '';
                        if($row['status'] == 'approved') {
                            $statusBadge = '<span class="badge bg-success"><i class="fa-solid fa-check"></i> Đã Duyệt</span>';
                        } elseif($row['status'] == 'pending') {
                            $statusBadge = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-clock"></i> Chờ Duyệt</span>';
                        } elseif($row['status'] == 'rejected') {
                            $statusBadge = '<span class="badge bg-danger"><i class="fa-solid fa-ban"></i> Từ Chối</span>';
                        }

                        echo "<tr>";
                        echo "<td>#" . $row["id"] . "</td>";
                        echo "<td class='fw-bold text-primary'>" . $row["title"] . "</td>";
                        echo "<td class='fw-bold'>" . number_format($row["price"]) . " đ</td>";
                        echo "<td>" . $row["seller_name"] . "</td>";
                        echo "<td>" . $statusBadge . "</td>";
                        echo "<td>
                                <a href='#' class='btn btn-sm btn-outline-primary'><i class='fa-solid fa-pen'></i> Sửa</a>
                                <a href='#' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Bạn muốn xóa tin này?\")'><i class='fa-solid fa-trash'></i> Xóa</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center py-4'>Chưa có sản phẩm nào!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>