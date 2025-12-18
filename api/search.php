<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once __DIR__ . '/../config/connect.php';

// Get parameters
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest'; // newest, oldest, price_low, price_high
$min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : 999999999;

// Validate pagination
$page = max(1, $page);
$limit = min(100, max(1, $limit));
$offset = ($page - 1) * $limit;

// Build query
$where = "WHERE p.status = 'approved'";
$params = [];
$types = '';

// Search by query
if (!empty($query)) {
    $where .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $like_query = '%' . $query . '%';
    $params[] = $like_query;
    $params[] = $like_query;
    $types .= 'ss';
}

// Filter by category
if ($category > 0) {
    $where .= " AND p.category_id = ?";
    $params[] = $category;
    $types .= 'i';
}

// Filter by price range
if ($min_price > 0) {
    $where .= " AND p.price >= ?";
    $params[] = $min_price;
    $types .= 'i';
}
if ($max_price < 999999999) {
    $where .= " AND p.price <= ?";
    $params[] = $max_price;
    $types .= 'i';
}

// Determine sort order
$order_by = "p.created_at DESC";
switch ($sort) {
    case 'oldest':
        $order_by = "p.created_at ASC";
        break;
    case 'price_low':
        $order_by = "p.price ASC";
        break;
    case 'price_high':
        $order_by = "p.price DESC";
        break;
    default:
        $order_by = "p.created_at DESC";
}

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM products p $where";
$count_stmt = $conn->prepare($count_sql);
if (!empty($types)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$count_row = $count_result->fetch_assoc();
$total = $count_row['total'];
$total_pages = ceil($total / $limit);

// Get products with images
$sql = "SELECT p.*, 
            (SELECT filename FROM product_images WHERE product_id = p.id ORDER BY sort_order ASC LIMIT 1) AS image,
            (SELECT COUNT(*) FROM favorites WHERE product_id = p.id) AS favorite_count,
            u.name AS seller_name
        FROM products p
        LEFT JOIN users u ON p.seller_id = u.id
        $where
        ORDER BY $order_by
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types . 'ii', ...$params, $limit, $offset);
} else {
    $stmt->bind_param('ii', $limit, $offset);
}

if (!$stmt->execute()) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) {
    // Format image URL
    if (!empty($row['image'])) {
        $row['image'] = '/DoAn_ThucTapChuyenNganh/' . $row['image'];
    } else {
        $row['image'] = null;
    }
    
    // Format price
    $row['price_formatted'] = number_format($row['price']) . ' đ';
    
    // Format date
    $row['created_at_formatted'] = date('d/m/Y', strtotime($row['created_at']));
    
    $products[] = $row;
}

// Return JSON response
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'data' => [
        'products' => $products,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'total_pages' => $total_pages,
            'has_next' => $page < $total_pages,
            'has_prev' => $page > 1
        ],
        'query' => [
            'search' => $query,
            'category' => $category,
            'sort' => $sort,
            'min_price' => $min_price,
            'max_price' => $max_price
        ]
    ]
], JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
?>
