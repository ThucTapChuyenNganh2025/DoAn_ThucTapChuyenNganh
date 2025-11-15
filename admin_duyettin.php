<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Duyệt Tin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f4f6f9; /* Màu nền xám nhạt sang trọng */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        /* Sidebar (Menu bên trái) */
        .sidebar {
            height: 100vh; /* Cao full màn hình */
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40; /* Màu đen xám chuyên nghiệp */
            padding-top: 20px;
            color: white;
        }
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 16px;
            color: #cfd8dc;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057;
            color: #ff9f43; /* Hover ra màu cam */
            border-left: 4px solid #ff9f43; /* Viền cam khi di chuột */
        }
        .sidebar a.active {
            background-color: #495057;
            color: white;
            border-left: 4px solid #ff9f43; /* Viền cam cho mục đang chọn */
        }
        .sidebar .brand {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #ff9f43; /* Màu cam thương hiệu */
        }
        
        /* Main Content (Nội dung bên phải) */
        .main-content {
            margin-left: 250px; /* Chừa chỗ cho sidebar */
            padding: 30px;
        }
        
        /* Card (Hộp chứa nội dung) */
        .card-custom {
            background: white;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Đổ bóng nhẹ */
            border-top: 5px solid #ff9f43; /* ĐÂY LÀ VIỀN MÀU CAM BẠN YÊU CẦU */
        }
        .table thead {
            background-color: #ff9f43;
            color: white;
        }
        .btn-duyet {
            background-color: #28a745;
            color: white;
        }
        .btn-tuchoi {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-shop"></i> CHỢ ADMIN</div>
    <a href="admin_duyettin.php" class="active"><i class="fa-solid fa-check-double me-2"></i> Duyệt Tin Mới</a>
    <a href="admin_sanpham.php"><i class="fa-solid fa-box-open me-2"></i> Kho Sản Phẩm</a>
    <a href="#"><i class="fa-solid fa-users me-2"></i> Quản Lý User</a>
    <a href="#"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng Xuất</a>
</div>

<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-secondary">Tổng Quan</h3>
        <div class="user-info">
            <span class="me-2 text-dark">Xin chào, <b>Admin</b></span>
            <img src="https://via.placeholder.com/40" class="rounded-circle border border-2 border-warning">
        </div>
    </div>

    <div class="card card-custom p-4">
        <h4 class="mb-4 text-dark"><i class="fa-solid fa-clock text-warning"></i> Danh Sách Cần Phê Duyệt</h4>

        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Giá Bán</th>
                    <th>Người Bán</th>
                    <th>Trạng Thái</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'connect.php';
                $sql = "SELECT products.*, users.name as seller_name 
                        FROM products 
                        JOIN users ON products.seller_id = users.id 
                        WHERE products.status = 'pending'";
                
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>#" . $row["id"] . "</td>";
                        // Ảnh giả minh họa
                        echo "<td><img src='https://via.placeholder.com/60' class='rounded border'></td>";
                        echo "<td class='fw-bold'>" . $row["title"] . "</td>";
                        echo "<td class='text-danger fw-bold'>" . number_format($row["price"]) . " đ</td>";
                        echo "<td>" . $row["seller_name"] . "</td>";
                        echo "<td><span class='badge bg-warning text-dark rounded-pill'>Wait Approval</span></td>";
                        echo "<td>
                                <a href='xuly_duyet.php?id=" . $row["id"] . "&action=approve' class='btn btn-sm btn-duyet shadow-sm'><i class='fa-solid fa-check'></i> Duyệt</a>
                                <a href='xuly_duyet.php?id=" . $row["id"] . "&action=reject' class='btn btn-sm btn-tuchoi shadow-sm'><i class='fa-solid fa-xmark'></i> Xóa</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center py-5 text-muted'>
                            <i class='fa-solid fa-clipboard-check fa-3x mb-3'></i><br>
                            Hiện không có tin nào chờ duyệt!
                          </td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>