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
include '../config/connect.php';

if (isset($_POST['login_admin'])) {
    $username = trim($_POST['username']);
    // Trim password to avoid accidental whitespace errors from copy/paste
    $password = trim($_POST['password']);

    // Tìm admin theo username
    $stmt = $conn->prepare("SELECT id, fullname, username, password, role FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            // Đăng nhập thành công
            // If the stored hash needs to be rehashed (e.g., algorithm/options changed), rehash it now
            if (password_needs_rehash($admin['password'], PASSWORD_BCRYPT)) {
                $rehash = password_hash($password, PASSWORD_BCRYPT);
                $rehashStmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
                $rehashStmt->bind_param("si", $rehash, $admin['id']);
                $rehashStmt->execute();
                $rehashStmt->close();
            }
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['fullname'];
            $_SESSION['admin_role'] = $admin['role'];
            // Đăng nhập thành công thì chuyển sang trang Duyệt Tin
            header('location:admin_duyettin.php');
            exit;
        } else {
            // If password_verify failed, check for legacy stored plaintext or md5 hashed passwords
            // If admin created before the app used password_hash, they might have plaintext or md5
            $legacyMatch = false;
            // Direct plaintext match (unsafe but supports legacy)
            if ($password === $admin['password']) {
                $legacyMatch = true;
            }
            // MD5 legacy support
            if (!$legacyMatch && md5($password) === $admin['password']) {
                $legacyMatch = true;
            }

            if ($legacyMatch) {
                // Re-hash the password using password_hash for security and update DB
                $newHash = password_hash($password, PASSWORD_BCRYPT);
                $updateStmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
                $updateStmt->bind_param("si", $newHash, $admin['id']);
                $updateStmt->execute();
                $updateStmt->close();

                // Set session and redirect as normal
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['fullname'];
                $_SESSION['admin_role'] = $admin['role'];
                header('location:admin_duyettin.php');
                exit;
            }

            echo '<div class="alert alert-danger text-center">Sai mật khẩu Admin!</div>';
        }
    } else {
        echo '<div class="alert alert-danger text-center">Tên đăng nhập Admin không tồn tại!</div>';
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