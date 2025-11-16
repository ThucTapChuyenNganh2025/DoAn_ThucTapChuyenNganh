<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php
session_start();
include '../../config/connect.php';

if (isset($_POST['login_admin'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Tìm admin theo username
    $stmt = $conn->prepare("SELECT id, fullname, username, password, role FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Tạo một chuỗi hash giả để ngăn chặn tấn công dò thời gian (timing attack)
    $hashed_password = '$2y$10$thisisafakehashthatwillnevermatch'; 

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        $hashed_password = $admin['password'];
    }

    // Sử dụng password_verify để so sánh mật khẩu một cách an toàn
    if (password_verify($password, $hashed_password)) {
        // Đăng nhập thành công
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['fullname'];
        $_SESSION['admin_role'] = $admin['role'];
        header('location:admin_duyettin.php'); // Sửa lỗi 404: Chuyển hướng đến trang admin chính
        exit;
    } else {
        // Gộp chung 2 lỗi để tăng bảo mật, tránh để lộ thông tin tài khoản nào tồn tại
        echo '<div class="alert alert-danger text-center">Tên đăng nhập hoặc mật khẩu không chính xác!</div>';
    }
    $stmt->close();
}
?>
    <div class="d-flex justify-content-center align-items-center" style="min-height:100vh; background:#ffffff;">
        <div class="card shadow-lg border-0" style="width: 500px;">
            <div class="card-body">
                <h2 class="text-center text-danger fw-bold mb-4"> Đăng nhập Admin</h2>
                <form method="post" action="dangnhapadmin.php">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">Tên đăng nhập</label>
                        <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập..."
                            required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..."
                            required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" name="login_admin" class="btn btn-danger fw-bold">
                            Đăng nhập
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>