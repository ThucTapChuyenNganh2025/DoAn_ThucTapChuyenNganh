<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/connect.php';
?>

<style>
/* ========== PRODUCT CARD STYLES ========== */
#posts-section {
  background: #f5f5f5;
  padding: 60px 0;
}
#posts-section .section-title {
  font-size: 26px;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: 35px;
}
#posts-container {
  display: flex;
  flex-wrap: wrap;
  margin: 0 -12px;
}
#posts-container > .col-lg-3,
#posts-container > .col-md-4,
#posts-container > .col-sm-6 {
  padding: 0 12px;
  margin-bottom: 24px;
  display: flex;
}

.product-card {
  position: relative;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
  width: 100%;
  display: flex;
  flex-direction: column;
  border: 1px solid #eee;
}
.product-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}

.product-card .btn-wishlist {
  position: absolute;
  top: 10px;
  left: 10px;
  z-index: 10;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #fff;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #999;
  box-shadow: 0 2px 8px rgba(0,0,0,0.12);
  padding: 0;
}
.product-card .btn-wishlist:hover {
  background: #ff4757;
  color: #fff;
  transform: scale(1.1);
}
.product-card .btn-wishlist.favorited {
  background: #ff4757;
  color: #fff;
}
.product-card .btn-wishlist.favorited svg {
  fill: currentColor;
}

.product-card-link {
  display: flex;
  flex-direction: column;
  flex: 1;
  text-decoration: none;
  color: inherit;
}
.product-card-link:hover {
  text-decoration: none;
  color: inherit;
}

.product-card-img {
  position: relative;
  width: 100%;
  height: 180px;
  overflow: hidden;
  background: #f8f8f8;
}
.product-card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}
.product-card:hover .product-card-img img {
  transform: scale(1.05);
}

.product-category-badge {
  position: absolute;
  bottom: 8px;
  right: 8px;
  background: rgba(0,0,0,0.7);
  color: #fff;
  font-size: 10px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 12px;
}

.product-card-body {
  padding: 14px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.product-card-title {
  font-size: 14px;
  font-weight: 600;
  color: #222;
  margin: 0 0 6px 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  height: 40px;
}

.product-card-desc {
  font-size: 12px;
  color: #777;
  margin: 0 0 10px 0;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  height: 34px;
}

.product-card-price {
  font-size: 16px;
  font-weight: 700;
  color: #e53935;
  margin-bottom: 10px;
}

.product-card-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  font-size: 11px;
  color: #888;
  border-top: 1px solid #f0f0f0;
  padding-top: 10px;
  margin-top: auto;
}
.product-card-location,
.product-card-time {
  display: flex;
  align-items: center;
  gap: 4px;
}
.product-card-location svg,
.product-card-time svg {
  opacity: 0.6;
}

/* Responsive - 4 columns on large screens */
@media (min-width: 992px) {
  #posts-container > .col-lg-3 {
    width: 25%;
    flex: 0 0 25%;
  }
}
@media (max-width: 991px) {
  #posts-container > .col-md-4 {
    width: 33.333%;
    flex: 0 0 33.333%;
  }
}
@media (max-width: 767px) {
  #posts-container > .col-sm-6 {
    width: 50%;
    flex: 0 0 50%;
  }
  .product-card-img {
    height: 150px;
  }
}
@media (max-width: 575px) {
  #posts-container > .col-sm-6 {
    width: 100%;
    flex: 0 0 100%;
  }
  .product-card-img {
    height: 200px;
  }
}

/* Load more button */
#load-more-posts {
  padding: 10px 30px;
  font-weight: 600;
  border-radius: 25px;
}

/* Wishlist loading state */
.btn-wishlist--loading {
  opacity: 0.6;
  pointer-events: none;
}
</style>

<?php
// Helper functions
function sm_db_excerpt($html, $len = 80) {
  $txt = strip_tags($html ?? '');
  $txt = trim($txt);
  if (mb_strlen($txt, 'UTF-8') <= $len) return $txt;
  return mb_substr($txt, 0, $len, 'UTF-8') . '...';
}

function sm_db_image($image_file) {
  $uploads = realpath(__DIR__ . '/uploads');
  if ($image_file && $uploads) {
    $candidate = $uploads . DIRECTORY_SEPARATOR . $image_file;
    if (is_file($candidate)) return 'uploads/' . $image_file;
    $base = strtolower(basename($image_file));
    $files = @scandir($uploads);
    if (is_array($files)) {
      foreach ($files as $f) {
        if (strtolower($f) === $base) return 'uploads/' . $f;
      }
    }
  }
  return 'images/default-product.jpg';
}

function sm_time_ago($datetime) {
  $time = strtotime($datetime);
  $diff = time() - $time;
  if ($diff < 60) return 'Vừa xong';
  if ($diff < 3600) return floor($diff / 60) . ' phút trước';
  if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
  if ($diff < 604800) return floor($diff / 86400) . ' ngày trước';
  return date('d/m/Y', $time);
}

// Lấy tham số tìm kiếm và lọc
$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;

// Xây dựng tiêu đề section
$section_title = 'Tin đăng mới';
if ($search_query || $category_id) {
  $section_title = 'Kết quả tìm kiếm';
}

// Lấy danh sách tin đăng
$limit = 12;

// Xây dựng câu query động
$where_conditions = ["p.status = 'approved'"];
$params = [];
$types = '';

if ($search_query) {
  $where_conditions[] = "(p.title LIKE ? OR p.description LIKE ?)";
  $search_param = '%' . $search_query . '%';
  $params[] = $search_param;
  $params[] = $search_param;
  $types .= 'ss';
}

if ($category_id > 0) {
  $where_conditions[] = "p.category_id = ?";
  $params[] = $category_id;
  $types .= 'i';
}

$where_sql = implode(' AND ', $where_conditions);

$sql = "SELECT DISTINCT p.id, p.title, p.description, p.price, p.currency, p.created_at,
              (SELECT filename FROM product_images pi2 WHERE pi2.product_id = p.id ORDER BY pi2.sort_order ASC LIMIT 1) AS image_file,
              c.name AS category_name,
              l.province AS location_province,
              l.district AS location_district
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN locations l ON p.location_id = l.id
        WHERE {$where_sql}
        ORDER BY p.created_at DESC, p.id DESC
        LIMIT {$limit}";

$posts = [];
if ($stmt = $conn->prepare($sql)) {
  if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $res = $stmt->get_result();
  while ($row = $res->fetch_assoc()) {
    $posts[] = $row;
  }
  $stmt->close();
}
?>

<!-- ======================= TIN ĐĂNG MỚI ======================= -->
<section class="py-5" id="posts-section">
  <div class="container">
    <h2 class="section-title text-center mb-4"><?php echo $section_title; ?></h2>
    
    <?php if ($search_query || $category_id): ?>
    <div class="search-info text-center mb-4">
      <?php if ($search_query): ?>
        <span class="badge bg-secondary me-2" style="font-size:14px;padding:8px 15px;">
          <i class="fa-solid fa-search me-1"></i> "<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>"
        </span>
      <?php endif; ?>
      <?php if ($category_id > 0): 
        $cat_name = '';
        foreach ($categories as $cat) {
          if ($cat['id'] == $category_id) {
            $cat_name = $cat['name'];
            break;
          }
        }
        // Nếu không tìm thấy trong biến $categories, query lại
        if (!$cat_name) {
          $cat_stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
          $cat_stmt->bind_param('i', $category_id);
          $cat_stmt->execute();
          $cat_stmt->bind_result($cat_name);
          $cat_stmt->fetch();
          $cat_stmt->close();
        }
      ?>
        <span class="badge bg-primary" style="font-size:14px;padding:8px 15px;">
          <i class="fa-solid fa-tag me-1"></i> <?php echo htmlspecialchars($cat_name, ENT_QUOTES, 'UTF-8'); ?>
        </span>
      <?php endif; ?>
      <div class="mt-2">
        <small class="text-muted">Tìm thấy <?php echo count($posts); ?> sản phẩm</small>
        <a href="<?php echo $BASE_PATH; ?>/index.php" class="btn btn-sm btn-outline-secondary ms-2">
          <i class="fa-solid fa-times me-1"></i> Xóa bộ lọc
        </a>
      </div>
    </div>
    <?php endif; ?>
    
    <div class="row" id="posts-container" data-limit="<?php echo $limit; ?>" data-query="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>" data-category="<?php echo $category_id; ?>">
      <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $p):
          $title = htmlspecialchars($p['title'] ?? '', ENT_QUOTES, 'UTF-8');
          $img = sm_db_image($p['image_file'] ?? '');
          $excerpt = htmlspecialchars(sm_db_excerpt($p['description'] ?? '', 80), ENT_QUOTES, 'UTF-8');
          $link = 'product.php?id=' . (int)$p['id'];
          $priceVal = ($p['price'] > 0) 
            ? number_format($p['price'], 0, ',', '.') . ' ' . htmlspecialchars($p['currency'] ?? 'đ', ENT_QUOTES, 'UTF-8')
            : 'Thỏa thuận';
          $categoryName = htmlspecialchars($p['category_name'] ?? 'Chưa phân loại', ENT_QUOTES, 'UTF-8');
          
          $location = '';
          if (!empty($p['location_district'])) $location = htmlspecialchars($p['location_district'], ENT_QUOTES, 'UTF-8');
          if (!empty($p['location_province'])) {
            $location = $location ? $location . ', ' . htmlspecialchars($p['location_province'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($p['location_province'], ENT_QUOTES, 'UTF-8');
          }
          if (!$location) $location = 'Chưa cập nhật';
          
          $timeAgo = !empty($p['created_at']) ? sm_time_ago($p['created_at']) : '';
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
          <div class="product-card h-100" data-category="<?= $categoryName ?>" data-product-id="<?= (int)$p['id'] ?>">
            <button type="button" class="btn-wishlist favorite-btn" title="Yêu thích">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
              </svg>
            </button>
            
            <a href="<?= $link ?>" class="product-card-link">
              <div class="product-card-img">
                <img src="<?= $img ?>" alt="<?= $title ?>" loading="lazy">
                <?php if ($categoryName !== 'Chưa phân loại'): ?>
                <span class="product-category-badge"><?= $categoryName ?></span>
                <?php endif; ?>
              </div>
              
              <div class="product-card-body">
                <h3 class="product-card-title"><?= $title ?></h3>
                <p class="product-card-desc"><?= $excerpt ?></p>
                <div class="product-card-price"><?= $priceVal ?></div>
                
                <div class="product-card-meta">
                  <div class="product-card-location">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                      <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span><?= $location ?></span>
                  </div>
                  <div class="product-card-time">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span><?= $timeAgo ?></span>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center text-muted py-5">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" class="mb-3">
            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
            <line x1="8" y1="21" x2="16" y2="21"></line>
            <line x1="12" y1="17" x2="12" y2="21"></line>
          </svg>
          <p class="mb-0">Chưa có tin đăng nào.</p>
        </div>
      <?php endif; ?>
    </div>
    
    <?php if (!empty($posts)): ?>
    <div class="text-center mt-3">
      <button id="load-more-posts" class="btn btn-outline-primary">Tải thêm</button>
    </div>
    <?php endif; ?>
  </div>
</section>

<script src="js/jquery-1.11.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script src="js/plugins.js"></script>
<script src="js/script.js"></script>
<script src="js/main.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  
  // Đánh dấu các sản phẩm đã được yêu thích
  fetch('favorites/get_favorites.php', { credentials: 'same-origin' })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data || data.status !== 'success' || !Array.isArray(data.favorites)) return;
      var favoriteIds = data.favorites.map(function(f) { return String(f.product_id || ''); });
      document.querySelectorAll('[data-product-id]').forEach(function(card) {
        var pid = String(card.getAttribute('data-product-id') || '');
        if (favoriteIds.indexOf(pid) !== -1) {
          var btn = card.querySelector('.btn-wishlist');
          if (btn) btn.classList.add('favorited');
        }
      });
    })
    .catch(function() {});

  // Xử lý nút yêu thích (event delegation)
  document.body.addEventListener('click', function(e) {
    var btn = e.target.closest('.btn-wishlist');
    if (!btn) return;
    
    e.preventDefault();
    e.stopPropagation();
    
    var card = btn.closest('.product-card');
    if (!card) return;

    var productId = card.getAttribute('data-product-id');
    btn.classList.add('btn-wishlist--loading');

    var formData = new FormData();
    formData.append('product_id', productId);

    fetch('favorites/handle_favorite.php', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      btn.classList.remove('btn-wishlist--loading');
      if (data && data.status === 'success') {
        if (data.action === 'added') {
          btn.classList.add('favorited');
          showToast('Đã thêm vào yêu thích!', 'success');
        } else {
          btn.classList.remove('favorited');
          showToast('Đã bỏ yêu thích.', 'info');
        }
        if (typeof window.refreshFavoritesList === 'function') {
          window.refreshFavoritesList();
        }
      } else if (data && data.require_login) {
        showToast('Bạn cần đăng nhập để yêu thích sản phẩm!', 'warning');
        setTimeout(function() {
          window.location.href = 'user/dangnhap.php';
        }, 1500);
      } else {
        showToast(data.message || 'Có lỗi xảy ra!', 'error');
      }
    })
    .catch(function() {
      btn.classList.remove('btn-wishlist--loading');
      showToast('Lỗi kết nối, vui lòng thử lại!', 'error');
    });
  });
  
  // Toast notification function
  function showToast(message, type) {
    var toast = document.createElement('div');
    toast.className = 'toast-notification toast-' + (type || 'info');
    toast.textContent = message;
    toast.style.cssText = 'position:fixed;bottom:20px;right:20px;padding:12px 24px;border-radius:8px;color:#fff;font-size:14px;z-index:9999;animation:slideIn 0.3s ease;box-shadow:0 4px 12px rgba(0,0,0,0.15);';
    
    if (type === 'success') toast.style.background = '#28a745';
    else if (type === 'warning') toast.style.background = '#ffc107'; 
    else if (type === 'error') toast.style.background = '#dc3545';
    else toast.style.background = '#17a2b8';
    
    if (type === 'warning') toast.style.color = '#333';
    
    document.body.appendChild(toast);
    setTimeout(function() {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity 0.3s ease';
      setTimeout(function() { toast.remove(); }, 300);
    }, 2500);
  }

  // Load more posts
  (function() {
    var loadBtn = document.getElementById('load-more-posts');
    var container = document.getElementById('posts-container');
    if (!loadBtn || !container) return;
    
    var limit = parseInt(container.getAttribute('data-limit') || 12, 10);
    var searchQuery = container.getAttribute('data-query') || '';
    var categoryId = container.getAttribute('data-category') || '';

    loadBtn.addEventListener('click', function() {
      loadBtn.disabled = true;
      loadBtn.textContent = 'Đang tải...';
      
      var existingIds = Array.from(container.querySelectorAll('[data-product-id]'))
        .map(function(el) { return el.getAttribute('data-product-id'); });
      
      var url = 'favorites/get_products.php?limit=' + limit + '&offset=0&exclude_ids=' + encodeURIComponent(existingIds.join(','));
      
      // Thêm query và category nếu có
      if (searchQuery) {
        url += '&q=' + encodeURIComponent(searchQuery);
      }
      if (categoryId) {
        url += '&category=' + encodeURIComponent(categoryId);
      }
      
      fetch(url, { credentials: 'same-origin' })
        .then(function(r) { return r.json(); })
        .then(function(data) {
          loadBtn.disabled = false;
          loadBtn.textContent = 'Tải thêm';
          
          if (!data || data.status !== 'success' || !Array.isArray(data.products)) return;
          
          var prods = data.products;
          if (prods.length === 0) {
            loadBtn.style.display = 'none';
            var msg = document.createElement('div');
            msg.className = 'text-muted small mt-2';
            msg.textContent = 'Không còn tin đăng.';
            loadBtn.parentNode.appendChild(msg);
            return;
          }
          
          var html = '';
          prods.forEach(function(row) {
            if (existingIds.indexOf(String(row.id)) !== -1) return;
            
            var title = (row.title || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            var desc = (row.description || '').replace(/<[^>]*>/g, '');
            var excerpt = desc.substring(0, 80) + (desc.length > 80 ? '...' : '');
            excerpt = excerpt.replace(/</g, '&lt;').replace(/>/g, '&gt;');
            var img = row.image || 'images/default-product.jpg';
            var link = 'product.php?id=' + row.id;
            var price = (row.price > 0) ? parseFloat(row.price).toLocaleString('vi-VN') + ' ' + (row.currency || 'đ') : 'Thỏa thuận';
            var category = (row.category_name || 'Chưa phân loại').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            var location = '';
            if (row.location_district) location = row.location_district;
            if (row.location_province) location = location ? location + ', ' + row.location_province : row.location_province;
            if (!location) location = 'Chưa cập nhật';
            location = location.replace(/</g, '&lt;').replace(/>/g, '&gt;');
            var timeStr = timeAgo(row.created_at);
            
            html += '<div class="col-lg-3 col-md-4 col-sm-6 mb-4">' +
              '<div class="product-card h-100" data-category="' + category + '" data-product-id="' + row.id + '">' +
              '<button type="button" class="btn-wishlist favorite-btn" title="Yêu thích">' +
              '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>' +
              '</button>' +
              '<a href="' + link + '" class="product-card-link">' +
              '<div class="product-card-img"><img src="' + img + '" alt="' + title + '" loading="lazy">' +
              (category !== 'Chưa phân loại' ? '<span class="product-category-badge">' + category + '</span>' : '') +
              '</div>' +
              '<div class="product-card-body">' +
              '<h3 class="product-card-title">' + title + '</h3>' +
              '<p class="product-card-desc">' + excerpt + '</p>' +
              '<div class="product-card-price">' + price + '</div>' +
              '<div class="product-card-meta">' +
              '<div class="product-card-location"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg><span>' + location + '</span></div>' +
              '<div class="product-card-time"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg><span>' + timeStr + '</span></div>' +
              '</div></div></a></div></div>';
          });
          
          if (html) {
            container.insertAdjacentHTML('beforeend', html);
          }
        })
        .catch(function() {
          loadBtn.disabled = false;
          loadBtn.textContent = 'Lỗi, thử lại';
          setTimeout(function() { loadBtn.textContent = 'Tải thêm'; }, 1500);
        });
    });
    
    // Helper function
    function timeAgo(dateStr) {
      if (!dateStr) return '';
      var diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
      if (diff < 60) return 'Vừa xong';
      if (diff < 3600) return Math.floor(diff / 60) + ' phút trước';
      if (diff < 86400) return Math.floor(diff / 3600) + ' giờ trước';
      if (diff < 604800) return Math.floor(diff / 86400) + ' ngày trước';
      var d = new Date(dateStr);
      return d.getDate().toString().padStart(2, '0') + '/' + (d.getMonth() + 1).toString().padStart(2, '0') + '/' + d.getFullYear();
    }
  })();
  
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
