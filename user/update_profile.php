<?php
session_start();
include '../config/connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dangnhap.php");
    exit();
}

$id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $id";
$query = mysqli_query($conn, $sql);
$users = mysqli_fetch_assoc($query);

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $bio=$_POST['bio'];

    $update = "UPDATE users SET name='$name', phone='$phone',bio='$bio' WHERE id=$id";
    mysqli_query($conn, $update);

    echo "<script>alert('Cập nhật thành công!'); window.location='profile.php';</script>";
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
                    <a href="profile.php" class="btn btn-danger px-4">Hủy</a>
                </div>

            </form>

        </div>
    </div>

</body>

</html>