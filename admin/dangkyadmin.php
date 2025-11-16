<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo tài khoản Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mb-4 text-center text-danger fw-bold">TẠO TÀI KHOẢN ADMIN</h2>

                <?php
include '../config/connect.php';

if (isset($_POST['create'])) {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Kiểm tra xác nhận mật khẩu
    if ($password !== $confirm_password) {
        echo '<div class="alert alert-danger text-center">Mật khẩu xác nhận không khớp!</div>';
    } else {
        // Hash mật khẩu
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Kiểm tra username đã tồn tại chưa
        $check = $conn->prepare("SELECT id FROM admins WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo '<div class="alert alert-danger text-center">Tên đăng nhập đã tồn tại!</div>';
        } else {
            // Tạo admin
            $stmt = $conn->prepare("INSERT INTO admins (fullname, username, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $fullname, $username, $password_hash, $role);

            if ($stmt->execute()) {
                echo '<div class="alert alert-success text-center">
                        Tạo Admin thành công!<br>
                        <strong>Tên đăng nhập:</strong> ' . htmlspecialchars($username) . '<br>
                        <a href="dangnhapadmin.php" class="btn btn-primary mt-2">Đăng nhập ngay</a>
                      </div>';
            } else {
                echo '<div class="alert alert-danger text-center">Có lỗi xảy ra khi tạo tài khoản!</div>';
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>


                <form method="post" class="border p-4 rounded shadow">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Họ và tên</label>
                        <input type="text" class="form-control" name="fullname" required placeholder="Nhập tên đầy đủ">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên đăng nhập</label>
                        <input type="text" class="form-control" name="username" required placeholder="Tên đăng nhập">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mật khẩu</label>
                        <input type="password" class="form-control" name="password" required minlength="6"
                            placeholder="Nhập mật khẩu">
                        <small class="text-muted">Tối thiểu 6 ký tự, nên có chữ hoa, số và ký tự đặc biệt</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Xác nhận mật khẩu</label>
                        <input type="password" class="form-control" name="confirm_password" required
                            placeholder="Nhập lại mật khẩu">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Vai trò</label>
                        <select class="form-select" name="role" required>
                            <option value="support">Support</option>
                            <option value="moderator">Moderator</option>
                            <option value="superadmin">Superadmin</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="text-center">
                        <button type="submit" name="create" class="btn btn-danger px-5 fw-bold">TẠO ADMIN NGAY</button>
                    </div>
                    <div class="text-center mt-3">
                        <small>Đã có tài khoản? <a href="dangnhapadmin.php" class="text-warning">Đăng
                                nhập</a></small>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>