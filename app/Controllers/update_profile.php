<?php
session_start();
include '../../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$id = $_SESSION['user_id'];

// Sử dụng prepared statement để lấy thông tin an toàn hơn
$stmt = $conn->prepare("SELECT name, phone, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_assoc();
$stmt->close();

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $bio = trim($_POST['bio']);

    // Sử dụng prepared statement để cập nhật an toàn hơn
    $stmt_update = $conn->prepare("UPDATE users SET name=?, phone=?, bio=? WHERE id=?");
    $stmt_update->bind_param("sssi", $name, $phone, $bio, $id);
    $stmt_update->execute();
    $stmt_update->close();

    echo "<script>alert('Cập nhật thành công!'); window.location='../../resources/views/user/profile.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Cập nhật thông tin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card p-4 shadow">

            <h3 class="mb-3 text-center text-danger fw-bold">Cập nhật thông tin cá nhân</h3>

            <form method="POST" class="p-3">

                <div class="mb-3">
                    <label class="form-label fw-bold">Họ tên</label>
                    <input type="text" name="name" class="form-control form-control-lg" value="<?= $users['name'] ?>"
                        required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control form-control-lg" value="<?= $users['phone'] ?>"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Bio</label>
                    <input type="text" name="bio" class="form-control form-control-lg" value="<?= $users['bio'] ?>"
                        required>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-danger px-4" name="submit"> Lưu thay đổi</button>
                    <a href="../../resources/views/user/profile.php" class="btn btn-secondary px-4">Hủy</a>
                </div>

            </form>

        </div>
    </div>

</body>

</html>