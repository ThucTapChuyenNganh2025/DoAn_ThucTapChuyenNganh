<?php
session_start();
include '../config/connect.php';

$error_msg = '';
$success_msg = '';

// --- PHẦN MỚI THÊM: Lấy danh sách Tỉnh/Thành từ Database ---
// Truy vấn lấy id và tên tỉnh. 
// Nếu bảng locations có cả huyện, bạn có thể thêm WHERE district IS NULL
$sql_loc = "SELECT id, province FROM locations ORDER BY id ASC"; 
$result_loc = $conn->query($sql_loc);
// -----------------------------------------------------------

if (isset($_POST['create'])) {   
    $name         = trim($_POST['name']);
    $email        = trim($_POST['email']);
    $phone        = trim($_POST['phone']);
    $password     = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $bio          = trim($_POST['bio']);
    $location_id  = $_POST['location_id'];

    if ($password !== $confirm_pass) {
        $error_msg = 'Mật khẩu xác nhận không khớp!';
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error_msg = 'Email này đã tồn tại!';
            $check->close();
        } else {
            $check->close();
            
            $check_phone = $conn->prepare("SELECT id FROM users WHERE phone = ?");
            $check_phone->bind_param("s", $phone);
            $check_phone->execute();
            $phone_result = $check_phone->get_result();
            
            if ($phone_result->num_rows > 0) {
                $error_msg = 'Số điện thoại này đã được sử dụng!';
                $check_phone->close();
            } else {
                $check_phone->close();
                
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, bio, location_id, is_verified, created_at) VALUES (?, ?, ?, ?, ?, ?, 0, NOW())");
                $stmt->bind_param("sssssi", $name, $email, $phone, $password_hash, $bio, $location_id);

                try {
                    if ($stmt->execute()) {
                        echo '<script src="../js/toast.js"></script>
                              <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    toastAndRedirect("Đăng ký thành công! Bạn có thể đăng nhập ngay.", "success", "dangnhap.php", 2000);
                                });
                              </script>';
                        exit();
                    } else {
                        $error_msg = 'Có lỗi xảy ra khi đăng ký!';
                    }
                } catch (mysqli_sql_exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                        $error_msg = 'Thông tin (Email hoặc SĐT) đã tồn tại!';
                    } else {
                        $error_msg = 'Lỗi hệ thống: ' . htmlspecialchars($e->getMessage());
                    }
                }
                $stmt->close();
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
    <title>Đăng ký - Chợ Điện Tử</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1a202c;
            --accent-color: #f6c23e; 
            --bg-color: #f4f6f9;
            --text-color: #333;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 40px 0;
        }

        .login-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            padding: 40px 30px;
            width: 100%;
            max-width: 550px;
            border-top: 5px solid var(--accent-color);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            text-decoration: none;
        }

        .logo-icon {
            background-color: var(--accent-color);
            color: var(--primary-color);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 20px;
            margin-right: 10px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .login-card h4 {
            color: #555;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.2rem;
            text-transform: uppercase;
        }

        .form-control, .form-select {
            border-radius: 6px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            font-size: 0.95rem;
            border-left: none;
        }
        
        .password-field {
            border-right: none;
        }

        .form-control:focus, .form-select:focus {
            box-shadow: none;
            border-color: var(--accent-color);
        }

        .input-group-text {
            background-color: #fff;
            border-right: none;
            color: #888;
            min-width: 45px;
            justify-content: center;
        }

        .form-select { border-left: none; }
        
        .toggle-password {
            cursor: pointer;
            border-left: none;
            border-top-right-radius: 6px !important;
            border-bottom-right-radius: 6px !important;
            border: 1px solid #ced4da;
        }
        
        .toggle-password:hover {
            color: var(--primary-color);
        }

        .btn-login {
            background-color: var(--primary-color);
            color: #fff;
            font-weight: 600;
            padding: 12px;
            border-radius: 6px;
            width: 100%;
            border: none;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background-color: #0d1117;
            color: var(--accent-color);
        }

        .alert-custom {
            font-size: 0.9rem;
            padding: 10px;
            border-radius: 6px;
        }

        .footer-links {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .footer-links a:hover {
            color: #d69e08;
            text-decoration: underline;
        }
        
        .back-home {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #888;
            font-size: 0.85rem;
            text-decoration: none;
        }
        .back-home:hover {
            color: var(--primary-color);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.3rem;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <a href="../index.php" class="brand-logo">
            <div class="logo-icon">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="logo-text">CHỢ ĐIỆN TỬ</div>
        </a>

        <h4>Đăng ký tài khoản</h4>

        <?php if ($error_msg): ?>
            <div class="alert alert-danger alert-custom text-center">
                <i class="fas fa-exclamation-circle me-1"></i>
                <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">HỌ VÀ TÊN</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" class="form-control" required placeholder="Họ và tên" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">SỐ ĐIỆN THOẠI</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" name="phone" class="form-control" placeholder="Số điện thoại" pattern="^[0-9]{10}$" required value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">EMAIL</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" required placeholder="Email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                </div>
            </div>

          <div class="mb-3">
                <label class="form-label">MẬT KHẨU</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="regPass" class="form-control password-field" required minlength="6" placeholder="Nhập mật khẩu">
                    <span class="input-group-text toggle-password" onclick="togglePassword('regPass', 'iconRegPass')">
                        <i class="fas fa-eye" id="iconRegPass"></i>
                    </span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">NHẬP LẠI MẬT KHẨU</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                    <input type="password" name="confirm_password" id="confirmPass" class="form-control password-field" required placeholder="Nhập lại mật khẩu">
                    <span class="input-group-text toggle-password" onclick="togglePassword('confirmPass', 'iconConfirmPass')">
                        <i class="fas fa-eye" id="iconConfirmPass"></i>
                    </span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">KHU VỰC / TỈNH THÀNH</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <select class="form-select" name="location_id" required>
                        <option value="" disabled <?php echo !isset($location_id) ? 'selected' : ''; ?>>-- Chọn địa điểm --</option>
                        <?php
                        if ($result_loc && $result_loc->num_rows > 0) {
                            while($row = $result_loc->fetch_assoc()) {
                                // Kiểm tra nếu người dùng đã chọn trước đó (khi submit lỗi) thì giữ nguyên lựa chọn
                                $selected = (isset($location_id) && $location_id == $row['id']) ? 'selected' : '';
                                echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['province'] . '</option>';
                            }
                        } else {
                            echo '<option value="">Không có dữ liệu</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">GIỚI THIỆU NGẮN (BIO)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-pen"></i></span>
                    <textarea class="form-control" name="bio" rows="2" placeholder=""><?php echo isset($bio) ? htmlspecialchars($bio) : ''; ?></textarea>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" name="create" class="btn btn-login">
                    ĐĂNG KÝ TÀI KHOẢN
                </button>
            </div>
        </form>

        <div class="footer-links">
            <p>Đã có tài khoản? <a href="dangnhap.php">Đăng nhập ngay</a></p>
        </div>
        
        <a href="../index.php" class="back-home">
            <i class="fas fa-arrow-left me-1"></i> Quay lại trang chủ
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>