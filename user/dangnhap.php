<?php
session_start();
include '../config/connect.php';

$error = '';
$requested_next = isset($_GET['next']) ? trim($_GET['next']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_user'])) {
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

            // Tạo URL redirect an toàn: đặt mặc định về index của project (không phải root server)
            $script = $_SERVER['SCRIPT_NAME'];
            $projectRoot = dirname(dirname($script)); // e.g. /DoAn_ThucTapChuyenNganh
            if ($projectRoot === '/' || $projectRoot === '\\') {
                $projectRoot = '';
            }
            $next = $projectRoot . '/index.php';

            if ($requested_next !== '') {
                $candidate = $requested_next;
                // Kiểm tra không chứa scheme/host để tránh open redirect
                if (strpos($candidate, '://') === false && strpos($candidate, '//') === false && strpos($candidate, 'http') === false) {
                    if (strpos($candidate, '/') === 0) {
                        // Nếu bắt đầu bằng '/', hiểu là đường dẫn trong server; prepend project root nếu cần
                        if (strpos($candidate, $projectRoot) === 0) {
                            $next = $candidate;
                        } else {
                            $next = $projectRoot . $candidate;
                        }
                    } else {
                        // Bình thường, tạo đường dẫn dựa trên thư mục của script hiện tại
                        $base = dirname($script); // e.g. /DoAn_ThucTapChuyenNganh/user
                        $next = $base . '/' . $candidate; // => /DoAn_ThucTapChuyenNganh/user/user_dangtin.php
                    }
                }
            }

            header('Location: ' . $next);
            exit;
        } else {
            $error = 'Sai mật khẩu!';
        }
    } else {
        $error = 'Email không tồn tại!';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="d-flex justify-content-center align-items-center" style="min-height:100vh; background:#ffffff;">
        <div class="card shadow-lg border-0" style="width: 400px;">
            <div class="card-body">
                <h3 class="text-center text-danger fw-bold mb-4"> Đăng nhập User</h3>
                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <form method="post" action="dangnhap.php<?php echo $requested_next ? '?next=' . urlencode($requested_next) : ''; ?>">
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