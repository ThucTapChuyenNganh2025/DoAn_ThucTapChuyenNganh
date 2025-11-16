<?php
include '../../config/connect.php'; // file kết nối CSDL

// Lấy danh sách tỉnh/thành phố và lưu vào một mảng
$locations = [];
$locations_query = $conn->query("SELECT id, province FROM locations ORDER BY province ASC");
if ($locations_query && $locations_query->num_rows > 0) {
    while ($loc = $locations_query->fetch_assoc()) {
        $locations[] = $loc; // Mảng locations bây giờ sẽ chứa các phần tử có 'id' và 'province'
    }
}

// Xử lý form đăng ký
$error_message = ''; // Biến để lưu thông báo lỗi

if (isset($_POST['create'])) {  
    $name         = trim($_POST['name']);
    $email        = trim($_POST['email']);
    $phone        = trim($_POST['phone']);
    $password     = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $bio          = trim($_POST['bio']);
    $location_id  = $_POST['location_id'];

    // Kiểm tra mật khẩu xác nhận
    if ($password !== $confirm_pass) {
        $error_message = 'Mật khẩu xác nhận không khớp!';
    } else {
        // Kiểm tra email đã tồn tại chưa
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error_message = 'Email này đã tồn tại!';
        } else {
            // Hash mật khẩu
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Thêm user mới
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, bio, location_id, is_verified, created_at) VALUES (?, ?, ?, ?, ?, ?, 0, NOW())");
            $stmt->bind_param("sssssi", $name, $email, $phone, $password_hash, $bio, $location_id);

            if ($stmt->execute()) {
                echo '<script>alert("Đăng ký thành công! Email: ' . htmlspecialchars($email) . '"); window.location.href = "dangnhap.php";</script>';
                exit();
            } else {
                $error_message = 'Có lỗi xảy ra khi đăng ký!';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo tài khoản Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mb-4 text-center text-danger fw-bold">TẠO TÀI KHOẢN USERS</h2>

                <?php
                    // Hiển thị thông báo lỗi nếu có
                    if (!empty($error_message)) {
                        echo '<div class="alert alert-danger text-center">' . $error_message . '</div>';
                    }
                ?>
                <form method="post" class="border p-4 rounded shadow">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Họ và tên</label>
                        <input type="text" class="form-control" name="name" required placeholder="Nhập tên đầy đủ">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Email</label>
                        <input type="email" class="form-control" name="email" required placeholder="email@gmail.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Số điện thoại</label>
                        <input type="tel" class="form-control" name="phone" placeholder="Nhập số điện thoại"
                            pattern="^[0-9]{10}$" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Mật khẩu</label>
                        <input type="password" class="form-control" name="password" required minlength="6"
                            placeholder="Nhập mật khẩu">
                        <small class="text-muted">Tối thiểu 6 ký tự nên có chữ hoa, số và ký tự đặc biệt</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Nhập lại mật khẩu</label>
                        <input type="password" class="form-control" name="confirm_password" required
                            placeholder="Nhập lại mật khẩu">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Giới thiệu ngắn</label>
                        <textarea class="form-control" name="bio" placeholder="Nhập giới thiệu về bạn"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Địa chỉ</label>
                        <select class="form-select" name="location_id">
                            <option value="">-- Chọn Tỉnh/Thành phố --</option>
                            <?php
                                // Sử dụng mảng $locations đã được lấy ở đầu trang
                                foreach ($locations as $loc) {
                                    echo "<option value='" . htmlspecialchars($loc['id']) . "'>" . htmlspecialchars($loc['province']) . "</option>";
                                }
                            ?>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="text-center">
                        <button type="submit" name="create" class="btn btn-danger px-5 fw-bold">TẠO TÀI KHOẢN
                            NGAY</button>
                    </div>
                    <div class="text-center mt-3">
                        <small>Đã có tài khoản? <a href="dangnhap.php" class="text-danger fw-bold">Đăng
                                nhập</a></small>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>