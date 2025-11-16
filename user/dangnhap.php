<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php
session_start();
include '../config/connect.php';

if (isset($_POST['login_user'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Tìm user theo email
    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('location: index.php');
            exit;
        } else {
            echo '<div class="alert alert-danger text-center">Sai mật khẩu!</div>';
        }
    } else {
        echo '<div class="alert alert-danger text-center">Email không tồn tại!</div>';
    }
    $stmt->close();
}
?>
    <div class="d-flex justify-content-center align-items-center" style="min-height:100vh; background:#ffffff;">
        <div class="card shadow-lg border-0" style="width: 400px;">
            <div class="card-body">
                <h3 class="text-center text-danger fw-bold mb-4"> Đăng nhập User</h3>
                <form method="post" action="dangnhap.php">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Nhập email..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-danger">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..."
                            required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" name="login_user" class="btn btn-danger fw-bold">
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