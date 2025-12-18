<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  echo '<div class="container py-5 text-center"><h3 class="text-danger">Sản phẩm không tồn tại</h3><a href="index.php" class="btn btn-primary mt-3">Quay lại trang chủ</a></div>';
  require_once __DIR__ . '/includes/footer.php';
  exit;
}

// Lấy thông tin sản phẩm
$sql = "
SELECT 
  p.id, p.title, p.description, p.price, p.currency, p.created_at, p.seller_id,
  u.name AS seller_name, u.phone AS seller_phone, u.email AS seller_email,
  c.name AS category_name,
  l.province AS location_province, l.district AS location_district
FROM products p
LEFT JOIN users u ON p.seller_id = u.id
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN locations l ON p.location_id = l.id
WHERE p.id = ? AND p.status = 'approved'
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo '<div class="container py-5 text-center"><h3 class="text-danger">Sản phẩm không tồn tại hoặc chưa được duyệt</h3><a href="index.php" class="btn btn-primary mt-3">Quay lại trang chủ</a></div>';
  require_once __DIR__ . '/includes/footer.php';
  exit;
}

$p = $res->fetch_assoc();
$stmt->close();

// Lấy tất cả ảnh của sản phẩm
$images = [];
$img_sql = "SELECT filename FROM product_images WHERE product_id = ? ORDER BY sort_order ASC";
$img_stmt = $conn->prepare($img_sql);
$img_stmt->bind_param('i', $id);
$img_stmt->execute();
$img_res = $img_stmt->get_result();
while ($img_row = $img_res->fetch_assoc()) {
  $images[] = $img_row['filename'];
}
$img_stmt->close();

// Nếu không có ảnh, dùng ảnh mặc định
if (empty($images)) {
  $images[] = 'default-product.jpg';
}

// Xử lý thông tin hiển thị
$title = htmlspecialchars($p['title'] ?? '', ENT_QUOTES, 'UTF-8');
$description = nl2br(htmlspecialchars($p['description'] ?? '', ENT_QUOTES, 'UTF-8'));
$price = ($p['price'] > 0) 
  ? number_format($p['price'], 0, ',', '.') . ' ' . htmlspecialchars($p['currency'] ?? 'đ', ENT_QUOTES, 'UTF-8')
  : 'Thỏa thuận';
$category = htmlspecialchars($p['category_name'] ?? 'Chưa phân loại', ENT_QUOTES, 'UTF-8');
$sellerName = htmlspecialchars($p['seller_name'] ?? 'Người bán', ENT_QUOTES, 'UTF-8');
$sellerPhone = htmlspecialchars($p['seller_phone'] ?? '', ENT_QUOTES, 'UTF-8');
$sellerEmail = htmlspecialchars($p['seller_email'] ?? '', ENT_QUOTES, 'UTF-8');

$location = '';
if (!empty($p['location_district'])) $location = htmlspecialchars($p['location_district'], ENT_QUOTES, 'UTF-8');
if (!empty($p['location_province'])) {
  $location = $location ? $location . ', ' . htmlspecialchars($p['location_province'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($p['location_province'], ENT_QUOTES, 'UTF-8');
}
if (!$location) $location = 'Chưa cập nhật';

$createdAt = !empty($p['created_at']) ? date('d/m/Y H:i', strtotime($p['created_at'])) : '';

// Kiểm tra đã yêu thích chưa
$isFavorited = false;
if (isset($_SESSION['user_id'])) {
  $fav_sql = "SELECT id FROM favorites WHERE user_id = ? AND product_id = ?";
  $fav_stmt = $conn->prepare($fav_sql);
  $fav_stmt->bind_param('ii', $_SESSION['user_id'], $id);
  $fav_stmt->execute();
  $fav_res = $fav_stmt->get_result();
  $isFavorited = ($fav_res->num_rows > 0);
  $fav_stmt->close();
}
?>

<style>
/* Product Detail Styles */
.product-detail-section {
  background: #f5f5f5;
  padding: 40px 0 60px;
}
.product-gallery {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}
.main-image {
  width: 100%;
  height: 400px;
  object-fit: contain;
  border-radius: 8px;
  background: #f8f8f8;
}
.thumbnail-list {
  display: flex;
  gap: 10px;
  margin-top: 15px;
  flex-wrap: wrap;
}
.thumbnail-item {
  width: 70px;
  height: 70px;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.2s ease;
}
.thumbnail-item:hover,
.thumbnail-item.active {
  border-color: #f6c23e;
}
.thumbnail-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-info-card {
  background: #fff;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}
.product-title {
  font-size: 24px;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 15px;
}
.product-price {
  font-size: 28px;
  font-weight: 700;
  color: #e53935;
  margin-bottom: 20px;
}
.product-meta-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 0;
  border-bottom: 1px solid #f0f0f0;
  font-size: 14px;
  color: #666;
}
.product-meta-item i {
  width: 20px;
  text-align: center;
  color: #888;
}
.product-meta-item strong {
  color: #333;
}

.seller-card {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  margin-top: 20px;
}
.seller-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
}
.seller-avatar {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #f6c23e, #dda20a);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 20px;
  font-weight: 700;
}
.seller-name {
  font-size: 16px;
  font-weight: 600;
  color: #333;
}
.seller-contact {
  font-size: 13px;
  color: #888;
}

.btn-contact {
  width: 100%;
  padding: 12px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.btn-chat {
  background: linear-gradient(135deg, #0d6efd, #0a58ca);
  color: #fff;
  border: none;
}
.btn-chat:hover {
  background: linear-gradient(135deg, #0a58ca, #084298);
  color: #fff;
}
.btn-phone {
  background: #28a745;
  color: #fff;
  border: none;
  margin-top: 10px;
}
.btn-phone:hover {
  background: #218838;
  color: #fff;
}
.btn-favorite {
  background: #fff;
  border: 2px solid #ff4757;
  color: #ff4757;
  margin-top: 10px;
}
.btn-favorite:hover,
.btn-favorite.favorited {
  background: #ff4757;
  color: #fff;
}

.product-description {
  background: #fff;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  margin-top: 30px;
}
.product-description h5 {
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 2px solid #f6c23e;
  display: inline-block;
}
.product-description-content {
  line-height: 1.8;
  color: #555;
}

/* Chat Box Styles */
.chat-box {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 350px;
  max-width: 90vw;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 5px 30px rgba(0,0,0,0.2);
  display: none;
  z-index: 9999;
  overflow: hidden;
}
.chat-header {
  background: linear-gradient(135deg, #0d6efd, #0a58ca);
  color: #fff;
  padding: 15px;
  font-weight: 600;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.chat-header .btn-close-chat {
  background: rgba(255,255,255,0.2);
  border: none;
  color: #fff;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 18px;
}
.chat-header .btn-close-chat:hover {
  background: rgba(255,255,255,0.3);
}
.chat-body {
  height: 300px;
  overflow-y: auto;
  padding: 15px;
  background: #f8f9fa;
}
.chat-message {
  margin-bottom: 12px;
  padding: 10px 14px;
  border-radius: 12px;
  max-width: 85%;
  word-wrap: break-word;
}
.chat-message.system {
  background: #e9ecef;
  color: #666;
  font-size: 13px;
  text-align: center;
  max-width: 100%;
  font-style: italic;
}
.chat-message.user {
  background: linear-gradient(135deg, #0d6efd, #0a58ca);
  color: #fff;
  margin-left: auto;
}
.chat-message.seller {
  background: #fff;
  border: 1px solid #e0e0e0;
}
.chat-footer {
  padding: 15px;
  border-top: 1px solid #eee;
  display: flex;
  gap: 10px;
}
.chat-footer textarea {
  flex: 1;
  border: 1px solid #ddd;
  border-radius: 20px;
  padding: 10px 15px;
  resize: none;
  height: 42px;
  font-size: 14px;
}
.chat-footer textarea:focus {
  outline: none;
  border-color: #0d6efd;
}
.chat-footer .btn-send {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: linear-gradient(135deg, #0d6efd, #0a58ca);
  color: #fff;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.chat-footer .btn-send:hover {
  background: linear-gradient(135deg, #0a58ca, #084298);
}

@media (max-width: 768px) {
  .main-image {
    height: 280px;
  }
  .product-title {
    font-size: 20px;
  }
  .product-price {
    font-size: 22px;
  }
  .chat-box {
    width: 100%;
    max-width: none;
    right: 0;
    bottom: 0;
    border-radius: 12px 12px 0 0;
  }
}
</style>

<section class="product-detail-section">
  <div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo $BASE_PATH; ?>/index.php">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="<?php echo $BASE_PATH; ?>/index.php?category=<?php echo urlencode($p['category_name'] ?? ''); ?>"><?php echo $category; ?></a></li>
        <li class="breadcrumb-item active"><?php echo mb_substr($title, 0, 30, 'UTF-8'); ?>...</li>
      </ol>
    </nav>
    
    <div class="row">
      <!-- Product Gallery -->
      <div class="col-lg-6 mb-4">
        <div class="product-gallery">
          <?php 
          $mainImg = $images[0];
          $mainImgPath = (strpos($mainImg, 'uploads/') === 0) ? $mainImg : 'uploads/' . $mainImg;
          if ($mainImg === 'default-product.jpg') $mainImgPath = 'images/default-product.jpg';
          ?>
          <img src="<?php echo $BASE_PATH . '/' . $mainImgPath; ?>" alt="<?php echo $title; ?>" class="main-image" id="mainImage">
          
          <?php if (count($images) > 1): ?>
          <div class="thumbnail-list">
            <?php foreach ($images as $index => $img): 
              $imgPath = (strpos($img, 'uploads/') === 0) ? $img : 'uploads/' . $img;
            ?>
            <div class="thumbnail-item <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeImage(this, '<?php echo $BASE_PATH . '/' . $imgPath; ?>')">
              <img src="<?php echo $BASE_PATH . '/' . $imgPath; ?>" alt="">
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
        
        <!-- Description -->
        <div class="product-description" id="description">
          <h5><i class="fa-solid fa-align-left me-2"></i>Mô tả chi tiết</h5>
          <div class="product-description-content">
            <?php echo $description ?: '<em class="text-muted">Chưa có mô tả</em>'; ?>
          </div>
        </div>
      </div>
      
      <!-- Product Info -->
      <div class="col-lg-6">
        <div class="product-info-card">
          <h1 class="product-title"><?php echo $title; ?></h1>
          <div class="product-price"><?php echo $price; ?></div>
          
          <div class="product-meta-item">
            <i class="fa-solid fa-tag"></i>
            <span>Danh mục:</span>
            <strong><?php echo $category; ?></strong>
          </div>
          
          <div class="product-meta-item">
            <i class="fa-solid fa-location-dot"></i>
            <span>Khu vực:</span>
            <strong><?php echo $location; ?></strong>
          </div>
          
          <div class="product-meta-item">
            <i class="fa-regular fa-clock"></i>
            <span>Đăng ngày:</span>
            <strong><?php echo $createdAt; ?></strong>
          </div>
        </div>
        
        <!-- Seller Card -->
        <div class="seller-card">
          <div class="seller-header">
            <div class="seller-avatar">
              <?php echo mb_strtoupper(mb_substr($sellerName, 0, 1, 'UTF-8'), 'UTF-8'); ?>
            </div>
            <div>
              <div class="seller-name"><?php echo $sellerName; ?></div>
              <div class="seller-contact">Người bán</div>
            </div>
          </div>
          
          <button class="btn btn-contact btn-chat" id="openChat">
            <i class="fa-regular fa-comment-dots"></i> Chat với người bán
          </button>
          
          <?php if ($sellerPhone): ?>
          <a href="tel:<?php echo $sellerPhone; ?>" class="btn btn-contact btn-phone">
            <i class="fa-solid fa-phone"></i> <?php echo $sellerPhone; ?>
          </a>
          <?php endif; ?>
          
          <button class="btn btn-contact btn-favorite <?php echo $isFavorited ? 'favorited' : ''; ?>" id="btnFavorite" data-product-id="<?php echo $id; ?>">
            <i class="fa-<?php echo $isFavorited ? 'solid' : 'regular'; ?> fa-heart"></i>
            <span><?php echo $isFavorited ? 'Đã lưu tin' : 'Lưu tin'; ?></span>
          </button>
        </div>
        
        <!-- Warning -->
        <div class="alert alert-warning mt-3" style="font-size: 13px;">
          <i class="fa-solid fa-triangle-exclamation me-2"></i>
          <strong>Lưu ý:</strong> Để tránh rủi ro, hãy gặp mặt trực tiếp và kiểm tra hàng trước khi thanh toán.
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= CHAT BOX THỰC ================= -->
<div class="chat-box" id="chatBox">
  <div class="chat-header">
    <span><i class="fa-regular fa-comment-dots me-2"></i>Chat với <?php echo $sellerName; ?></span>
    <div style="display:flex;gap:8px;align-items:center;">
      <a href="<?php echo $BASE_PATH; ?>/user/messages.php?product_id=<?php echo $id; ?>&user_id=<?php echo (int)$p['seller_id']; ?>" 
         class="btn-close-chat" title="Mở rộng" style="font-size:14px;">
        <i class="fa-solid fa-expand"></i>
      </a>
      <button class="btn-close-chat" id="closeChat">×</button>
    </div>
  </div>
  <div class="chat-body" id="chatBody">
    <div class="chat-message system">
      <i class="fa-solid fa-spinner fa-spin me-1"></i> Đang tải tin nhắn...
    </div>
  </div>
  <div class="chat-footer">
    <textarea id="chatInput" placeholder="Nhập tin nhắn..." rows="1" <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>></textarea>
    <button class="btn-send" id="sendMsg" <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>>
      <i class="fa-solid fa-paper-plane"></i>
    </button>
  </div>
  <?php if (!isset($_SESSION['user_id'])): ?>
  <div style="padding:10px 15px;background:#fff3cd;text-align:center;font-size:13px;">
    <a href="<?php echo $BASE_PATH; ?>/user/dangnhap.php">Đăng nhập</a> để gửi tin nhắn
  </div>
  <?php endif; ?>
</div>

<script>
// Config
const productId = <?php echo $id; ?>;
const sellerId = <?php echo (int)$p['seller_id']; ?>;
const currentUserId = <?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; ?>;
const basePath = <?php echo json_encode($BASE_PATH); ?>;
const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;

let lastMessageId = 0;
let chatPolling = null;
let chatLoaded = false;

// Image gallery
function changeImage(thumb, src) {
  document.getElementById('mainImage').src = src;
  document.querySelectorAll('.thumbnail-item').forEach(function(t) {
    t.classList.remove('active');
  });
  thumb.classList.add('active');
}

// Chat functionality
document.getElementById('openChat').onclick = function() {
  // Kiểm tra không chat với chính mình
  if (currentUserId === sellerId) {
    alert('Đây là sản phẩm của bạn!');
    return;
  }
  
  document.getElementById('chatBox').style.display = 'block';
  
  if (!chatLoaded && isLoggedIn) {
    loadChatMessages();
    chatLoaded = true;
    // Start polling
    chatPolling = setInterval(pollNewMessages, 3000);
  } else if (!isLoggedIn) {
    document.getElementById('chatBody').innerHTML = `
      <div class="chat-message system">
        <i class="fa-solid fa-lock me-1"></i> Vui lòng <a href="${basePath}/user/dangnhap.php">đăng nhập</a> để chat với người bán
      </div>
    `;
  }
};

document.getElementById('closeChat').onclick = function() {
  document.getElementById('chatBox').style.display = 'none';
  if (chatPolling) {
    clearInterval(chatPolling);
    chatPolling = null;
  }
};

// Load tin nhắn
function loadChatMessages() {
  const url = `${basePath}/api/get_messages.php?product_id=${productId}&other_user_id=${sellerId}`;
  
  fetch(url, { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      if (data.status !== 'success') {
        document.getElementById('chatBody').innerHTML = `
          <div class="chat-message system">Không thể tải tin nhắn</div>
        `;
        return;
      }
      
      renderChatMessages(data.messages);
    })
    .catch(err => {
      console.error('Load chat error:', err);
      document.getElementById('chatBody').innerHTML = `
        <div class="chat-message system">Lỗi kết nối</div>
      `;
    });
}

function renderChatMessages(messages) {
  const body = document.getElementById('chatBody');
  
  if (messages.length === 0) {
    body.innerHTML = `
      <div class="chat-message system">
        <i class="fa-solid fa-comments me-1"></i> Bắt đầu cuộc trò chuyện về sản phẩm này!
      </div>
    `;
    return;
  }
  
  let html = '';
  messages.forEach(msg => {
    if (msg.id > lastMessageId) lastMessageId = msg.id;
    const msgClass = msg.is_mine ? 'user' : 'seller';
    html += `<div class="chat-message ${msgClass}">${escapeHtml(msg.message)}</div>`;
  });
  
  body.innerHTML = html;
  body.scrollTop = body.scrollHeight;
}

function pollNewMessages() {
  if (lastMessageId === 0 || !isLoggedIn) return;
  
  const url = `${basePath}/api/get_messages.php?product_id=${productId}&other_user_id=${sellerId}&last_id=${lastMessageId}`;
  
  fetch(url, { credentials: 'same-origin' })
    .then(r => r.json())
    .then(data => {
      if (data.status !== 'success' || data.messages.length === 0) return;
      
      const body = document.getElementById('chatBody');
      
      data.messages.forEach(msg => {
        if (msg.id > lastMessageId) {
          lastMessageId = msg.id;
          const msgDiv = document.createElement('div');
          msgDiv.className = `chat-message ${msg.is_mine ? 'user' : 'seller'}`;
          msgDiv.textContent = msg.message;
          body.appendChild(msgDiv);
        }
      });
      
      body.scrollTop = body.scrollHeight;
    })
    .catch(err => console.error('Poll error:', err));
}

document.getElementById('sendMsg').onclick = sendMessage;
document.getElementById('chatInput').onkeypress = function(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
};

function sendMessage() {
  if (!isLoggedIn) {
    alert('Bạn cần đăng nhập để gửi tin nhắn!');
    window.location.href = basePath + '/user/dangnhap.php';
    return;
  }
  
  var input = document.getElementById('chatInput');
  var msg = input.value.trim();
  if (!msg) return;
  
  var btn = document.getElementById('sendMsg');
  btn.disabled = true;
  
  var formData = new FormData();
  formData.append('product_id', productId);
  formData.append('receiver_id', sellerId);
  formData.append('message', msg);
  
  fetch(basePath + '/api/send_message.php', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin'
  })
  .then(r => r.json())
  .then(data => {
    btn.disabled = false;
    
    if (data.status === 'success') {
      input.value = '';
      
      // Thêm tin nhắn vào UI
      var body = document.getElementById('chatBody');
      
      // Xóa message system nếu có
      var systemMsg = body.querySelector('.chat-message.system');
      if (systemMsg) systemMsg.remove();
      
      var msgDiv = document.createElement('div');
      msgDiv.className = 'chat-message user';
      msgDiv.textContent = data.data.message;
      body.appendChild(msgDiv);
      body.scrollTop = body.scrollHeight;
      
      if (data.data.id > lastMessageId) lastMessageId = data.data.id;
    } else if (data.require_login) {
      alert('Bạn cần đăng nhập để gửi tin nhắn!');
      window.location.href = basePath + '/user/dangnhap.php';
    } else {
      alert(data.message || 'Không thể gửi tin nhắn');
    }
  })
  .catch(err => {
    btn.disabled = false;
    console.error('Send error:', err);
    alert('Lỗi kết nối, vui lòng thử lại!');
  });
}

function escapeHtml(text) {
  if (!text) return '';
  var div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Favorite functionality
document.getElementById('btnFavorite').onclick = function() {
  var btn = this;
  var productId = btn.getAttribute('data-product-id');
  
  var formData = new FormData();
  formData.append('product_id', productId);
  
  fetch('<?php echo $BASE_PATH; ?>/favorites/handle_favorite.php', {
    method: 'POST',
    body: formData,
    credentials: 'same-origin'
  })
  .then(function(r) { return r.json(); })
  .then(function(data) {
    if (data && data.status === 'success') {
      if (data.action === 'added') {
        btn.classList.add('favorited');
        btn.innerHTML = '<i class="fa-solid fa-heart"></i> <span>Đã lưu tin</span>';
      } else {
        btn.classList.remove('favorited');
        btn.innerHTML = '<i class="fa-regular fa-heart"></i> <span>Lưu tin</span>';
      }
      if (typeof window.refreshFavoritesList === 'function') {
        window.refreshFavoritesList();
      }
    } else if (data && data.require_login) {
      alert('Bạn cần đăng nhập để lưu tin!');
      window.location.href = '<?php echo $BASE_PATH; ?>/user/dangnhap.php';
    }
  })
  .catch(function() {
    alert('Có lỗi xảy ra, vui lòng thử lại!');
  });
};

// Cleanup on page leave
window.addEventListener('beforeunload', function() {
  if (chatPolling) clearInterval(chatPolling);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
