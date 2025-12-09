<?php
session_start();
include '../config/connect.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
$my_id = $_SESSION['user_id'];

// 2. Kiểm tra ID sản phẩm
if (!isset($_GET['id'])) {
    header("Location: user_dashboard.php");
    exit;
}
$id = intval($_GET['id']);

// 3. Lấy dữ liệu sản phẩm
$sql_get = "SELECT * FROM products WHERE id = $id AND seller_id = $my_id";
$result = $conn->query($sql_get);

if ($result->num_rows == 0) {
    echo "<script>alert('Tin không tồn tại hoặc không thuộc quyền của bạn!'); window.location.href='user_dashboard.php';</script>";
    exit;
}
$row = $result->fetch_assoc();

// 4. Lấy ảnh thumbnail từ bảng product_images
$sql_img = "SELECT filename FROM product_images WHERE product_id = $id ORDER BY sort_order ASC LIMIT 1";
$img_row = $conn->query($sql_img)->fetch_assoc();

$img_src = (!empty($img_row['filename']))
            ? '../' . $img_row['filename']
            : 'https://via.placeholder.com/300x300?text=No+Image';

// 5. Khi nhấn nút cập nhật
if (isset($_POST['btn_update'])) {

    $title = $conn->real_escape_string($_POST['title']);
    $price = $_POST['price'];
    $desc  = $conn->real_escape_string($_POST['description']);
    $cate_id = $_POST['category_id'];

    // CHECK nếu upload ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {

        $target_dir = "../uploads/";
        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $new_path = "uploads/" . $file_name;

            // Lấy ảnh cũ để xóa file cũ
            if (!empty($img_row['filename']) && file_exists("../" . $img_row['filename'])) {
                unlink("../" . $img_row['filename']);
            }

            // Cập nhật filename trong product_images
            $sql_up = "UPDATE product_images SET filename='$new_path' WHERE product_id=$id ORDER BY sort_order ASC LIMIT 1";
            $conn->query($sql_up);
        } else {
            // Nếu không upload ảnh mới, không làm gì
        }
    }

    // Cập nhật thông tin sản phẩm (không cập nhật cột image vì không tồn tại)
    $sql_update = "UPDATE products SET title='$title', price='$price', description='$desc', category_id='$cate_id', status='pending' WHERE id=$id AND seller_id=$my_id";

    if ($conn->query($sql_update) === TRUE) {
        echo "<script>
                alert('Cập nhật thành công! Tin sẽ chờ duyệt lại.');
                window.location.href='user_dashboard.php';
              </script>";
    } else {
        echo "Lỗi SQL: " . $conn->error;
    }
}

?>

<?php include_once dirname(__DIR__) . '/includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-3 d-none d-lg-block">
            <aside class="seller-aside">
                <div class="text-center mb-3 brand"><i class="fa-solid fa-store me-2"></i>Đăng Tin</div>
                <ul class="list-unstyled">
                    <li><a href="user_dashboard.php">Tổng Quan</a></li>
                    <li><a href="user_dangtin.php">Đăng Tin</a></li>
                    <li><a href="user_quanlytin.php">Tin Đã Đăng</a></li>
                </ul>
            </aside>
        </div>

        <div class="col-lg-9">
            <h3 class="mb-4 text-secondary">Chỉnh Sửa Tin Đăng</h3>

            <div class="card card-custom p-4">
                <h4 class="mb-3 text-warning"><i class="fa-solid fa-pen-to-square"></i> Cập Nhật Thông Tin</h4>

                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <label class="fw-bold">Tiêu đề</label>
                            <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">Giá bán (VNĐ)</label>
                                    <input type="number" name="price" class="form-control" required value="<?php echo htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8'); ?>" min="1000" step="1000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="fw-bold">Danh mục</label>
                                    <select name="category_id" class="form-select" required>
                                        <?php
                                        $cats = $conn->query("SELECT * FROM categories");
                                        while($c = $cats->fetch_assoc()) {
                                            $selected = ($c['id'] == $row['category_id']) ? 'selected' : '';
                                            echo "<option value='".$c['id']."' $selected>".htmlspecialchars($c['name'])."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <label class="fw-bold">Mô tả</label>
                            <textarea name="description" class="form-control" rows="6"><?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>

                        </div>

                        <div class="col-md-4">
                            <label class="fw-bold mb-2">Ảnh hiện tại</label>
                            <div class="border rounded p-2 bg-light text-center">
                                <img src="<?php echo $img_src; ?>" class="img-fluid rounded" style="max-height:250px; object-fit:cover;">
                            </div>

                            <label class="fw-bold mt-3">Thay ảnh mới</label>
                            <input type="file" name="image" class="form-control mt-1">
                            <small class="text-muted">Để trống nếu giữ ảnh cũ.</small>
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" name="btn_update" class="btn btn-cam px-4 py-2">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Lưu Thay Đổi
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once dirname(__DIR__) . '/includes/footer.php'; ?>
