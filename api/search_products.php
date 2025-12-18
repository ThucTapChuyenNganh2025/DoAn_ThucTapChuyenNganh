<?php
require_once __DIR__ . '/../config/connect.php';
header('Content-Type: application/json; charset=UTF-8');

/*
Schema liên quan:
- products(id, title, description, price, currency, category_id, status, created_at, ...)
- categories(id, name)
- product_images(id, product_id, filename, sort_order)
*/

$keyword     = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Base SQL
$sql = "
SELECT 
    p.id,
    p.title,
    p.description,
    p.price,
    p.currency,
    p.created_at,
    c.name AS category_name,
    (
        SELECT pi.filename
        FROM product_images pi
        WHERE pi.product_id = p.id
        ORDER BY pi.sort_order ASC, pi.id ASC
        LIMIT 1
    ) AS image
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.status = 'approved'
";

$params = [];
$types  = "";

// Search by keyword (title)
if ($keyword !== '') {
    $sql .= " AND p.title LIKE ?";
    $params[] = '%' . $keyword . '%';
    $types   .= 's';
}

// Filter by category
if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types   .= 'i';
}

// Order newest first
$sql .= " ORDER BY p.created_at DESC, p.id DESC";

// Limit kết quả (an toàn)
$sql .= " LIMIT 20";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    // Chuẩn hóa ảnh (fallback)
    if (empty($row['image'])) {
        $row['image'] = 'images/default-product.jpg';
    } else {
        // Đảm bảo đường dẫn đúng uploads/
        if (!str_starts_with($row['image'], 'uploads/')) {
            $row['image'] = 'uploads/' . $row['image'];
        }
    }

    $data[] = [
        'id'            => (int)$row['id'],
        'title'         => $row['title'],
        'description'   => $row['description'],
        'price'         => $row['price'],
        'currency'      => $row['currency'],
        'category_name' => $row['category_name'],
        'image'         => $row['image'],
        'created_at'    => $row['created_at']
    ];
}

echo json_encode([
    'status'   => 'success',
    'count'    => count($data),
    'products' => $data
]);

$stmt->close();
$conn->close();
