<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Quản Lý User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        
        /* Sidebar */
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
        .sidebar a.active {
            background-color: #495057; color: white; border-left: 4px solid #ff9f43;
        }
        .sidebar .brand {
            text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 30px; color: #ff9f43;
        }
        .main-content { margin-left: 250px; padding: 30px; }
        
        /* Card Custom */
        .card-custom {
            background: white; border: none; border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 5px solid #17a2b8;
        }
        .avatar-circle {
            width: 40px; height: 40px; background-color: #e9ecef; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-weight: bold; color: #495057;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand"><i class="fa-solid fa-shop"></i> CHỢ ADMIN</div>
    <a href="admin_duyettin.php"><i class="fa-solid fa-chart-line me-2"></i> Tổng Quan & Duyệt</a>
    <a href="admin_sanpham.php"><i class="fa-solid fa-box-open me-2"></i> Kho Sản Phẩm</a>
    <a href="admin_users.php" class="active"><i class="fa-solid fa-users me-2"></i> Quản Lý User</a>
    <a href="#"><i class="fa-solid fa-right-from-bracket me-2"></i> Đăng Xuất</a>
</div>

<div class="main-content">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-secondary">Danh Sách Thành Viên</h3>
        <button class="btn btn-info text-white shadow-sm"><i class="fa-solid fa-user-plus"></i> Thêm User (Demo)</button>
    </div>

    <div class="card card-custom p-4">
        <h4 class="mb-4 text-dark"><i class="fa-solid fa-users-gear text-info"></i> Tài Khoản Hệ Thống</h4>

        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Họ Tên</th>
                    <th>Email / SĐT</th>
                    <th>Ngày Tham Gia</th>
                    <th>Xác Thực</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include '../config/connect.php';
                
                $sql = "SELECT * FROM users ORDER BY id DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $firstLetter = strtoupper(substr($row['name'], 0, 1));
                        
                        $verifyBadge = $row['is_verified'] 
                            ? '<span class="badge bg-success"><i class="fa-solid fa-check-circle"></i> Đã xác thực</span>' 
                            : '<span class="badge bg-secondary">Chưa xác thực</span>';

                        echo "<tr>";
                        echo "<td>#" . $row["id"] . "</td>";
                        echo "<td>
                                <div class='d-flex align-items-center'>
                                    <div class='avatar-circle me-2'>$firstLetter</div>
                                    <span class='fw-bold'>" . $row["name"] . "</span>
                                </div>
                              </td>";
                        echo "<td>
                                <i class='fa-regular fa-envelope text-muted'></i> " . $row["email"] . "<br>
                                <i class='fa-solid fa-phone text-muted'></i> " . ($row["phone"] ? $row["phone"] : 'Chưa có') . "
                              </td>";
                        echo "<td>" . date("d/m/Y", strtotime($row["created_at"])) . "</td>";
                        echo "<td>" . $verifyBadge . "</td>";
                        
                        // --- ĐOẠN NÀY ĐÃ SỬA LINK XÓA CHO BẠN ---
                        echo "<td>
                                <a href='xuly_xoa_user.php?id=" . $row["id"] . "' 
                                   class='btn btn-sm btn-outline-danger' 
                                   onclick='return confirm(\"Cảnh báo: Xóa user sẽ xóa luôn tin đăng của họ! Bạn chắc chứ?\")'>
                                    <i class='fa-solid fa-user-xmark'></i> Xóa
                                </a>
                              </td>";
                        // ----------------------------------------
                        
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center py-4'>Chưa có thành viên nào!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>