<?php
// Server-side renderer for product cards used on the homepage.
// Outputs the whole "Bài viết mới" section and reads from `products` table.
require_once __DIR__ . '/../config/connect.php';

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$offset = 0;

$uploads_dir = realpath(__DIR__ . '/../uploads');

$sql = "SELECT p.id, p.title, p.description, p.price, p.currency, p.created_at, p.status, p.views,
               (
                 SELECT filename FROM product_images pi2 WHERE pi2.product_id = p.id ORDER BY pi2.sort_order ASC LIMIT 1
               ) AS image_file
        FROM products p
        WHERE p.status = 'approved'
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    echo "<p class='text-danger'>Không thể truy vấn sản phẩm: " . htmlspecialchars($conn->error) . "</p>";
    return;
}

// Render section
echo '<section class="py-5">';
echo '<div class="container">';
echo '<h2 class="section-title text-center mb-4">Bài viết mới</h2>';
echo '<div class="row products-compact-row" id="products-container" data-limit="' . intval($limit) . '">';

while ($row = $res->fetch_assoc()) {
    $title = htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8');
    $desc = strip_tags($row['description'] ?? '');
    $excerpt = mb_substr(trim($desc), 0, 250, 'UTF-8');
    if (mb_strlen($desc, 'UTF-8') > 250) $excerpt .= '...';

    $imgPath = 'images/default-product.jpg';
    if (!empty($row['image_file']) && $uploads_dir) {
        $image_file = trim($row['image_file']);
        $candidate = $uploads_dir . DIRECTORY_SEPARATOR . $image_file;
        if (file_exists($candidate) && is_file($candidate)) {
            $imgPath = 'uploads/' . $image_file;
        } else {
            $basename = strtolower(basename($image_file));
            $files = @scandir($uploads_dir);
            if (is_array($files)) {
                foreach ($files as $f) { if (strtolower($f) === $basename) { $imgPath = 'uploads/' . $f; break; } }
            }
        }
    }

    $link = 'product.php?id=' . (int)$row['id'];
    $priceHtml = '';
    if (isset($row['price']) && $row['price'] !== '') {
        if (is_numeric($row['price'])) $price = number_format($row['price'], 0, ',', '.');
        else $price = htmlspecialchars($row['price']);
        $currency = isset($row['currency']) ? htmlspecialchars($row['currency']) : '';
        $priceHtml = '<div class="price">' . $price . ' ' . $currency . '</div>';
    }

    $statusHtml = '';
    if (!empty($row['status'])) $statusHtml = '<span class="badge bg-info text-dark ms-1">' . htmlspecialchars($row['status']) . '</span>';
    $viewsHtml = '<small class="text-muted ms-2">Lượt xem: ' . ((int)$row['views']) . '</small>';

    echo '<div class="col-md-3 col-sm-6 mb-4">';
    echo '<div class="card h-100 shadow-sm product-item compact">';
    echo '<a href="' . $link . '"><img src="' . $imgPath . '" class="tab-image" alt="' . $title . '"></a>';
    echo '<div class="card-body">';
    echo '<h5 class="card-title"><a href="' . $link . '" class="stretched-link text-dark">' . $title . '</a></h5>';
    echo '<p class="card-text small text-muted">' . htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<a href="' . $link . '#description" class="small">Xem thêm</a>';
    echo $priceHtml;
    echo '</div>'; // card-body
    echo '<div class="card-footer bg-white border-0 d-flex align-items-center justify-content-between">';
    echo '<div><small class="text-muted">' . date('d/m/Y', strtotime($row['created_at'])) . '</small>' . $statusHtml . '</div>';
    echo '<div>' . $viewsHtml . '</div>';
    echo '</div>'; // card-footer
    echo '</div>'; // card
    echo '</div>'; // col
}

echo '</div>'; // row
echo '<div class="text-center mt-3"><button id="load-more-products" class="btn btn-outline-primary">Tải thêm</button></div>';
echo '</div></section>';

$res->free();
$stmt->close();

?>
