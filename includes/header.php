<?php
// Header include - used by pages under project root and subfolders.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$BASE_PATH = '/DoAn_ThucTapChuyenNganh';

// Kết nối database
require_once __DIR__ . '/../config/connect.php';

// Lấy tên người dùng
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

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
  
  <link rel="stylesheet" href="<?php echo $BASE_PATH; ?>/css/vendor.css">
  <link rel="stylesheet" href="<?php echo $BASE_PATH; ?>/style.css">
  
  <script src="<?php echo $BASE_PATH; ?>/js/toast.js"></script>
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
    .site-logo .logo-text {
      letter-spacing: 1px;
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
      /* QUAN TRỌNG: Để hiển thị dropdown tràn ra ngoài, phải bỏ hidden */
      overflow: visible; 
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
    .site-header.scrolled .header-search .btn-search,
    .site-header.scrolled .header-cat-wrapper {
      width: 0;
      padding: 0;
      opacity: 0;
      pointer-events: none;
      overflow: hidden;
      min-width: 0;
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
    .site-header.scrolled .search-wrapper.expanded .header-cat-wrapper {
      width: auto;
      opacity: 1;
      pointer-events: auto;
      min-width: 100px;
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
    
    /* Đăng tin button - Icon tròn màu vàng */
    .btn-post-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
      border-radius: 50%;
      display: flex !important;
      align-items: center;
      justify-content: center;
      color: #1a1a2e !important;
      text-decoration: none !important;
      font-size: 18px;
      font-weight: bold;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(246, 194, 62, 0.4);
    }
    .btn-post-icon:hover {
      transform: scale(1.1) rotate(90deg);
      box-shadow: 0 5px 20px rgba(246, 194, 62, 0.6);
      color: #1a1a2e !important;
    }
    
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
    .site-header.scrolled .btn-post-icon { width: 36px; height: 36px; font-size: 16px; }
    
    /* ========================================================== */
    /* CUSTOM HEADER CATEGORY DROPDOWN (Like Mobile) */
    /* ========================================================== */
    .header-cat-wrapper {
      position: relative;
      margin-right: 5px;
    }
    
    .header-cat-toggle {
      border: none;
      outline: none;
      background: transparent;
      padding: 6px 10px;
      font-size: 14px;
      color: #333;
      cursor: pointer;
      border-left: 1px solid #ddd;
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-width: 140px; /* Độ rộng tối thiểu cho đẹp */
      max-width: 180px;
      gap: 5px;
    }
    
    .header-cat-toggle:focus {
      outline: none;
    }
    
    .header-cat-toggle span {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      flex: 1;
      text-align: left;
    }
    
    .header-cat-dropdown {
      display: none;
      position: absolute;
      top: 100%;
      left: 0; /* Căn trái hoặc phải tùy ý */
      right: 0;
      min-width: 200px;
      background: #fff;
      border: 1px solid #eee;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
      margin-top: 10px;
      max-height: 300px;
      overflow-y: auto;
      z-index: 9999;
      padding: 5px 0;
    }
    
    /* Show dropdown */
    .header-cat-dropdown.show {
      display: block;
      animation: fadeIn 0.2s ease;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-5px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .header-cat-item {
      padding: 10px 15px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      color: #333;
      transition: background 0.2s;
    }
    
    .header-cat-item:hover {
      background: #f8f9fa;
    }
    
    .header-cat-item.active {
      background: #fff8e1;
      color: #dda20a;
      font-weight: 600;
    }
    
    .header-cat-item i {
      opacity: 0;
      font-size: 12px;
      width: 16px;
    }
    
    .header-cat-item.active i {
      opacity: 1;
    }
    
    /* ===== RESPONSIVE STYLES ===== */
    
    /* Mobile Toggle Button */
    .mobile-menu-toggle {
      display: none;
      width: 40px;
      height: 40px;
      background: rgba(255,255,255,0.1);
      border: none;
      border-radius: 8px;
      color: #fff;
      font-size: 18px;
      cursor: pointer;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
    }
    .mobile-menu-toggle:hover {
      background: rgba(255,255,255,0.2);
    }
    
    /* Mobile Search Button */
    .mobile-search-btn {
      display: none;
      width: 40px;
      height: 40px;
      background: rgba(255,255,255,0.1);
      border: none;
      border-radius: 50%;
      color: #fff;
      font-size: 16px;
      cursor: pointer;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
    }
    .mobile-search-btn:hover {
      background: #f6c23e;
      color: #1a1a2e;
    }
    
    /* Extra Large (≥1200px) */
    @media (min-width: 1200px) {
      .search-wrapper {
        max-width: 650px;
      }
    }
    
    /* Large & Tablet (992px - 1199px) */
    @media (max-width: 1199.98px) {
      .user-greeting { display: none !important; }
      .search-wrapper {
        max-width: 100%;
        margin: 0 15px;
      }
      .header-search input { font-size: 13px; }
      
      /* Giữ nguyên dropdown đẹp nhưng co nhỏ lại chút */
      .header-cat-toggle {
        min-width: 120px;
        max-width: 140px;
        font-size: 13px;
        padding: 6px 8px;
      }
    }
    
    /* Medium - Tablet Portrait (768px - 991px) */
    @media (max-width: 991.98px) {
      body { padding-top: 70px; }
      .site-header { padding: 10px 0; }
      
      .search-wrapper { 
        display: flex !important; 
        max-width: 100%;
        margin: 0 10px;
      }
      .mobile-search-btn { display: none; }
      
      /* Trên Tablet dọc, ẩn text "Tất cả danh mục" để tiết kiệm chỗ, chỉ hiện icon hoặc text ngắn */
      .header-cat-toggle {
        min-width: 90px;
        font-size: 13px;
      }
      
      .site-logo .logo-text { display: none; } 
      
      .btn-post-icon { width: 36px; height: 36px; font-size: 16px; }
      .header-icon { width: 38px; height: 38px; }
      .gap-2 { gap: 0.5rem !important; }
    }
    
    /* Small (Mobile) < 768px */
    @media (max-width: 767.98px) {
      body { padding-top: 65px; }
      .site-header { padding: 8px 0; }
      
      /* Trên Mobile thì ẩn search bar đi */
      .search-wrapper { display: none !important; }
      .mobile-search-btn { display: flex; }
      
      .site-logo .logo-text { display: block; }
      
      .container-fluid { padding-left: 12px; padding-right: 12px; }
      .btn-post-icon { width: 34px; height: 34px; font-size: 15px; }
      .site-logo { font-size: 16px; gap: 8px; }
      .site-logo .logo-icon { width: 30px; height: 30px; font-size: 13px; }
      
      .mobile-menu-toggle { display: flex; }
      .header-icon { width: 36px; height: 36px; font-size: 14px; }
      .gap-2 { gap: 0.4rem !important; }
      
      #messages-count, #favorites-count {
        top: -4px; right: -6px; font-size: 10px; min-width: 18px; height: 18px; line-height: 18px; padding: 0 5px;
      }
    }
    
    /* Extra Small (< 576px) */
    @media (max-width: 575.98px) {
      body { padding-top: 60px; }
      .site-header { padding: 6px 0; }
      .container-fluid { padding-left: 10px; padding-right: 10px; }
      .site-logo { font-size: 14px; gap: 6px; }
      .site-logo .logo-icon { width: 28px; height: 28px; font-size: 12px; }
      .btn-post-icon { width: 32px; height: 32px; font-size: 14px; }
      .header-icon { width: 32px; height: 32px; font-size: 13px; }
      .mobile-search-btn { width: 32px; height: 32px; font-size: 13px; }
      .gap-2 { gap: 0.35rem !important; }
      
      .header-dropdown .dropdown-menu { min-width: 180px !important; margin-top: 8px !important; }
      .header-dropdown .dropdown-menu .dropdown-item { padding: 8px 15px !important; font-size: 13px !important; }
    }
    
    /* Mobile Search Offcanvas styles */
    #offcanvasSearch .offcanvas-body { padding-top: 20px; }
    #offcanvasSearch .form-control { padding: 12px 15px; font-size: 16px; border-radius: 10px 0 0 10px; }
    #offcanvasSearch .btn { padding: 12px 20px; font-weight: 600; border-radius: 0 10px 10px 0; }
    
    /* Mobile Category CSS */
    .category-mobile-wrapper { position: relative; }
    .category-mobile-toggle { width: 100%; padding: 12px 15px; font-size: 15px; border: 1px solid #dee2e6; border-radius: 10px; background: #fff; display: flex; justify-content: space-between; align-items: center; cursor: pointer; color: #333; transition: all 0.2s; }
    .category-mobile-toggle:hover { border-color: #f6c23e; outline: none; }
    .category-mobile-toggle i { transition: transform 0.2s; }
    .category-mobile-toggle.open i { transform: rotate(180deg); }
    .category-mobile-list { display: none; margin-top: 8px; border: 1px solid #dee2e6; border-radius: 10px; background: #fff; overflow: hidden; max-height: 200px; overflow-y: auto; }
    .category-mobile-list.show { display: block; }
    .category-mobile-item { padding: 12px 15px; cursor: pointer; border-bottom: 1px solid #f0f0f0; transition: background 0.2s; display: flex; align-items: center; gap: 8px; }
    .category-mobile-item:last-child { border-bottom: none; }
    .category-mobile-item:hover { background: #f8f9fa; }
    .category-mobile-item.active { background: #fff8e1; color: #dda20a; font-weight: 600; }
    .category-mobile-item i { opacity: 0; font-size: 12px; }
    .category-mobile-item.active i { opacity: 1; }
    
    /* Favorites Offcanvas responsive */
    @media (max-width: 575.98px) {
      .offcanvas-end { width: 85% !important; max-width: 320px; }
      .offcanvas-header { padding: 15px; }
      .offcanvas-body { padding: 15px; }
    }
  </style>
</head>
<body>

<header class="site-header" id="siteHeader">
  <div class="container-fluid">
    <div class="row align-items-center justify-content-between">
      
      <div class="col-auto">
        <a href="<?php echo $BASE_PATH; ?>/index.php" class="site-logo">
          <span class="logo-icon"><i class="fa-solid fa-bolt"></i></span>
          <span class="logo-text">CHỢ ĐIỆN TỬ</span>
        </a>
      </div>
      
      <div class="col d-none d-lg-flex justify-content-center">
        <div class="search-wrapper" id="searchWrapper">
          <form action="<?php echo $BASE_PATH; ?>/index.php" method="get" class="header-search" id="headerSearchForm">
            <div class="search-icon-box" id="searchIconBox">
              <i class="fa-solid fa-search"></i>
            </div>
            <input type="text" name="query" placeholder="Tìm sản phẩm, dịch vụ..." id="searchInput">
            
            <input type="hidden" name="category" id="headerCatValue" value="">
            <div class="header-cat-wrapper position-relative">
                <button type="button" class="header-cat-toggle" id="headerCatToggle">
                    <span id="headerCatLabel">Tất cả danh mục</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
                <div class="header-cat-dropdown" id="headerCatDropdown">
                    <div class="header-cat-item active" data-value="" data-label="Tất cả danh mục">
                        <i class="fa-solid fa-check"></i> Tất cả danh mục
                    </div>
                    <?php foreach ($categories as $cat): ?>
                    <div class="header-cat-item" data-value="<?php echo (int)$cat['id']; ?>" data-label="<?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="fa-solid fa-check"></i> <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <button type="submit" class="btn-search">Tìm kiếm</button>
          </form>
        </div>
      </div>
      
      <div class="col-auto">
        <div class="d-flex align-items-center gap-2">
          
          <button class="mobile-search-btn" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSearch" title="Tìm kiếm">
            <i class="fa-solid fa-search"></i>
          </button>
          
          <a href="<?php echo $BASE_PATH; ?>/user/<?php echo $display_username ? 'user_dangtin.php' : 'dangnhap.php?next=user_dangtin.php'; ?>" class="btn-post-icon" title="Đăng tin">
            <i class="fa-solid fa-plus"></i>
          </a>
          
          <?php if ($display_username): ?>
          <a href="<?php echo $BASE_PATH; ?>/user/messages.php" class="header-icon position-relative" title="Tin nhắn" id="messagesIcon">
            <i class="fa-regular fa-comment-dots"></i>
            <span id="messages-count" class="position-absolute badge rounded-pill d-none" style="font-size:10px;font-weight:700;min-width:18px;height:18px;line-height:18px;padding:0 5px;top:-5px;right:-6px;background:#0084ff;color:#fff;border:2px solid #1a1a2e;box-shadow:0 2px 6px rgba(0,0,0,0.3);">0</span>
          </a>
          <?php endif; ?>
          
          <a href="#" class="header-icon position-relative" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFavorites" title="Yêu thích">
            <i class="fa-regular fa-heart"></i>
            <span id="favorites-count" class="position-absolute badge rounded-pill d-none" style="font-size:10px;font-weight:700;min-width:18px;height:18px;line-height:18px;padding:0 5px;top:-5px;right:-6px;background:#ff4757;color:#fff;border:2px solid #1a1a2e;box-shadow:0 2px 6px rgba(0,0,0,0.3);">0</span>
          </a>
          
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
          
        </div>
      </div>
      
    </div>
  </div>
</header>

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

<div class="offcanvas offcanvas-top" tabindex="-1" id="offcanvasSearch" style="height: auto;">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title"><i class="fa-solid fa-search me-2"></i>Tìm kiếm</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body" style="padding-bottom: 20px;">
    <form action="<?php echo $BASE_PATH; ?>/index.php" method="get">
      <div class="input-group mb-3">
        <input type="text" name="query" class="form-control" placeholder="Tìm sản phẩm, dịch vụ..." style="padding: 12px 15px; font-size: 16px;">
        <button class="btn btn-warning" type="submit" style="padding: 12px 20px; font-weight: 600;">
          <i class="fa-solid fa-search"></i> Tìm
        </button>
      </div>
      <input type="hidden" name="category" id="mobileCategoryValue" value="">
      <div class="category-mobile-wrapper">
        <button type="button" class="category-mobile-toggle" id="categoryMobileToggle">
          <span id="categoryMobileLabel">Tất cả danh mục</span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>
        <div class="category-mobile-list" id="categoryMobileList">
          <div class="category-mobile-item active" data-value="" data-label="Tất cả danh mục">
            <i class="fa-solid fa-check"></i> Tất cả danh mục
          </div>
          <?php foreach ($categories as $cat): ?>
          <div class="category-mobile-item" data-value="<?php echo (int)$cat['id']; ?>" data-label="<?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>">
            <i class="fa-solid fa-check"></i> <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </form>
  </div>
</div>

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
      if (searchWrapper) {
        searchWrapper.classList.remove('expanded');
      }
    }
  });
  
  // Toggle search expand
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
  
  // ===== HEADER CATEGORY DROPDOWN (NEW) =====
  var headerCatToggle = document.getElementById('headerCatToggle');
  var headerCatDropdown = document.getElementById('headerCatDropdown');
  var headerCatValue = document.getElementById('headerCatValue');
  var headerCatLabel = document.getElementById('headerCatLabel');
  var headerCatItems = document.querySelectorAll('.header-cat-item');
  
  if (headerCatToggle) {
    // Toggle dropdown
    headerCatToggle.addEventListener('click', function(e) {
      e.stopPropagation();
      headerCatDropdown.classList.toggle('show');
    });
    
    // Select item
    headerCatItems.forEach(function(item) {
      item.addEventListener('click', function(e) {
        e.stopPropagation();
        var value = this.getAttribute('data-value');
        var label = this.getAttribute('data-label');
        
        // Update hidden input
        headerCatValue.value = value;
        // Update label text
        headerCatLabel.textContent = label;
        
        // Handle active class
        headerCatItems.forEach(function(i) { i.classList.remove('active'); });
        this.classList.add('active');
        
        // Close dropdown
        headerCatDropdown.classList.remove('show');
      });
    });
    
    // Close when clicking outside
    document.addEventListener('click', function(e) {
      if (headerCatToggle && !headerCatToggle.contains(e.target)) {
        headerCatDropdown.classList.remove('show');
      }
    });
  }
  
  // ===== MOBILE CATEGORY SELECTOR =====
  var categoryToggle = document.getElementById('categoryMobileToggle');
  var categoryList = document.getElementById('categoryMobileList');
  var categoryValue = document.getElementById('mobileCategoryValue');
  var categoryLabel = document.getElementById('categoryMobileLabel');
  var categoryItems = document.querySelectorAll('.category-mobile-item');
  
  if (categoryToggle) {
    categoryToggle.addEventListener('click', function(e) {
      e.preventDefault();
      categoryToggle.classList.toggle('open');
      categoryList.classList.toggle('show');
    });
    
    categoryItems.forEach(function(item) {
      item.addEventListener('click', function() {
        var value = this.getAttribute('data-value');
        var label = this.getAttribute('data-label');
        
        categoryValue.value = value;
        categoryLabel.textContent = label;
        
        categoryItems.forEach(function(i) { i.classList.remove('active'); });
        this.classList.add('active');
        
        categoryToggle.classList.remove('open');
        categoryList.classList.remove('show');
      });
    });
    
    document.addEventListener('click', function(e) {
      if (categoryToggle && !categoryToggle.contains(e.target) && !categoryList.contains(e.target)) {
        categoryToggle.classList.remove('open');
        categoryList.classList.remove('show');
      }
    });
  }
  
  // ===== FAVORITES FUNCTIONALITY =====
  var favoritesList = document.getElementById('favorites-list');
  var favoritesCount = document.getElementById('favorites-count');
  var clearFavoritesBtn = document.getElementById('clear-favorites');
  var basePath = '<?php echo $BASE_PATH; ?>';
  
  function loadFavorites() {
    fetch(basePath + '/favorites/get_favorites.php', { credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (!data || data.status !== 'success') return;
        var favorites = data.favorites || [];
        
        if (favorites.length > 0) {
          favoritesCount.textContent = favorites.length;
          favoritesCount.classList.remove('d-none');
        } else {
          favoritesCount.classList.add('d-none');
        }
        
        if (favorites.length === 0) {
          favoritesList.innerHTML = '<li class="list-group-item text-muted text-center py-4"><i class="fa-regular fa-heart fa-2x mb-2 d-block"></i>Chưa có sản phẩm yêu thích</li>';
          clearFavoritesBtn.style.display = 'none';
          return;
        }
        
        clearFavoritesBtn.style.display = 'block';
        var html = '';
        favorites.forEach(function(item) {
          var priceNum = parseFloat(item.price) || 0;
          var priceText = priceNum > 0 ? priceNum.toLocaleString('vi-VN') + ' đ' : 'Thỏa thuận';
          var imgSrc = item.image || 'images/default-product.jpg';
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
  
  loadFavorites();
  window.refreshFavoritesList = loadFavorites;
  
  favoritesList.addEventListener('click', function(e) {
    var btn = e.target.closest('.remove-favorite-btn');
    if (!btn) return;
    e.preventDefault();
    var productId = btn.getAttribute('data-product-id');
    var formData = new FormData();
    formData.append('product_id', productId);
    
    fetch(basePath + '/favorites/handle_favorite.php', {
      method: 'POST', body: formData, credentials: 'same-origin'
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (data && data.status === 'success') {
        loadFavorites();
        var card = document.querySelector('.product-card[data-product-id="' + productId + '"]');
        if (card) {
          var heartBtn = card.querySelector('.btn-wishlist');
          if (heartBtn) heartBtn.classList.remove('favorited');
        }
      }
    });
  });
  
  if (clearFavoritesBtn) {
    clearFavoritesBtn.addEventListener('click', function() {
      showConfirm('Bạn có chắc muốn xóa tất cả sản phẩm yêu thích?', function() {
        fetch(basePath + '/favorites/clear_favorites.php', {
          method: 'POST', credentials: 'same-origin'
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
          if (data && data.status === 'success') {
            toastSuccess('\u0110ã xóa tất cả sản phẩm yêu thích!');
            loadFavorites();
            document.querySelectorAll('.btn-wishlist.favorited').forEach(function(btn) {
              btn.classList.remove('favorited');
            });
          }
        });
      }, null, { title: 'Xác nhận xóa', confirmText: 'Xóa tất cả', cancelText: 'Hủy', type: 'danger' });
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
        
        if (messagesCountBadge) {
          if (count > 0) {
            messagesCountBadge.textContent = count > 99 ? '99+' : count;
            messagesCountBadge.classList.remove('d-none');
          } else {
            messagesCountBadge.classList.add('d-none');
          }
        }
        
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
    var oldNotif = document.querySelector('.msg-notification');
    if (oldNotif) oldNotif.remove();
    
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
    
    notif.addEventListener('click', function(e) {
      if (e.target.classList.contains('msg-notif-close')) return;
      window.location.href = basePath + '/user/messages.php?product_id=' + msg.product_id + '&user_id=' + msg.other_user_id;
    });
    
    document.body.appendChild(notif);
    
    try {
      var audio = new Audio('data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA//tQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAABhgC7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7//////////////////////////////////////////////////////////////////8AAAAATGF2YzU4LjEzAAAAAAAAAAAAAAAAJAAAAAAAAAAAAYbFX5PXAAAAAAAAAAAAAAAAAAAAAP/7UMQAA8AAADSAAAAAAgAABpAAAAEQAAADKAAAAAQAAAEgAAAD//7UMQGg8AAADSAAAAACAAAmkAAAAX//7UMQMg8AAADSAAAAACAAAmkAAAAX//7UMQQg8AAADSAAAAACAAAmkAAAAX//7UMQVg8AAADSAAAAACAAAmkAAAAX//7UMQag8AAADSAAAAACAAAmkAAAAX//7UMQfg8AAADSAAAAACAAAmkAAAAX//7UMQkg8AAADSAAAAACAAAmkAAAAX');
      audio.volume = 0.5;
      audio.play();
    } catch(e) {}
    
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
  
  if (messagesCountBadge) {
    checkNewMessages();
    setInterval(checkNewMessages, 10000);
  }
});
</script>

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
@keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
@keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
.msg-notif-icon { width: 44px; height: 44px; background: linear-gradient(135deg, #0084ff 0%, #0066cc 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; flex-shrink: 0; margin-right: 12px; }
.msg-notif-content { flex: 1; min-width: 0; }
.msg-notif-title { font-weight: 600; color: #050505; font-size: 14px; margin-bottom: 4px; }
.msg-notif-text { color: #65676b; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px; }
.msg-notif-product { font-size: 11px; color: #0084ff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.msg-notif-close { position: absolute; top: 8px; right: 8px; width: 24px; height: 24px; border: none; background: #f0f2f5; border-radius: 50%; font-size: 16px; color: #65676b; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.msg-notif-close:hover { background: #e4e6eb; }
.msg-notification:hover { box-shadow: 0 8px 40px rgba(0,0,0,0.25); }
</style>
</body>
</html>