<?php
include '../config/connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Không cho xóa ID số 1 (Thường là Super Admin)
    if($id == 1) {
        echo '<!DOCTYPE html><html><head>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <script src="../js/toast.js"></script>
        </head><body>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                toastAndRedirect("Không thể xóa tài khoản Super Admin!", "error", "admin_users.php", 2000);
            });
        </script></body></html>';
        exit;
    }

    $sql = "DELETE FROM users WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../admin/admin_users.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>