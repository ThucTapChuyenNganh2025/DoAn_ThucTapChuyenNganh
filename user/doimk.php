<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$id = $_SESSION['user_id'];

if (isset($_POST['submit'])) {
    $old = $_POST['old_password']; 
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Lấy mật khẩu hiện tại từ DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_assoc();
    $stmt->close();

    // Kiểm tra mật khẩu cũ
    if (!password_verify($old, $users['password'])) {
        $error ="Mật khẩu cũ không đúng";
    } elseif ($new !== $confirm) {
        $error = "Mật khẩu mới không khớp!";
    } else {
        // Hash mật khẩu mới
        $new_hashed = password_hash($new, PASSWORD_BCRYPT);

        // Cập nhật mật khẩu
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $new_hashed, $id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Đổi mật khẩu thành công!'); window.location='profile.php';</script>";
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card p-4 shadow">

            <h2 class="mb-3 text-center text-danger fw-bold">Đổi mật khẩu</h2>

            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

            <form method="POST">

                <div class="mb-3">
                    <label>Mật khẩu hiện tại</label>
                    <input type="password" name="old_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Mật khẩu mới</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Nhập lại mật khẩu mới</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>

                <button class="btn btn-danger" name="submit">Đổi mật khẩu</button>
                <a href="profile.php" class="btn btn-danger">Hủy</a>
            </form>

        </div>
    </div>

</body>

</html>