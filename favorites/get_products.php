<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/connect.php';


$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat = isset($_GET['category']) ? trim($_GET['category']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
// Optional: list of product ids already rendered on client to avoid duplicates
$exclude = isset($_GET['exclude_ids']) ? trim($_GET['exclude_ids']) : '';

$sql = "SELECT DISTINCT p.id, p.title, p.description, p.price, p.currency, p.created_at, p.category_id, p.status, p.views, c.name AS category_name,
                (
                    SELECT filename FROM product_images pi2 WHERE pi2.product_id = p.id ORDER BY pi2.sort_order ASC LIMIT 1
                ) AS image_file
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'approved'";
$params = [];
if ($q !== '') {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $like = '%' . $q . '%';
    $params[] = $like; $params[] = $like;
}
if ($cat !== '') {
    // Accept category by name (case-insensitive) or by slug, fuzzy match
    $sql .= " AND (LOWER(c.name) LIKE LOWER(?) OR c.slug LIKE ?)";
    $params[] = '%'.$cat.'%';
    $params[] = '%'.$cat.'%';
}
// Exclude ids to avoid duplicates when client passes existing ids
if ($exclude !== '') {
    // sanitize to numeric ids only
    $ids = array_values(array_filter(array_map(function($x){ return preg_replace('/[^0-9]/','',$x); }, explode(',', $exclude)), function($v){ return $v !== ''; }));
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql .= " AND p.id NOT IN (".$placeholders.")";
        foreach ($ids as $id) { $params[] = (int)$id; }
    }
}
// Use a stable ordering to ensure consistent pagination
$sql .= " ORDER BY p.created_at DESC, p.id DESC LIMIT ? OFFSET ?";
// When exclude_ids are provided, using OFFSET can skip too many rows.
// To keep paging consistent, return the next latest products excluding existing ones with OFFSET 0.
if ($exclude !== '' && !empty($ids)) {
    $params[] = $limit;
    $params[] = 0; // ignore client offset when excluding ids
} else {
    $params[] = $limit;
    $params[] = $offset;
}

$stmt = $conn->prepare($sql);
if (!$stmt) { echo json_encode(['status'=>'error','message'=>'Prepare failed: '.$conn->error]); exit; }

// bind params (build by reference for compatibility)
if (count($params)) {
    $types = '';
    foreach ($params as $p) { $types .= is_int($p) ? 'i' : 's'; }
    $bind_names = array();
    $bind_names[] = $types;
    for ($i = 0; $i < count($params); $i++) {
        $varName = 'param' . $i;
        $$varName = $params[$i];
        $bind_names[] = &$$varName;
    }
    call_user_func_array(array($stmt, 'bind_param'), $bind_names);
}

$stmt->execute();
$res = $stmt->get_result();
$out = array();
$uploads_dir = realpath(__DIR__ . '/../uploads');
while ($row = $res->fetch_assoc()) {
    $imagePath = 'images/default-product.jpg';
    if (!empty($row['image_file']) && $uploads_dir) {
        $image_file = trim($row['image_file']);
        $candidate = $uploads_dir . DIRECTORY_SEPARATOR . $image_file;
        if (file_exists($candidate) && is_file($candidate)) {
            $imagePath = 'uploads/' . $image_file;
        } else {
            // case-insensitive search fallback
            $basename = strtolower(basename($image_file));
            $found = null;
            $files = @scandir($uploads_dir);
            if (is_array($files)) {
                foreach ($files as $f) {
                    if (strtolower($f) === $basename) { $found = $f; break; }
                }
            }
            if ($found) {
                $imagePath = 'uploads/' . $found;
            } else {
                // log missing image for manual review
                try {
                    $logLine = date('c') . "\t" . ($row['id'] ?? '') . "\t" . ($image_file ?? '') . "\n";
                    @file_put_contents(__DIR__ . '/missing_images.log', $logLine, FILE_APPEND | LOCK_EX);
                } catch (Exception $e) {}
            }
        }
    }
    $row['image'] = $imagePath;
    $out[] = $row;
}

echo json_encode(array('status'=>'success','products'=>$out));
$stmt->close();
exit;

?>
