<?php
session_start();
include '../config/connect.php';

$error = '';
$requested_next = isset($_GET['next']) ? trim($_GET['next']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_user'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            $script = $_SERVER['SCRIPT_NAME'];
            $projectRoot = dirname(dirname($script)); 
            if ($projectRoot === '/' || $projectRoot === '\\') $projectRoot = '';
            $next = $projectRoot . '/index.php';

            if ($requested_next !== '') {
                $candidate = $requested_next;
                if (strpos($candidate, '://') === false && strpos($candidate, '//') === false && strpos($candidate, 'http') === false) {
                    if (strpos($candidate, '/') === 0) {
                        if (strpos($candidate, $projectRoot) === 0) $next = $candidate;
                        else $next = $projectRoot . $candidate;
                    } else {
                        $base = dirname($script);
                        $next = $base . '/' . $candidate;
                    }
                }
            }
            header('Location: ' . $next);
            exit;
        } else {
            $error = 'Mật khẩu không chính xác!';
        }
    } else {
        $error = 'Email này chưa được đăng ký!';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Chợ Điện Tử</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1a202c;
            --accent-color: #f6c23e; 
            --bg-color: #f4f6f9;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            padding: 40px 30px;
            width: 100%;
            max-width: 420px;
            border-top: 5px solid var(--accent-color);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
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
        }

        .form-control {
            border-radius: 6px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            border-left: none;
        }
        
        .password-field {
            border-right: none; 
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--accent-color);
        }

        .input-group-text {
            background-color: #fff;
            border-right: none;
            color: #888;
        }
        
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

        <h4>Đăng nhập tài khoản</h4>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-custom text-center">
                <i class="fas fa-exclamation-circle me-1"></i>
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="dangnhap.php<?php echo $requested_next ? '?next=' . urlencode($requested_next) : ''; ?>">
            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">EMAIL</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold small text-muted">MẬT KHẨU</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" id="loginPass" class="form-control password-field" placeholder="Mật khẩu" required>
                    <span class="input-group-text toggle-password" onclick="togglePassword('loginPass', 'iconEyeLogin')">
                        <i class="fas fa-eye" id="iconEyeLogin"></i>
                    </span>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" name="login_user" class="btn btn-login">
                    ĐĂNG NHẬP
                </button>
            </div>
        </form>

        <div class="footer-links">
            <p>Chưa có tài khoản? <a href="dangky.php">Đăng ký ngay</a></p>
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