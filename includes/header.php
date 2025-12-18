<?php
// Header include - used by pages under project root and subfolders.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$BASE_PATH = '/DoAn_ThucTapChuyenNganh';

// Kết nối database (phải include trước để lấy categories)
require_once __DIR__ . '/../config/connect.php';

// Lấy tên người dùng từ session (hỗ trợ cả user và admin)
$display_username = null;
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $display_username = trim($_SESSION['user_name']);
} elseif (isset($_SESSION['admin_name']) && !empty($_SESSION['admin_name'])) {
    $display_username = trim($_SESSION['admin_name']);
}

if ($display_username !== null) {
    $display_username = htmlspecialchars($display_username, ENT_QUOTES, 'UTF-8');
}
$categories = [];
$cat_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$cat_result = $conn->query($cat_sql);
if ($cat_result && $cat_result->num_rows > 0) {
    while ($cat_row = $cat_result->fetch_assoc()) {
        $categories[] = $cat_row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <title>Chợ Điện Tử Online</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Hệ thống chợ điện tử trực tuyến">

  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo $BASE_PATH; ?>/css/vendor.css">
  <link rel="stylesheet" href="<?php echo $BASE_PATH; ?>/style.css">
  <link rel="stylesheet" href="<?php echo $BASE_PATH; ?>/css/user.css">
  
  <style>
    /* ===== HEADER STYLES ===== */
    .site-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1050;
      background: #1a1a2e;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
      padding: 12px 0;
      transition: padding 0.3s ease;
    }
    .site-header.scrolled {
      padding: 6px 0;
    }
    
    /* Logo */
    .site-logo {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none !important;
      color: #fff !important;
      font-weight: 800;
      font-size: 20px;
      text-transform: uppercase;
    }
    .site-logo:hover { color: #fff !important; }
    .site-logo .logo-icon {
      width: 36px;
      height: 36px;
      background: linear-gradient(45deg, #f6c23e, #dda20a);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #1a1a2e;
      font-size: 16px;
    }
    
    /* Search bar */
    .search-wrapper {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 1;
      max-width: 600px;
      margin: 0 auto;
    }
    
    .header-search {
      background: rgba(255,255,255,0.97);
      border-radius: 50px;
      padding: 5px 6px 5px 6px;
      display: flex;
      align-items: center;
      width: 100%;
      transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
      overflow: hidden;
    }
    
    .search-icon-box {
      width: 38px;
      height: 38px;
      min-width: 38px;
      background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #1a1a2e;
      font-size: 14px;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      flex-shrink: 0;
    }
    .search-icon-box:hover {
      transform: scale(1.08);
      box-shadow: 0 4px 15px rgba(246, 194, 62, 0.5);
    }
    
    .header-search input {
      border: none;
      outline: none;
      padding: 10px 15px;
      flex: 1;
      font-size: 14px;
      background: transparent;
      min-width: 0;
      color: #333;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .header-search input::placeholder {
      color: #888;
    }
    .header-search .btn-search {
      background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
      color: #fff;
      border: none;
      border-radius: 25px;
      padding: 10px 22px;
      font-size: 14px;
      font-weight: 500;
      white-space: nowrap;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .header-search .btn-search:hover {
      background: linear-gradient(135deg, #2d2d44 0%, #3d3d54 100%);
    }
    
    /* When scrolled: collapse to icon */
    .site-header.scrolled .search-wrapper {
      justify-content: flex-start;
      max-width: none;
      margin: 0 20px;
    }
    .site-header.scrolled .header-search {
      width: 42px;
      height: 42px;
      padding: 2px;
      border-radius: 50%;
      background: transparent;
    }
    .site-header.scrolled .header-search input,
    .site-header.scrolled .header-search .btn-search {
      width: 0;
      padding: 0;
      opacity: 0;
      pointer-events: none;
    }
    .site-header.scrolled .search-icon-box {
      width: 36px;
      height: 36px;
      min-width: 36px;
      background: rgba(255,255,255,0.15);
      color: #fff;
    }
    .site-header.scrolled .search-icon-box:hover {
      background: #f6c23e;
      color: #1a1a2e;
    }
    
    /* When expanded after scroll */
    .site-header.scrolled .search-wrapper.expanded .header-search {
      width: 480px;
      height: 42px;
      padding: 2px 4px 2px 2px;
      border-radius: 25px;
      background: rgba(255,255,255,0.97);
      box-shadow: 0 8px 30px rgba(0,0,0,0.25);
    }
    .site-header.scrolled .search-wrapper.expanded .header-search input {
      width: auto;
      padding: 8px 12px;
      opacity: 1;
      pointer-events: auto;
    }
    .site-header.scrolled .search-wrapper.expanded .header-search .btn-search {
      width: auto;
      padding: 6px 16px;
      opacity: 1;
      pointer-events: auto;
      font-size: 12px;
      border-radius: 18px;
      height: 32px;
    }
    .site-header.scrolled .search-wrapper.expanded .search-icon-box {
      background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
      color: #1a1a2e;
    }
    
    /* Header icons */
    .header-icon {
      width: 40px;
      height: 40px;
      background: #f0f0f0;
      border-radius: 50%;
      display: flex !important;
      align-items: center;
      justify-content: center;
      color: #1a1a2e !important;
      text-decoration: none !important;
      transition: all 0.2s ease;
      border: none;
      cursor: pointer;
      position: relative;
    }
    .header-icon:hover {
      background: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
      color: #1a1a2e !important;
    }
    
    /* Đăng tin button */
    .btn-post {
      background: linear-gradient(45deg, #f6c23e, #dda20a);
      color: #1a1a2e !important;
      font-weight: 600;
      padding: 8px 20px;
      border-radius: 6px;
      text-decoration: none !important;
      border: none;
      font-size: 14px;
      display: inline-block;
    }
    .btn-post:hover {
      background: linear-gradient(45deg, #dda20a, #c99107);
      color: #1a1a2e !important;
    }
    
    /* User greeting */
    .user-greeting {
      text-align: right;
      color: #fff;
    }
    .user-greeting small { opacity: 0.7; font-size: 12px; }
    .user-greeting strong { display: block; font-size: 14px; }
    
    /* Dropdown menu - Bootstrap override */
    .header-dropdown .dropdown-toggle::after {
      display: none !important;
    }
    .header-dropdown .dropdown-menu {
      background: #fff !important;
      border: 1px solid #eee !important;
      border-radius: 10px !important;
      box-shadow: 0 5px 20px rgba(0,0,0,0.15) !important;
      padding: 10px 0 !important;
      min-width: 200px !important;
      margin-top: 10px !important;
    }
    .header-dropdown .dropdown-menu .dropdown-item {
      padding: 10px 20px !important;
      color: #333 !important;
      font-size: 14px !important;
      display: flex !important;
      align-items: center !important;
      gap: 10px !important;
      background: transparent !important;
    }
    .header-dropdown .dropdown-menu .dropdown-item:hover {
      background: #f5f5f5 !important;
      color: #000 !important;
    }
    .header-dropdown .dropdown-menu .dropdown-item.text-danger {
      color: #dc3545 !important;
    }
    .header-dropdown .dropdown-menu .dropdown-item.text-danger:hover {
      background: #fff5f5 !important;
    }
    .header-dropdown .dropdown-divider {
      margin: 5px 0;
      border-color: #eee;
    }
    
    /* Body padding for fixed header */
    body { padding-top: 75px; }
    
    /* Hide elements when scrolled */
    .site-header.scrolled .hide-on-scroll { display: none !important; }
    
    /* Compact on scroll */
    .site-header.scrolled .header-icon { width: 36px; height: 36px; }
    .site-header.scrolled .btn-post { padding: 6px 15px; font-size: 13px; }
    
    /* Category dropdown in search */
    .category-select {
      border: none;
      outline: none;
      background: transparent;
      padding: 8px 12px;
      font-size: 14px;
      color: #333;
      cursor: pointer;
      border-left: 1px solid #ddd;
      min-width: 120px;
      max-width: 150px;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 8px center;
      padding-right: 28px;
    }
    .category-select:focus {
      outline: none;
    }
    .category-select option {
      padding: 8px;
    }
    
    /* Hide category select when scrolled */
    .site-header.scrolled .header-search .category-select {
      width: 0;
      padding: 0;
      opacity: 0;
      border: none;
      min-width: 0;
    }
    .site-header.scrolled .search-wrapper.expanded .header-search .category-select {
      width: auto;
      padding: 6px 24px 6px 10px;
      opacity: 1;
      border-left: 1px solid #ddd;
      min-width: 100px;
      font-size: 12px;
    }
  </style>
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="site-header" id="siteHeader">
  <div class="container-fluid">
    <div class="row align-items-center">
      
      <!-- Logo -->
      <div class="col-auto">
        <a href="<?php echo $BASE_PATH; ?>/index.php" class="site-logo">
          <span class="logo-icon"><i class="fa-solid fa-bolt"></i></span>
          <span class="d-none d-md-inline">CHỢ ĐIỆN TỬ</span>
        </a>
      </div>
      
      <!-- Search Bar (collapsible) -->
      <div class="col d-none d-lg-block">
        <div class="search-wrapper" id="searchWrapper">
          <form action="<?php echo $BASE_PATH; ?>/index.php" method="get" class="header-search" id="headerSearchForm">
            <div class="search-icon-box" id="searchIconBox">
              <i class="fa-solid fa-search"></i>
            </div>
            <input type="text" name="query" placeholder="Tìm sản phẩm, dịch vụ..." id="searchInput">
            <select name="category" class="category-select" id="categorySelect">
              <option value="">Tất cả danh mục</option>
              <?php foreach ($categories as $cat): ?>
              <option value="<?php echo (int)$cat['id']; ?>"><?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-search">Tìm kiếm</button>
          </form>
        </div>
      </div>
      
      <!-- Right Section -->
      <div class="col-auto">
        <div class="d-flex align-items-center gap-3">
          
          <!-- User greeting -->
          <div class="user-greeting d-none d-xl-block hide-on-scroll">
            <small>Xin chào</small>
            <strong><?php echo $display_username ?? 'Khách'; ?></strong>
          </div>
          
          <!-- Đăng tin button -->
          <a href="<?php echo $BASE_PATH; ?>/user/<?php echo $display_username ? 'user_dangtin.php' : 'dangnhap.php?next=user_dangtin.php'; ?>" class="btn-post d-none d-md-inline-block">
            <i class="fa-solid fa-plus me-1"></i> Đăng tin
          </a>
          
          <!-- User Icon with Dropdown -->
          <div class="dropdown header-dropdown">
            <?php if ($display_username): ?>
              <a href="#" class="header-icon dropdown-toggle" id="userDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user"></i>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownBtn">
                <li><a class="dropdown-item" href="<?php echo $BASE_PATH; ?>/user/profile.php"><i class="fa-solid fa-user"></i> Hồ sơ</a></li>
                <li><a class="dropdown-item" href="<?php echo $BASE_PATH; ?>/user/user_dashboard.php"><i class="fa-solid fa-gauge"></i> Tổng quan</a></li>
                <li><a class="dropdown-item" href="<?php echo $BASE_PATH; ?>/user/user_quanlytin.php"><i class="fa-solid fa-list"></i> Quản lý tin</a></li>
                <li><a class="dropdown-item" href="<?php echo $BASE_PATH; ?>/user/messages.php"><i class="fa-solid fa-comments"></i> Tin nhắn</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?php echo $BASE_PATH; ?>/user/dangxuat.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
              </ul>
            <?php else: ?>
              <a href="<?php echo $BASE_PATH; ?>/user/dangnhap.php" class="header-icon" title="Đăng nhập">
                <i class="fa-solid fa-user"></i>
              </a>
            <?php endif; ?>
          </div>
          
          <?php if ($display_username): ?>
          <!-- Messages Icon -->
          <a href="<?php echo $BASE_PATH; ?>/user/messages.php" class="header-icon position-relative" title="Tin nhắn" id="messagesIcon">
            <i class="fa-regular fa-comment-dots"></i>
            <span id="messages-count" class="position-absolute badge rounded-pill d-none" style="font-size:11px;font-weight:700;min-width:20px;height:20px;line-height:20px;padding:0 6px;top:-6px;right:-8px;background:#0084ff;color:#fff;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.3);">0</span>
          </a>
          <?php endif; ?>
          
          <!-- Favorites Icon -->
          <a href="#" class="header-icon" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFavorites">
            <i class="fa-regular fa-heart"></i>
            <span id="favorites-count" class="position-absolute badge rounded-pill d-none" style="font-size:11px;font-weight:700;min-width:20px;height:20px;line-height:20px;padding:0 6px;top:-6px;right:-8px;background:#ff4757;color:#fff;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.3);">0</span>
          </a>
          
        </div>
      </div>
      
    </div>
  </div>
</header>

<!-- ===== OFFCANVAS FAVORITES ===== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFavorites">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Sản phẩm yêu thích</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="list-group" id="favorites-list">
      <li class="list-group-item text-muted text-center">Chưa có sản phẩm</li>
    </ul>
    <button class="btn btn-outline-danger w-100 mt-3" id="clear-favorites">Xóa tất cả</button>
  </div>
</div>

<!-- ===== OFFCANVAS SEARCH (Mobile) ===== -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasSearch">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Tìm kiếm</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <form action="<?php echo $BASE_PATH; ?>/index.php" method="get">
      <div class="input-group">
        <input type="text" name="query" class="form-control" placeholder="Tìm sản phẩm...">
        <button class="btn btn-primary" type="submit">Tìm</button>
      </div>
    </form>
  </div>
</div>

<!-- ===== BOOTSTRAP 5.3 JS Bundle (includes Popper) ===== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Header scroll effect & Search toggle
document.addEventListener('DOMContentLoaded', function() {
  var header = document.getElementById('siteHeader');
  var searchWrapper = document.getElementById('searchWrapper');
  var searchIconBox = document.getElementById('searchIconBox');
  var searchInput = document.getElementById('searchInput');
  
  // Scroll effect
  window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
      // Collapse search when scroll back to top
      if (searchWrapper) {
        searchWrapper.classList.remove('expanded');
      }
    }
  });
  
  // Toggle search expand (only when scrolled)
  if (searchIconBox) {
    searchIconBox.addEventListener('click', function(e) {
      if (header.classList.contains('scrolled')) {
        e.preventDefault();
        e.stopPropagation();
        searchWrapper.classList.toggle('expanded');
        if (searchWrapper.classList.contains('expanded')) {
          setTimeout(function() {
            searchInput.focus();
          }, 100);
        }
      }
    });
  }
  
  // Close search when clicking outside
  document.addEventListener('click', function(e) {
    if (searchWrapper && header.classList.contains('scrolled')) {
      if (!searchWrapper.contains(e.target)) {
        searchWrapper.classList.remove('expanded');
      }
    }
  });
  
  // ===== FAVORITES FUNCTIONALITY =====
  var favoritesList = document.getElementById('favorites-list');
  var favoritesCount = document.getElementById('favorites-count');
  var clearFavoritesBtn = document.getElementById('clear-favorites');
  var basePath = '<?php echo $BASE_PATH; ?>';
  
  // Load favorites from database
  function loadFavorites() {
    fetch(basePath + '/favorites/get_favorites.php', { credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (!data || data.status !== 'success') return;
        
        var favorites = data.favorites || [];
        
        // Update count badge
        if (favorites.length > 0) {
          favoritesCount.textContent = favorites.length;
          favoritesCount.classList.remove('d-none');
        } else {
          favoritesCount.classList.add('d-none');
        }
        
        // Render list
        if (favorites.length === 0) {
          favoritesList.innerHTML = '<li class="list-group-item text-muted text-center py-4"><i class="fa-regular fa-heart fa-2x mb-2 d-block"></i>Chưa có sản phẩm yêu thích</li>';
          clearFavoritesBtn.style.display = 'none';
          return;
        }
        
        clearFavoritesBtn.style.display = 'block';
        var html = '';
        favorites.forEach(function(item) {
          var priceNum = parseFloat(item.price) || 0;
          var priceText = priceNum > 0 
            ? priceNum.toLocaleString('vi-VN') + ' đ'
            : 'Thỏa thuận';
          
          // Xử lý đường dẫn ảnh
          var imgSrc = item.image || 'images/default-product.jpg';
          // Nếu chưa có basePath thì thêm vào
          if (imgSrc.indexOf('http') !== 0 && imgSrc.indexOf(basePath) !== 0) {
            imgSrc = basePath + '/' + imgSrc;
          }
          
          html += '<li class="list-group-item d-flex align-items-center gap-3 favorite-item" data-product-id="' + item.product_id + '">' +
            '<a href="' + basePath + '/product.php?id=' + item.product_id + '" class="flex-shrink-0">' +
            '<img src="' + imgSrc + '" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:8px;border:1px solid #eee;" onerror="this.src=\'' + basePath + '/images/default-product.jpg\'">' +
            '</a>' +
            '<div class="flex-grow-1 min-width-0">' +
            '<a href="' + basePath + '/product.php?id=' + item.product_id + '" class="text-dark text-decoration-none">' +
            '<h6 class="mb-1 text-truncate" style="font-size:14px;font-weight:600;">' + (item.title || 'Sản phẩm').replace(/</g, '&lt;') + '</h6>' +
            '</a>' +
            '<span style="font-size:15px;font-weight:700;color:#e53935;">' + priceText + '</span>' +
            '</div>' +
            '<button type="button" class="btn btn-sm btn-outline-danger remove-favorite-btn" data-product-id="' + item.product_id + '" title="Xóa" style="border-radius:50%;width:32px;height:32px;padding:0;display:flex;align-items:center;justify-content:center;">' +
            '<i class="fa-solid fa-times"></i>' +
            '</button>' +
            '</li>';
        });
        favoritesList.innerHTML = html;
      })
      .catch(function() {});
  }
  
  // Initial load
  loadFavorites();
  
  // Expose refresh function globally
  window.refreshFavoritesList = loadFavorites;
  
  // Remove single favorite
  favoritesList.addEventListener('click', function(e) {
    var btn = e.target.closest('.remove-favorite-btn');
    if (!btn) return;
    
    e.preventDefault();
    var productId = btn.getAttribute('data-product-id');
    
    var formData = new FormData();
    formData.append('product_id', productId);
    
    fetch(basePath + '/favorites/handle_favorite.php', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data && data.status === 'success') {
        loadFavorites();
        // Update card on main page if exists
        var card = document.querySelector('.product-card[data-product-id="' + productId + '"]');
        if (card) {
          var heartBtn = card.querySelector('.btn-wishlist');
          if (heartBtn) heartBtn.classList.remove('favorited');
        }
      }
    });
  });
  
  // Clear all favorites
  if (clearFavoritesBtn) {
    clearFavoritesBtn.addEventListener('click', function() {
      if (!confirm('Bạn có chắc muốn xóa tất cả sản phẩm yêu thích?')) return;
      
      fetch(basePath + '/favorites/clear_favorites.php', {
        method: 'POST',
        credentials: 'same-origin'
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data && data.status === 'success') {
          loadFavorites();
          // Remove all favorited classes on main page
          document.querySelectorAll('.btn-wishlist.favorited').forEach(function(btn) {
            btn.classList.remove('favorited');
          });
        }
      });
    });
  }
  
  // ========== MESSAGE NOTIFICATIONS ==========
  var messagesCountBadge = document.getElementById('messages-count');
  var lastUnreadCount = 0;
  var lastNotifiedMsgTime = localStorage.getItem('lastNotifiedMsgTime') || '';
  
  function checkNewMessages() {
    fetch(basePath + '/api/get_unread_count.php', { credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (!data || data.status !== 'success') return;
        
        var count = data.count || 0;
        
        // Update badge
        if (messagesCountBadge) {
          if (count > 0) {
            messagesCountBadge.textContent = count > 99 ? '99+' : count;
            messagesCountBadge.classList.remove('d-none');
          } else {
            messagesCountBadge.classList.add('d-none');
          }
        }
        
        // Show notification if new message
        if (data.latest && count > lastUnreadCount) {
          var msgTime = data.latest.created_at || '';
          if (msgTime && msgTime !== lastNotifiedMsgTime) {
            showMessageNotification(data.latest);
            lastNotifiedMsgTime = msgTime;
            localStorage.setItem('lastNotifiedMsgTime', msgTime);
          }
        }
        
        lastUnreadCount = count;
      })
      .catch(function() {});
  }
  
  function showMessageNotification(msg) {
    // Remove old notification if exists
    var oldNotif = document.querySelector('.msg-notification');
    if (oldNotif) oldNotif.remove();
    
    // Create notification element
    var notif = document.createElement('div');
    notif.className = 'msg-notification';
    notif.innerHTML = 
      '<div class="msg-notif-icon"><i class="fa-solid fa-comment-dots"></i></div>' +
      '<div class="msg-notif-content">' +
        '<div class="msg-notif-title">' + escapeHtml(msg.sender_name) + '</div>' +
        '<div class="msg-notif-text">' + escapeHtml(msg.message) + '</div>' +
        '<div class="msg-notif-product"><i class="fa-solid fa-box fa-xs me-1"></i>' + escapeHtml(msg.product_title) + '</div>' +
      '</div>' +
      '<button class="msg-notif-close" onclick="this.parentElement.remove()">&times;</button>';
    
    // Notification click - go to messages
    notif.addEventListener('click', function(e) {
      if (e.target.classList.contains('msg-notif-close')) return;
      window.location.href = basePath + '/user/messages.php?product_id=' + msg.product_id + '&user_id=' + msg.other_user_id;
    });
    
    document.body.appendChild(notif);
    
    // Play sound (optional)
    try {
      var audio = new Audio('data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA//tQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAABhgC7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7//////////////////////////////////////////////////////////////////8AAAAATGF2YzU4LjEzAAAAAAAAAAAAAAAAJAAAAAAAAAAAAYbFX5PXAAAAAAAAAAAAAAAAAAAAAP/7UMQAA8AAADSAAAAAAgAABpAAAAEQAAADKAAAAAQAAAEgAAAD//7UMQGg8AAADSAAAAACAAAmkAAAAX//7UMQMg8AAADSAAAAACAAAmkAAAAX//7UMQQg8AAADSAAAAACAAAmkAAAAX//7UMQVg8AAADSAAAAACAAAmkAAAAX//7UMQag8AAADSAAAAACAAAmkAAAAX//7UMQfg8AAADSAAAAACAAAmkAAAAX//7UMQkg8AAADSAAAAACAAAmkAAAAX');
      audio.volume = 0.5;
      audio.play();
    } catch(e) {}
    
    // Auto hide after 5 seconds
    setTimeout(function() {
      if (notif.parentElement) {
        notif.style.animation = 'slideOutRight 0.3s ease forwards';
        setTimeout(function() { notif.remove(); }, 300);
      }
    }, 5000);
  }
  
  function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }
  
  // Check messages every 10 seconds
  if (messagesCountBadge) {
    checkNewMessages();
    setInterval(checkNewMessages, 10000);
  }
});
</script>

<!-- Message Notification Styles -->
<style>
.msg-notification {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 340px;
  max-width: calc(100vw - 40px);
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 5px 30px rgba(0,0,0,0.2);
  display: flex;
  align-items: flex-start;
  padding: 16px;
  z-index: 9999;
  cursor: pointer;
  animation: slideInRight 0.3s ease;
  border-left: 4px solid #0084ff;
}

@keyframes slideInRight {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOutRight {
  from { transform: translateX(0); opacity: 1; }
  to { transform: translateX(100%); opacity: 0; }
}

.msg-notif-icon {
  width: 44px;
  height: 44px;
  background: linear-gradient(135deg, #0084ff 0%, #0066cc 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 18px;
  flex-shrink: 0;
  margin-right: 12px;
}

.msg-notif-content {
  flex: 1;
  min-width: 0;
}

.msg-notif-title {
  font-weight: 600;
  color: #050505;
  font-size: 14px;
  margin-bottom: 4px;
}

.msg-notif-text {
  color: #65676b;
  font-size: 13px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 4px;
}

.msg-notif-product {
  font-size: 11px;
  color: #0084ff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.msg-notif-close {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 24px;
  height: 24px;
  border: none;
  background: #f0f2f5;
  border-radius: 50%;
  font-size: 16px;
  color: #65676b;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.msg-notif-close:hover {
  background: #e4e6eb;
}

.msg-notification:hover {
  box-shadow: 0 8px 40px rgba(0,0,0,0.25);
}
</style>