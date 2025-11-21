<?php
session_start();
include '../config/connect.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT name, email, phone, bio FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thông tin cá nhân</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow-lg p-4 border-0">
                    <h2 class="mb-4 text-center text-danger fw-bold">Thông Tin Cá Nhân</h2>

                    <form>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Họ tên:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($users['name']) ?>" " disabled>
                        </div>

                        <div class=" mb-3">
                            <label class="form-label fw-bold">Số điện thoại:</label>
                            <input type="text" class="form-control" value="<?= $users['phone'] ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Bio:</label>
                            <textarea class="form-control" rows="3" disabled><?= $users['bio'] ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="update_profile.php" class="btn btn-danger">Cập nhật hồ sơ</a>
                            <a href="doimk.php" class="btn btn-danger text-white">Đổi mật khẩu</a>
                            <a href="index.php" class="btn btn-danger">Về trang chủ</a>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>

</body>


</html>