<?php
/**
 * API Lấy danh sách đánh giá của sản phẩm
 * GET: product_id
 */
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/connect.php';

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ']);
    exit;
}

// Lấy thống kê đánh giá
$stats_sql = "SELECT 
                COUNT(*) AS total_ratings,
                AVG(rating) AS avg_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS star5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS star4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS star3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS star2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS star1
              FROM ratings WHERE product_id = ?";
$stats_stmt = $conn->prepare($stats_sql);
$stats_stmt->bind_param('i', $product_id);
$stats_stmt->execute();
$stats_res = $stats_stmt->get_result();
$stats = $stats_res->fetch_assoc();
$stats_stmt->close();

// Lấy danh sách đánh giá
$ratings_sql = "SELECT r.id, r.rating, r.comment, r.created_at, 
                       u.id AS user_id, u.name AS user_name
                FROM ratings r
                LEFT JOIN users u ON r.rater_user_id = u.id
                WHERE r.product_id = ?
                ORDER BY r.created_at DESC
                LIMIT 50";
$ratings_stmt = $conn->prepare($ratings_sql);
$ratings_stmt->bind_param('i', $product_id);
$ratings_stmt->execute();
$ratings_res = $ratings_stmt->get_result();

$ratings = [];
while ($row = $ratings_res->fetch_assoc()) {
    $ratings[] = [
        'id' => (int)$row['id'],
        'rating' => (int)$row['rating'],
        'comment' => $row['comment'],
        'created_at' => $row['created_at'],
        'user_id' => (int)$row['user_id'],
        'rater_name' => $row['user_name']
    ];
}
$ratings_stmt->close();

// Kiểm tra user hiện tại đã đánh giá chưa
$user_rating = null;
if (isset($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $my_sql = "SELECT id, rating, comment FROM ratings WHERE product_id = ? AND rater_user_id = ?";
    $my_stmt = $conn->prepare($my_sql);
    $my_stmt->bind_param('ii', $product_id, $user_id);
    $my_stmt->execute();
    $my_res = $my_stmt->get_result();
    if ($my_row = $my_res->fetch_assoc()) {
        $user_rating = [
            'id' => (int)$my_row['id'],
            'rating' => (int)$my_row['rating'],
            'comment' => $my_row['comment']
        ];
    }
    $my_stmt->close();
}

echo json_encode([
    'success' => true,
    'stats' => [
        'total' => (int)$stats['total_ratings'],
        'average' => $stats['avg_rating'] ? round((float)$stats['avg_rating'], 1) : 0,
        'breakdown' => [
            5 => (int)$stats['star5'],
            4 => (int)$stats['star4'],
            3 => (int)$stats['star3'],
            2 => (int)$stats['star2'],
            1 => (int)$stats['star1']
        ]
    ],
    'ratings' => $ratings,
    'user_rating' => $user_rating
]);

$conn->close();
