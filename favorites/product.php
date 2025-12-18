<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  die('Sản phẩm không tồn tại');
}

$sql = "
SELECT 
  p.id, p.title, p.description, p.price, p.currency, p.created_at,
  u.username, u.phone, u.email,
  c.name AS category_name,
  (
    SELECT pi.filename 
    FROM product_images pi 
    WHERE pi.product_id = p.id 
    ORDER BY pi.sort_order ASC 
    LIMIT 1
  ) AS image
FROM products p
LEFT JOIN users u ON p.user_id = u.id
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.id = ? AND p.status = 'approved'
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  die('Sản phẩm không tồn tại hoặc chưa được duyệt');
}

$p = $res->fetch_assoc();
$stmt->close();

$image = $p['image'] ? '../uploads/' . $p['image'] : '../images/default-product.jpg';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($p['title']); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../styles.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">
  <a href="../index.php" class="btn btn-sm btn-secondary mb-3">← Quay lại</a>

  <div class="row">
    <div class="col-md-6">
      <img src="<?php echo $image; ?>" class="img-fluid rounded border">
    </div>

    <div class="col-md-6">
      <h2><?php echo htmlspecialchars($p['title']); ?></h2>
      <p class="text-muted">Danh mục: <?php echo htmlspecialchars($p['category_name']); ?></p>

      <h4 class="text-success">
        <?php echo number_format($p['price'], 0, ',', '.'); ?>
        <?php echo htmlspecialchars($p['currency']); ?>
      </h4>

      <p class="mt-3"><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>

      <hr>

      <h5>👤 Người bán</h5>
      <p>
        <strong><?php echo htmlspecialchars($p['username']); ?></strong><br>
        📞 <?php echo htmlspecialchars($p['phone']); ?><br>
        ✉ <?php echo htmlspecialchars($p['email']); ?>
      </p>

      <button class="btn btn-primary" id="openChat">💬 Liên hệ người bán</button>
    </div>
  </div>
</div>

<!-- ================= CHAT GIẢ LẬP ================= -->
<div class="chat-box" id="chatBox">
  <div class="chat-header">
    Chat với <?php echo htmlspecialchars($p['username']); ?>
    <button class="btn btn-sm btn-light float-end" id="closeChat">×</button>
  </div>
  <div class="chat-body" id="chatBody">
    <div class="chat-message system">
      Đây là hộp chat giả lập (không lưu dữ liệu).
    </div>
  </div>
  <div class="chat-footer">
    <textarea id="chatInput" placeholder="Nhập tin nhắn..."></textarea>
    <button class="btn btn-sm btn-success mt-2 w-100" id="sendMsg">Gửi</button>
  </div>
</div>

<script>
document.getElementById('openChat').onclick = function () {
  document.getElementById('chatBox').style.display = 'block';
};

document.getElementById('closeChat').onclick = function () {
  document.getElementById('chatBox').style.display = 'none';
};

document.getElementById('sendMsg').onclick = function () {
  var input = document.getElementById('chatInput');
  var msg = input.value.trim();
  if (!msg) return;

  var body = document.getElementById('chatBody');
  body.innerHTML += '<div class="chat-message user">' + msg + '</div>';
  input.value = '';
  body.scrollTop = body.scrollHeight;
};
</script>

</body>
</html>
