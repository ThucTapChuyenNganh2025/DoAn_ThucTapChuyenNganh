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

// Kiểm tra đây có phải bài đăng của mình không
$isOwner = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $p['seller_id']) {
  $isOwner = true;
}

// Kiểm tra đã yêu thích chưa
$isFavorited = false;
if (isset($_SESSION['user_id']) && !$isOwner) {
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
}

/* Rating Styles */
.rating-big {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
}
.rating-big .rating-number {
  font-size: 48px;
  font-weight: 700;
  color: #1a1a2e;
}
.rating-big .rating-star {
  font-size: 36px;
  color: #f6c23e;
}
.rating-total {
  font-size: 14px;
}
.rating-bar-item {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 8px;
}
.rating-bar-item .star-label {
  width: 40px;
  font-size: 13px;
  color: #666;
}
.rating-bar-item .progress {
  height: 8px;
  border-radius: 4px;
  background: #e9ecef;
}
.rating-bar-item .count-label {
  width: 30px;
  text-align: right;
  font-size: 12px;
  color: #888;
}

/* Star Rating Input */
.star-rating-input {
  font-size: 28px;
  cursor: pointer;
}
.star-rating-input .star-input {
  color: #ddd;
  transition: color 0.2s;
  padding: 0 2px;
}
.star-rating-input .star-input:hover,
.star-rating-input .star-input.active {
  color: #f6c23e;
}

/* Rating Item */
.rating-item {
  padding: 15px;
  border-bottom: 1px solid #f0f0f0;
}
.rating-item:last-child {
  border-bottom: none;
}
.rating-item-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 8px;
}
.rating-item-avatar {
  width: 36px;
  height: 36px;
  background: #e9ecef;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: #666;
}
.rating-item-name {
  font-weight: 600;
  color: #333;
}
.rating-item-stars {
  color: #f6c23e;
  font-size: 14px;
}
.rating-item-date {
  font-size: 12px;
  color: #888;
  margin-left: auto;
}
.rating-item-comment {
  color: #555;
  font-size: 14px;
  line-height: 1.5;
  margin-left: 46px;
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
              <div class="seller-contact"><?php echo $isOwner ? 'Tin đăng của bạn' : 'Người bán'; ?></div>
            </div>
          </div>
          
          <?php if ($isOwner): ?>
          <!-- Chủ bài đăng - hiển thị nút quản lý -->
          <a href="<?php echo $BASE_PATH; ?>/user/user_sua_tin.php?id=<?php echo $id; ?>" class="btn btn-contact btn-chat">
            <i class="fa-solid fa-pen-to-square"></i> Sửa tin đăng
          </a>
          <a href="<?php echo $BASE_PATH; ?>/user/user_quanlytin.php" class="btn btn-contact btn-phone" style="background: #6c757d;">
            <i class="fa-solid fa-list-check"></i> Quản lý tin đăng
          </a>
          <a href="<?php echo $BASE_PATH; ?>/user/messages.php" class="btn btn-contact btn-favorite" style="background: #fff; border: 2px solid #0d6efd; color: #0d6efd;">
            <i class="fa-regular fa-comment-dots"></i> Xem tin nhắn
          </a>
          <?php elseif (isset($_SESSION['user_id'])): ?>
          <!-- Người dùng khác đã đăng nhập -->
          <a href="<?php echo $BASE_PATH; ?>/user/messages.php?product_id=<?php echo $id; ?>&user_id=<?php echo (int)$p['seller_id']; ?>" class="btn btn-contact btn-chat">
            <i class="fa-regular fa-comment-dots"></i> Chat với người bán
          </a>
          <?php if ($sellerPhone): ?>
          <a href="tel:<?php echo $sellerPhone; ?>" class="btn btn-contact btn-phone">
            <i class="fa-solid fa-phone"></i> <?php echo $sellerPhone; ?>
          </a>
          <?php endif; ?>
          <button class="btn btn-contact btn-favorite <?php echo $isFavorited ? 'favorited' : ''; ?>" id="btnFavorite" data-product-id="<?php echo $id; ?>">
            <i class="fa-<?php echo $isFavorited ? 'solid' : 'regular'; ?> fa-heart"></i>
            <span><?php echo $isFavorited ? 'Đã lưu tin' : 'Lưu tin'; ?></span>
          </button>
          <?php else: ?>
          <!-- Chưa đăng nhập -->
          <a href="<?php echo $BASE_PATH; ?>/user/dangnhap.php" class="btn btn-contact btn-chat">
            <i class="fa-regular fa-comment-dots"></i> Chat với người bán
          </a>
          <?php if ($sellerPhone): ?>
          <a href="tel:<?php echo $sellerPhone; ?>" class="btn btn-contact btn-phone">
            <i class="fa-solid fa-phone"></i> <?php echo $sellerPhone; ?>
          </a>
          <?php endif; ?>
          <a href="<?php echo $BASE_PATH; ?>/user/dangnhap.php" class="btn btn-contact btn-favorite">
            <i class="fa-regular fa-heart"></i>
            <span>Lưu tin</span>
          </a>
          <?php endif; ?>
        </div>
        
        <!-- Warning (chỉ hiện cho người mua) -->
        <?php if (!$isOwner): ?>
        <div class="alert alert-warning mt-3" style="font-size: 13px;">
          <i class="fa-solid fa-triangle-exclamation me-2"></i>
          <strong>Lưu ý:</strong> Để tránh rủi ro, hãy gặp mặt trực tiếp và kiểm tra hàng trước khi thanh toán.
        </div>
        <?php endif; ?>
        
        <!-- Report Button -->
        <?php if (isset($_SESSION['user_id']) && !$isOwner): ?>
        <button class="btn btn-outline-danger btn-sm mt-2" id="btnReport" data-bs-toggle="modal" data-bs-target="#reportModal">
          <i class="fa-solid fa-flag me-1"></i> Báo cáo tin đăng
        </button>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- Ratings Section -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="product-description" id="ratingsSection">
          <h5><i class="fa-solid fa-star me-2"></i>Đánh giá người bán</h5>
          
          <!-- Rating Summary -->
          <div class="row mb-4">
            <div class="col-md-4 text-center">
              <div class="rating-big" id="ratingAverage">
                <span class="rating-number">0</span>
                <span class="rating-star">★</span>
              </div>
              <div class="rating-total text-muted"><span id="ratingTotal">0</span> đánh giá</div>
            </div>
            <div class="col-md-8">
              <div class="rating-bars" id="ratingBars">
                <?php for($i = 5; $i >= 1; $i--): ?>
                <div class="rating-bar-item">
                  <span class="star-label"><?php echo $i; ?> ★</span>
                  <div class="progress flex-grow-1">
                    <div class="progress-bar bg-warning" id="bar<?php echo $i; ?>" style="width: 0%"></div>
                  </div>
                  <span class="count-label" id="count<?php echo $i; ?>">0</span>
                </div>
                <?php endfor; ?>
              </div>
            </div>
          </div>
          
          <!-- Rating Form -->
          <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $p['seller_id']): ?>
          <div class="rating-form-wrapper mb-4 p-3 bg-light rounded">
            <h6 class="mb-3">Đánh giá của bạn</h6>
            <div class="star-rating-input mb-3" id="starRatingInput">
              <?php for($i = 1; $i <= 5; $i++): ?>
              <span class="star-input" data-value="<?php echo $i; ?>">☆</span>
              <?php endfor; ?>
              <input type="hidden" id="ratingValue" value="0">
            </div>
            <textarea class="form-control mb-2" id="ratingComment" rows="2" placeholder="Nhận xét về người bán (không bắt buộc)"></textarea>
            <button class="btn btn-primary btn-sm" id="submitRating">
              <i class="fa-solid fa-paper-plane me-1"></i> Gửi đánh giá
            </button>
          </div>
          <?php elseif (!isset($_SESSION['user_id'])): ?>
          <div class="alert alert-info mb-4">
            <a href="<?php echo $BASE_PATH; ?>/user/dangnhap.php">Đăng nhập</a> để đánh giá người bán
          </div>
          <?php endif; ?>
          
          <!-- Rating List -->
          <div id="ratingsList">
            <p class="text-muted">Đang tải đánh giá...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa-solid fa-flag text-danger me-2"></i>Báo cáo tin đăng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3">Vui lòng cho chúng tôi biết lý do bạn báo cáo tin đăng này:</p>
        
        <div class="mb-3">
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="reportReason" id="reason1" value="Tin đăng giả mạo, lừa đảo">
            <label class="form-check-label" for="reason1">Tin đăng giả mạo, lừa đảo</label>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="reportReason" id="reason2" value="Hàng hóa bị cấm bán">
            <label class="form-check-label" for="reason2">Hàng hóa bị cấm bán</label>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="reportReason" id="reason3" value="Thông tin sai sự thật">
            <label class="form-check-label" for="reason3">Thông tin sai sự thật</label>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="reportReason" id="reason4" value="Trùng lặp, spam">
            <label class="form-check-label" for="reason4">Trùng lặp, spam</label>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="radio" name="reportReason" id="reason5" value="other">
            <label class="form-check-label" for="reason5">Lý do khác</label>
          </div>
        </div>
        
        <div id="otherReasonWrapper" style="display: none;">
          <textarea class="form-control" id="otherReason" rows="3" placeholder="Mô tả chi tiết lý do báo cáo (tối thiểu 10 ký tự)"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="button" class="btn btn-danger" id="submitReport">
          <i class="fa-solid fa-flag me-1"></i> Gửi báo cáo
        </button>
      </div>
    </div>
  </div>
</div>

<script>
const basePath = <?php echo json_encode($BASE_PATH); ?>;

// Image gallery
function changeImage(thumb, src) {
  document.getElementById('mainImage').src = src;
  document.querySelectorAll('.thumbnail-item').forEach(function(t) {
    t.classList.remove('active');
  });
  thumb.classList.add('active');
}

// Favorite functionality
var btnFavorite = document.getElementById('btnFavorite');
if (btnFavorite) {
  btnFavorite.onclick = function() {
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
        toastWarning('Bạn cần đăng nhập để lưu tin!');
        setTimeout(() => {
          window.location.href = '<?php echo $BASE_PATH; ?>/user/dangnhap.php';
        }, 1500);
      }
    })
    .catch(function() {
      toastError('Có lỗi xảy ra, vui lòng thử lại!');
    });
  };
}

// === RATING FUNCTIONALITY ===
const productId = <?php echo $id; ?>;
const sellerId = <?php echo (int)$p['seller_id']; ?>;

// Load ratings
function loadRatings() {
  fetch(basePath + '/api/get_ratings.php?product_id=' + productId)
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        // Update summary
        document.getElementById('ratingAverage').innerHTML = 
          '<span class="rating-number">' + data.stats.average + '</span><span class="rating-star">★</span>';
        document.getElementById('ratingTotal').textContent = data.stats.total;
        
        // Update bars
        for (let i = 1; i <= 5; i++) {
          const count = data.stats.breakdown[i] || 0;
          const percent = data.stats.total > 0 ? (count / data.stats.total * 100) : 0;
          document.getElementById('bar' + i).style.width = percent + '%';
          document.getElementById('count' + i).textContent = count;
        }
        
        // Update current user rating
        if (data.user_rating && document.getElementById('starRatingInput')) {
          document.getElementById('ratingValue').value = data.user_rating.rating;
          document.getElementById('ratingComment').value = data.user_rating.comment || '';
          updateStarDisplay(data.user_rating.rating);
        }
        
        // Render ratings list
        renderRatingsList(data.ratings);
      }
    });
}

function renderRatingsList(ratings) {
  const container = document.getElementById('ratingsList');
  if (!ratings || ratings.length === 0) {
    container.innerHTML = '<p class="text-muted">Chưa có đánh giá nào</p>';
    return;
  }
  
  let html = '';
  ratings.forEach(r => {
    const stars = '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating);
    const initial = r.rater_name.charAt(0).toUpperCase();
    const date = new Date(r.created_at).toLocaleDateString('vi-VN');
    
    html += `
      <div class="rating-item">
        <div class="rating-item-header">
          <div class="rating-item-avatar">${initial}</div>
          <span class="rating-item-name">${escapeHtml(r.rater_name)}</span>
          <span class="rating-item-stars">${stars}</span>
          <span class="rating-item-date">${date}</span>
        </div>
        ${r.comment ? '<div class="rating-item-comment">' + escapeHtml(r.comment) + '</div>' : ''}
      </div>
    `;
  });
  container.innerHTML = html;
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

function updateStarDisplay(value) {
  document.querySelectorAll('#starRatingInput .star-input').forEach((star, index) => {
    star.textContent = index < value ? '★' : '☆';
    star.classList.toggle('active', index < value);
  });
}

// Star rating input
if (document.getElementById('starRatingInput')) {
  document.querySelectorAll('#starRatingInput .star-input').forEach(star => {
    star.addEventListener('click', function() {
      const value = parseInt(this.dataset.value);
      document.getElementById('ratingValue').value = value;
      updateStarDisplay(value);
    });
    
    star.addEventListener('mouseenter', function() {
      const value = parseInt(this.dataset.value);
      document.querySelectorAll('#starRatingInput .star-input').forEach((s, index) => {
        s.textContent = index < value ? '★' : '☆';
      });
    });
  });
  
  document.getElementById('starRatingInput').addEventListener('mouseleave', function() {
    const value = parseInt(document.getElementById('ratingValue').value) || 0;
    updateStarDisplay(value);
  });
}

// Submit rating
if (document.getElementById('submitRating')) {
  document.getElementById('submitRating').onclick = function() {
    const rating = parseInt(document.getElementById('ratingValue').value);
    const comment = document.getElementById('ratingComment').value.trim();
    
    if (rating < 1 || rating > 5) {
      toastWarning('Vui lòng chọn số sao đánh giá');
      return;
    }
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('rating', rating);
    formData.append('comment', comment);
    
    fetch(basePath + '/api/submit_rating.php', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        toastSuccess(data.message);
        loadRatings();
      } else {
        toastError(data.message || 'Có lỗi xảy ra');
      }
    })
    .catch(() => toastError('Có lỗi xảy ra, vui lòng thử lại'));
  };
}

// Load ratings on page load
loadRatings();

// === REPORT FUNCTIONALITY ===
// Toggle other reason textarea
document.querySelectorAll('input[name="reportReason"]').forEach(radio => {
  radio.addEventListener('change', function() {
    const otherWrapper = document.getElementById('otherReasonWrapper');
    otherWrapper.style.display = this.value === 'other' ? 'block' : 'none';
  });
});

// Submit report
if (document.getElementById('submitReport')) {
  document.getElementById('submitReport').onclick = function() {
    const selected = document.querySelector('input[name="reportReason"]:checked');
    if (!selected) {
      toastWarning('Vui lòng chọn lý do báo cáo');
      return;
    }
    
    let reason = selected.value;
    if (reason === 'other') {
      reason = document.getElementById('otherReason').value.trim();
      if (reason.length < 10) {
        toastWarning('Vui lòng mô tả chi tiết lý do (tối thiểu 10 ký tự)');
        return;
      }
    }
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('reason', reason);
    
    fetch(basePath + '/api/submit_report.php', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        toastSuccess(data.message);
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('reportModal'));
        modal.hide();
        // Reset form
        document.querySelectorAll('input[name="reportReason"]').forEach(r => r.checked = false);
        document.getElementById('otherReason').value = '';
        document.getElementById('otherReasonWrapper').style.display = 'none';
      } else {
        toastError(data.message || 'Có lỗi xảy ra');
      }
    })
    .catch(() => toastError('Có lỗi xảy ra, vui lòng thử lại'));
  };
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
