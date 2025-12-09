<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/connect.php';

// Use the configured database from connect.php (do not force a different DB)

$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action'])?$_GET['action']: '');
if ($action === 'register') {
    $u = isset($_POST['username']) ? trim($_POST['username']) : '';
    $p = isset($_POST['password']) ? trim($_POST['password']) : '';
    if (!$u || !$p) { echo json_encode(['status'=>'error','message'=>'Thiếu thông tin']); exit; }
    // create users table if not exists
    @mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(191) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $hash = password_hash($p, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $stmt->bind_param('ss', $u, $hash);
    $ok = $stmt->execute();
    if ($ok) { echo json_encode(['status'=>'success','message'=>'Đăng ký thành công']); }
    else { echo json_encode(['status'=>'error','message'=>'Không thể đăng ký']); }
    $stmt->close();
    exit;
}
if ($action === 'login') {
    $u = isset($_POST['username']) ? trim($_POST['username']) : '';
    $p = isset($_POST['password']) ? trim($_POST['password']) : '';
    if (!$u || !$p) { echo json_encode(['status'=>'error','message'=>'Thiếu thông tin']); exit; }
    $stmt = $conn->prepare('SELECT id, password FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $u);
    $stmt->execute();
    $stmt->bind_result($id, $hash);
    if ($stmt->fetch()) {
        if (!$hash) { echo json_encode(['status'=>'error','message'=>'Không có mật khẩu lưu trên server']); $stmt->close(); exit; }
        if (password_verify($p, $hash)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $u;
            echo json_encode(['status'=>'success','message'=>'Đăng nhập thành công','user'=>['id'=>$id,'username'=>$u]]);
        } else {
            echo json_encode(['status'=>'error','message'=>'Sai mật khẩu']);
        }
    } else {
        echo json_encode(['status'=>'error','message'=>'Không tìm thấy tài khoản']);
    }
    $stmt->close();
    exit;
}
if ($action === 'logout') {
    session_unset(); session_destroy();
    echo json_encode(['status'=>'success','message'=>'Đã đăng xuất']);
    exit;
}

// return current user
if ($action === 'me') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode(['status'=>'success','user'=>['id'=>$_SESSION['user_id'],'username'=>$_SESSION['username']]]);
    } else {
        echo json_encode(['status'=>'error','message'=>'Không đăng nhập']);
    }
    exit;
}

echo json_encode(['status'=>'error','message'=>'Hành động không hợp lệ']);

?>
