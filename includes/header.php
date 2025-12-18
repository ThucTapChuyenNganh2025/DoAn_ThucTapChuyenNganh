<?php
// Header include - used by pages under project root and subfolders.
// Adjust BASE_PATH if you serve project under a subpath.
$BASE_PATH = '/DoAn_ThucTapChuyenNganh';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="format-detection" content="telephone=no">
  <meta name="author" content="Nhóm phát triển">
  <title>Chợ Điện Tử Online</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-brands/css/uicons-brands.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" type="text/css" href="<?php echo $BASE_PATH; ?>/css/vendor.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $BASE_PATH; ?>/style.css">
  <link rel="stylesheet" type="text/css" href="<?php echo $BASE_PATH; ?>/css/user.css">

  <style>
    .main-logo .brand { font-size: 24px; font-weight: 900; color: #ff9f43; text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 8px; }
    .main-logo .brand i { font-size: 20px; }

    /* Header scroll effect */
    header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      width: 100%;
      transition: all 0.3s ease;
      z-index: 1030;
      background: white;
    }

    header.header-scrolled {
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(10px);
    }

    header.header-scrolled .py-3 {
      padding-top: 0.5rem !important;
      padding-bottom: 0.5rem !important;
    }

    /* Hide elements when scrolled */
    header.header-scrolled .support-box {
      display: none;
    }

    header.header-scrolled .search-bar {
      display: none;
    }

    /* When scrolled: hide logo, only keep action icons */
    header.header-scrolled .main-logo {
      display: none;
    }

    /* Icon size adjustments */
    header.header-scrolled .header-action-icon {
      font-size: 16px;
    }

    /* Smooth transitions */
    header *,
    header *::before,
    header *::after {
      transition: all 0.3s ease;
    }

    /* Đẩy nội dung xuống dưới để không bị header che */
    main {
      padding-top: 90px;
    }
  </style>
</head>

<body>
  <?php
  if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
  }
  $username = isset($_SESSION['user_name']) ? htmlspecialchars(trim($_SESSION['user_name']), ENT_QUOTES, 'UTF-8') : null;
  ?>

  <!-- Header (matches index.php design) -->
  <header>
    <div class="container-fluid">
      <div class="row py-3 border-bottom align-items-center">

        <!-- LEFT: logo -->
        <div class="col-sm-4 col-lg-3 text-center text-sm-start">
          <div class="main-logo">
            <a href="<?php echo $BASE_PATH; ?>/index.php" class="logo-box">
              <i class="fa-solid fa-bolt"></i>
              <span class="logo-text">CHỢ ĐIỆN TỬ</span>
            </a>
          </div>
        </div>

        <!-- CENTER: search -->
        <div class="col-sm-6 offset-sm-2 offset-md-0 col-lg-5 d-none d-lg-block">
          <div class="search-bar row bg-light p-2 my-2 rounded-4 align-items-center">
            <div class="col-md-4 d-none d-md-block">
              <div class="custom-dropdown">
                <div class="dropdown-selected">Danh mục</div>
                <ul class="dropdown-list">
                  <li>Điện tử</li>
                  <li>Thời trang</li>
                  <li>Đồ gia dụng</li>
                  <li>Sách & Văn phòng</li>
                  <li>Đồ chơi & Trẻ em</li>
                </ul>
              </div>
            </div>
            <div class="col-12 col-md-8 d-flex">
              <form id="search-form" class="flex-grow-1 d-flex m-0" action="<?php echo $BASE_PATH; ?>/index.php" method="get">
                <input type="hidden" name="category" value="">
                <input type="text" class="form-control border-0 bg-light" placeholder="Tìm sản phẩm, dịch vụ..." name="query">
                <button class="btn btn-primary" type="submit">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z" />
                  </svg>
                </button>
              </form>
            </div>
          </div>
        </div>

        <!-- RIGHT: greeting and actions -->
        <div class="col-sm-8 col-lg-4 d-flex justify-content-end gap-3 align-items-center mt-4 mt-sm-0">
          <div class="support-box text-end d-none d-xl-block">
            <span class="fs-6 text-muted">Xin chào</span>
            <h5 class="mb-0" id="user-name"><?php echo $username ?? 'Khách'; ?></h5>
          </div>

          <!-- Icons -->
          <ul class="d-flex list-unstyled m-0 gap-2 position-relative header-icons-list">
            <!-- User Dropdown -->
            <li class="dropdown">
              <?php if ($username): ?>
                <a href="#" id="user-action" class="rounded-circle bg-light p-2" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
                  <i class="fa-solid fa-user header-action-icon"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end mt-2 shadow-sm">
                  <li><a class="dropdown-item" href="<?php echo $BASE_PATH; ?>/user/profile.php">Hồ sơ</a></li>
                  <li><a class="dropdown-item" href="<?php echo $BASE_PATH; ?>/user/dangxuat.php">Đăng xuất</a></li>
                </ul>
              <?php else: ?>
                <a href="<?php echo $BASE_PATH; ?>/user/dangnhap.php?next=/user/user_dangtin.php" id="user-action" class="rounded-circle bg-light p-2" title="Đăng nhập">
                  <i class="fa-solid fa-user header-action-icon"></i>
                </a>
              <?php endif; ?>
            </li>

            <!-- Favorites / Wishlist -->
            <li class="position-relative">
              <a href="#" id="open-favorites" class="rounded-circle bg-light p-2" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFavorites" title="Yêu thích">
                <i class="fa-solid fa-heart header-action-icon"></i>
                <span id="favorites-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">0</span>
              </a>
            </li>
          </ul>
        </div>

      </div>
    </div>
  </header>
  <!-- End header -->

  <!-- Offcanvas Favorites Sidebar -->
  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasFavorites" aria-labelledby="offcanvasFavoritesLabel">
    <div class="offcanvas-header justify-content-between">
      <h5 class="offcanvas-title text-warning" id="offcanvasFavoritesLabel"><i class="fa-solid fa-heart"></i> Sản phẩm yêu thích</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
    </div>
    <div class="offcanvas-body">
      <h6 class="d-flex justify-content-between align-items-center mb-3">
        <span>Sản phẩm yêu thích</span>
        <span class="badge bg-warning rounded-pill" id="favorites-count-badge">0</span>
      </h6>
      <ul class="list-group mb-3" id="favorites-list">
        <li class="list-group-item text-center text-muted">Chưa có sản phẩm</li>
      </ul>
      <button class="w-100 btn btn-outline-danger" id="clear-favorites" type="button">Xóa tất cả</button>
    </div>
  </div>

  <main>

<script>
// Load and display favorites from API when the favorites sidebar opens
document.addEventListener('DOMContentLoaded', function() {
  const favoritesOffcanvas = document.getElementById('offcanvasFavorites');
  const favoritesList = document.getElementById('favorites-list');
  const favoritesCountBadge = document.getElementById('favorites-count-badge');
  const favoritesCount = document.getElementById('favorites-count');
  const clearFavoritesBtn = document.getElementById('clear-favorites');
  const BASE_PATH = '<?php echo $BASE_PATH; ?>';

  // Load favorites from API
  function loadFavorites() {
    fetch(BASE_PATH + '/favorites/get_favorites.php')
      .then(response => response.json())
      .then(data => {
        if (data.items && data.items.length > 0) {
          let html = '';
          data.items.forEach(item => {
            const imageUrl = item.image || BASE_PATH + '/uploads/placeholder.png';
            html += `
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2 align-items-center flex-grow-1">
                  <img src="${imageUrl}" alt="${item.title}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                  <div style="flex: 1;">
                    <p class="mb-1 fw-bold" style="font-size: 14px;">${item.title}</p>
                    <p class="mb-0 text-danger fw-bold">${item.price} đ</p>
                  </div>
                </div>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFavorite('${item.favorite_id}')">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </li>
            `;
          });
          favoritesList.innerHTML = html;
          favoritesCountBadge.textContent = data.items.length;
          favoritesCount.textContent = data.items.length;
          if (data.items.length > 0) {
            favoritesCount.classList.remove('d-none');
          }
        } else {
          favoritesList.innerHTML = '<li class="list-group-item text-center text-muted">Chưa có sản phẩm</li>';
          favoritesCountBadge.textContent = '0';
          favoritesCount.classList.add('d-none');
        }
      })
      .catch(error => console.error('Error loading favorites:', error));
  }

  // Load favorites when offcanvas is shown
  if (favoritesOffcanvas) {
    favoritesOffcanvas.addEventListener('show.bs.offcanvas', loadFavorites);
  }

  // Remove individual favorite
  window.removeFavorite = function(favoriteId) {
    fetch(BASE_PATH + '/favorites/handle_favorite.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'favorite_id=' + favoriteId + '&action=remove'
    })
    .then(() => loadFavorites())
    .catch(error => console.error('Error removing favorite:', error));
  };

  // Clear all favorites
  if (clearFavoritesBtn) {
    clearFavoritesBtn.addEventListener('click', function() {
      if (confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm yêu thích?')) {
        fetch(BASE_PATH + '/favorites/handle_favorite.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'action=clear_all'
        })
        .then(() => loadFavorites())
        .catch(error => console.error('Error clearing favorites:', error));
      }
    });
  }

  // Load favorites count on page load
  loadFavorites();

  // Header scroll effect
  let scrollTimeout;
  const header = document.querySelector('header');
  const scrollThreshold = 100; // Khoảng cách scroll để kích hoạt hiệu ứng

  function handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    if (scrollTop > scrollThreshold) {
      // Scroll xuống - thu gọn header
      header.classList.add('header-scrolled');
    } else {
      // Ở đầu trang - mở rộng header
      header.classList.remove('header-scrolled');
    }
  }

  // Debounce scroll event for better performance
  window.addEventListener('scroll', function() {
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(handleScroll, 10);
  });
});
</script>
