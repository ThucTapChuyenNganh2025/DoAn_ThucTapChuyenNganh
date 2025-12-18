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
include '../config/connect.php'; // file kết nối CSDL

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
        echo '<div class="alert alert-danger text-center">Mật khẩu xác nhận không khớp!</div>';
    } else {
        // Kiểm tra email đã tồn tại chưa
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo '<div class="alert alert-danger text-center">Email này đã tồn tại!</div>';
            $check->close();
        } else {
            $check->close();
            
            // Kiểm tra số điện thoại đã tồn tại chưa
            $check_phone = $conn->prepare("SELECT id FROM users WHERE phone = ?");
            $check_phone->bind_param("s", $phone);
            $check_phone->execute();
            $phone_result = $check_phone->get_result();
            
            if ($phone_result->num_rows > 0) {
                echo '<div class="alert alert-danger text-center">Số điện thoại này đã được sử dụng!</div>';
                $check_phone->close();
            } else {
                $check_phone->close();
                
                // Hash mật khẩu
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                // Thêm user mới
                $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, bio, location_id, is_verified, created_at) 
                                        VALUES (?, ?, ?, ?, ?, ?, 0, NOW())");
                $stmt->bind_param("sssssi", $name, $email, $phone, $password_hash, $bio, $location_id);

                try {
                    if ($stmt->execute()) {
                        // Chuyển hướng sang trang đăng nhập
                        echo '<script>
                                alert("Đăng ký thành công! Email: ' . htmlspecialchars($email) . '");
                                window.location.href = "dangnhap.php";
                              </script>';
                        exit();
                    } else {
                        echo '<div class="alert alert-danger text-center">Có lỗi xảy ra khi đăng ký!</div>';
                    }
                } catch (mysqli_sql_exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        if (strpos($e->getMessage(), 'phone') !== false) {
                            echo '<div class="alert alert-danger text-center">Số điện thoại này đã được sử dụng!</div>';
                        } elseif (strpos($e->getMessage(), 'email') !== false) {
                            echo '<div class="alert alert-danger text-center">Email này đã tồn tại!</div>';
                        } else {
                            echo '<div class="alert alert-danger text-center">Thông tin đã tồn tại trong hệ thống!</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger text-center">Có lỗi xảy ra: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                }
                $stmt->close();
            }
        }
    }
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
                            <option value="1">An Giang</option>
                            <option value="2">Bắc Ninh</option>
                            <option value="3">Cà Mau</option>
                            <option value="4">Cao Bằng</option>
                            <option value="5">Điện Biên</option>
                            <option value="6">Đắk Lắk</option>
                            <option value="7">Đồng Nai</option>
                            <option value="8">Đồng Tháp</option>
                            <option value="9">Gia Lai</option>
                            <option value="10">Hà Tĩnh</option>
                            <option value="11">Hưng Yên</option>
                            <option value="12">Khánh Hòa</option>
                            <option value="13">Lai Châu</option>
                            <option value="14">Lạng Sơn</option>
                            <option value="15">Lào Cai</option>
                            <option value="16">Lâm Đồng</option>
                            <option value="17">Nghệ An</option>
                            <option value="18">Ninh Bình</option>
                            <option value="19">Phú Thọ</option>
                            <option value="20">Quảng Ngãi</option>
                            <option value="21">Quảng Ninh</option>
                            <option value="22">Quảng Trị</option>
                            <option value="23">Sơn La</option>
                            <option value="24">Tây Ninh</option>
                            <option value="25">Thanh Hóa</option>
                            <option value="26">Thái Nguyên</option>
                            <option value="27">TP.Cần Thơ</option>
                            <option value="28">TP.Đà Nẵng</option>
                            <option value="29">TP.Hà Nội</option>
                            <option value="30">TP.Hải Phòng</option>
                            <option value="31">TP.Hồ Chí Minh</option>
                            <option value="32">TP.Huế</option>
                            <option value="33">Tuyên Quang</option>
                            <option value="34">Vĩnh Long</option>
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