<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Debug Session</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        pre { background: #fff; padding: 20px; border-radius: 10px; overflow: auto; }
        h2 { color: #333; }
        .btn { display: inline-block; padding: 10px 20px; background: #dc3545; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <h2>🔍 Debug Session</h2>
    
    <h3>Session Contents:</h3>
    <pre><?php print_r($_SESSION); ?></pre>
    
    <h3>Session ID:</h3>
    <pre><?php echo session_id(); ?></pre>
    
    <a href="user/dangxuat.php" class="btn">Đăng xuất để reset session</a>
</body>
</html>
