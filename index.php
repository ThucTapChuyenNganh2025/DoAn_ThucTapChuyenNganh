<?php
session_start();
$username = isset($_SESSION['user_name']) ? trim($_SESSION['user_name']) : null;
if ($username !== null) {
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Chợ Điện Tử Online</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="format-detection" content="telephone=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="author" content="Nhóm phát triển">
  <meta name="keywords" content="chợ online, rao vặt, mua bán, sản phẩm">
  <meta name="description" content="Hệ thống chợ điện tử trực tuyến - đăng tin, mua bán sản phẩm dễ dàng">


  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-brands/css/uicons-brands.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


  <link rel="stylesheet" type="text/css" href="css/vendor.css">
  <link rel="stylesheet" type="text/css" href="style.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <style>
    /* Logo style to match admin branding */
    .main-logo .brand { font-size: 24px; font-weight: 900; color: #ff9f43; text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 8px; }
    .main-logo .brand i { font-size: 20px; }
  </style>
</head>

<body>
  <!-- ======================= SVG ICONS BEGIN ======================= -->
  <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
    <defs>
      <symbol xmlns="http://www.w3.org/2000/svg" id="link" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M12 19a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm5 0a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm0-4a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm-5 0a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm7-12h-1V2a1 1 0 0 0-2 0v1H8V2a1 1 0 0 0-2 0v1H5a3 3 0 0 0-3 3v14a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3Zm1 17a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-9h16Zm0-11H4V6a1 1 0 0 1 1-1h1v1a1 1 0 0 0 2 0V5h8v1a1 1 0 0 0 2 0V5h1a1 1 0 0 1 1 1ZM7 15a1 1 0 1 0-1-1a1 1 0 0 0 1 1Zm0 4a1 1 0 1 0-1-1a1 1 0 0 0 1 1Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="arrow-right" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M17.92 11.62a1 1 0 0 0-.21-.33l-5-5a1 1 0 0 0-1.42 1.42l3.3 3.29H7a1 1 0 0 0 0 2h7.59l-3.3 3.29a1 1 0 0 0 0 1.42a1 1 0 0 0 1.42 0l5-5a1 1 0 0 0 .21-.33a1 1 0 0 0 0-.76Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="category" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M19 5.5h-6.28l-.32-1a3 3 0 0 0-2.84-2H5a3 3 0 0 0-3 3v13a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3v-10a3 3 0 0 0-3-3Zm1 13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-13a1 1 0 0 1 1-1h4.56a1 1 0 0 1 .95.68l.54 1.64a1 1 0 0 0 .95.68h7a1 1 0 0 1 1 1Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="calendar" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M19 4h-2V3a1 1 0 0 0-2 0v1H9V3a1 1 0 0 0-2 0v1H5a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h14a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3Zm1 15a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-7h16Zm0-9H4V7a1 1 0 0 1 1-1h2v1a1 1 0 0 0 2 0V6h6v1a1 1 0 0 0 2 0V6h2a1 1 0 0 1 1 1Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="heart" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M20.16 4.61A6.27 6.27 0 0 0 12 4a6.27 6.27 0 0 0-8.16 9.48l7.45 7.45a1 1 0 0 0 1.42 0l7.45-7.45a6.27 6.27 0 0 0 0-8.87Zm-1.41 7.46L12 18.81l-6.75-6.74a4.28 4.28 0 0 1 3-7.3a4.25 4.25 0 0 1 3 1.25a1 1 0 0 0 1.42 0a4.27 4.27 0 0 1 6 6.05Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="plus" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M19 11h-6V5a1 1 0 0 0-2 0v6H5a1 1 0 0 0 0 2h6v6a1 1 0 0 0 2 0v-6h6a1 1 0 0 0 0-2Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="minus" viewBox="0 0 24 24">
        <path fill="currentColor" d="M19 11H5a1 1 0 0 0 0 2h14a1 1 0 0 0 0-2Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="cart" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M8.5 19a1.5 1.5 0 1 0 1.5 1.5A1.5 1.5 0 0 0 8.5 19ZM19 16H7a1 1 0 0 1 0-2h8.491a3.013 3.013 0 0 0 2.885-2.176l1.585-5.55A1 1 0 0 0 19 5H6.74a3.007 3.007 0 0 0-2.82-2H3a1 1 0 0 0 0 2h.921a1.005 1.005 0 0 1 .962.725l.155.545v.005l1.641 5.742A3 3 0 0 0 7 18h12a1 1 0 0 0 0-2Zm-1.326-9l-1.22 4.274a1.005 1.005 0 0 1-.963.726H8.754l-.255-.892L7.326 7ZM16.5 19a1.5 1.5 0 1 0 1.5 1.5a1.5 1.5 0 0 0-1.5-1.5Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="check" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M18.71 7.21a1 1 0 0 0-1.42 0l-7.45 7.46l-3.13-3.14A1 1 0 1 0 5.29 13l3.84 3.84a1 1 0 0 0 1.42 0l8.16-8.16a1 1 0 0 0 0-1.47Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="trash" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M10 18a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1ZM20 6h-4V5a3 3 0 0 0-3-3h-2a3 3 0 0 0-3 3v1H4a1 1 0 0 0 0 2h1v11a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8h1a1 1 0 0 0 0-2ZM10 5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v1h-4Zm7 14a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V8h10Zm-3-1a1 1 0 0 0 1-1v-6a1 1 0 0 0-2 0v6a1 1 0 0 0 1 1Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="star-outline" viewBox="0 0 15 15">
        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
          d="M7.5 9.804L5.337 11l.413-2.533L4 6.674l2.418-.37L7.5 4l1.082 2.304l2.418.37l-1.75 1.793L9.663 11L7.5 9.804Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="star-solid" viewBox="0 0 15 15">
        <path fill="currentColor"
          d="M7.953 3.788a.5.5 0 0 0-.906 0L6.08 5.85l-2.154.33a.5.5 0 0 0-.283.843l1.574 1.613l-.373 2.284a.5.5 0 0 0 .736.518l1.92-1.063l1.921 1.063a.5.5 0 0 0 .736-.519l-.373-2.283l1.574-1.613a.5.5 0 0 0-.283-.844L8.921 5.85l-.968-2.062Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="search" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="user" viewBox="0 0 24 24">
        <path fill="currentColor"
          d="M15.71 12.71a6 6 0 1 0-7.42 0a10 10 0 0 0-6.22 8.18a1 1 0 0 0 2 .22a8 8 0 0 1 15.9 0a1 1 0 0 0 1 .89h.11a1 1 0 0 0 .88-1.1a10 10 0 0 0-6.25-8.19ZM12 12a4 4 0 1 1 4-4a4 4 0 0 1-4 4Z" />
      </symbol>
      <symbol xmlns="http://www.w3.org/2000/svg" id="close" viewBox="0 0 15 15">
        <path fill="currentColor"
          d="M7.953 3.788a.5.5 0 0 0-.906 0L6.08 5.85l-2.154.33a.5.5 0 0 0-.283.843l1.574 1.613l-.373 2.284a.5.5 0 0 0 .736.518l1.92-1.063l1.921 1.063a.5.5 0 0 0 .736-.519l-.373-2.283l1.574-1.613a.5.5 0 0 0-.283-.844L8.921 5.85l-.968-2.062Z" />
      </symbol>
    </defs>
  </svg>
  <!-- ======================= SVG ICONS END ======================= -->

  <!-- ======================= PRELOADER BEGIN ======================= -->
  <div class="preloader-wrapper">
    <div class="preloader"></div>
  </div>
  <!-- ======================= PRELOADER END ========================= -->


  <!-- ======================= OFFCANVAS FAVORITES BEGIN ======================= -->
  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasFavorites"
    aria-labelledby="offcanvasFavoritesLabel">
    <div class="offcanvas-header justify-content-between">
      <h5 class="product-name offcanvas-title text-primary" id="offcanvasFavoritesLabel">Sản phẩm yêu thích</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
    </div>

    <div class="offcanvas-body">
      <div class="order-md-last">

        <h6 class="d-flex justify-content-between align-items-center mb-3">
          <span>Sản phẩm yêu thích</span>
          <span class="badge bg-primary rounded-pill" id="favorites-count-offcanvas">0</span>
        </h6>

        <ul class="list-group mb-3" id="favorites-list">
          <li class="list-group-item text-center text-muted">Chưa có sản phẩm</li>
        </ul>

        <button class="w-100 btn btn-outline-danger" id="clear-favorites" type="button">Xóa tất cả</button>

      </div>
    </div>
  </div>
  <!-- ======================= OFFCANVAS FAVORITES END ========================= -->

  <!-- ======================= OFFCANVAS SEARCH BEGIN ======================= -->
  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasSearch"
    aria-labelledby="offcanvasSearchLabel">

    <div class="offcanvas-header justify-content-between">
      <h5 class="product-name offcanvas-title text-primary" id="offcanvasSearchLabel">Tìm kiếm sản phẩm</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
    </div>

    <div class="offcanvas-body">
      <div class="order-md-last">
        <form role="search" action="index.php" method="get" class="d-flex mt-3 gap-0">
          <input type="hidden" name="category" id="offcanvas-search-category" value="">
          <input class="form-control rounded-start rounded-0 bg-light" type="text" name="query"
            placeholder="Nhập tên sản phẩm cần tìm..." aria-label="Nhập tên sản phẩm cần tìm">
          <button class="btn btn-primary rounded-end rounded-0" type="submit">Tìm kiếm</button>
        </form>
      </div>
    </div>

  </div>
  <!-- ======================= OFFCANVAS SEARCH END ========================= -->

  <!-- ======================= HEADER BEGIN ======================= -->
  <header>
    <div class="container-fluid">
      <div class="row py-3 border-bottom align-items-center">

        <!-- LOGO -->
        <div class="col-sm-4 col-lg-3 text-center text-sm-start">
          <div class="main-logo">
            <a href="/DoAn_ThucTapChuyenNganh/index.php" class="logo-box">
              <i class="fa-solid fa-bolt"></i>
              <span class="logo-text">CHỢ ĐIỆN TỬ</span>
            </a>

          </div>
        </div>

        <!-- SEARCH BAR -->
        <div class="col-sm-6 offset-sm-2 offset-md-0 col-lg-5 d-none d-lg-block">
          <div class="search-bar row bg-light p-2 my-2 rounded-4 align-items-center">
            <input type="hidden" id="categorySelect" value="">
            <!-- Custom Dropdown Categories -->
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

            <!-- Search Input -->
            <div class="col-12 col-md-8 d-flex">
              <form id="search-form" class="flex-grow-1 d-flex m-0">
                <input type="hidden" name="category" value="">
                <input type="text" class="form-control border-0 bg-light" placeholder="Tìm sản phẩm, dịch vụ..."
                  id="searchInput">
                <button class="btn btn-primary" type="submit">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="currentColor"
                      d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z" />
                  </svg>
                </button>
              </form>
            </div>

          </div>
        </div>

        <!-- USER / WISHLIST / CART / SUPPORT -->
        <div class="col-sm-8 col-lg-4 d-flex justify-content-end gap-3 align-items-center mt-4 mt-sm-0">

          <div class="support-box text-end d-none d-xl-block">
            <span class="fs-6 text-muted">Xin chào</span>
            <h5 class="mb-0" id="user-name"><?php echo $username  ?? 'Khách'; ?></h5>
          </div>


          <div class="ms-3 d-none d-lg-block">
            <?php if ($username): ?>
              <a href="user/user_dangtin.php" class="btn btn-warning btn-sm" id="post-ad-btn">Đăng tin</a>
            <?php else: ?>
              <a href="user/dangnhap.php?next=user_dangtin.php" class="btn btn-warning btn-sm" id="post-ad-btn">Đăng tin</a>
            <?php endif; ?>
          </div>

          <!-- Icons -->
          <ul class="d-flex list-unstyled m-0 gap-2 position-relative header-icons-list">
            <!-- User -->
            <li class="dropdown">
              <?php if ($username): ?>
                <a href="#" id="user-action" class="rounded-circle bg-light p-2" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
                  <i class="uicon-user header-action-icon" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24"><use xlink:href="#user"></use></svg></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end mt-2 shadow-sm">
                  <li><a class="dropdown-item" href="user/profile.php">Hồ sơ</a></li>
                  <li><a class="dropdown-item" href="user/dangxuat.php">Đăng xuất</a></li>
                </ul>
              <?php else: ?>
                <a href="user/dangky.php?next=../index.php" id="user-action" class="rounded-circle bg-light p-2" title="Đăng ký / Đăng nhập">
                  <i class="uicon-user header-action-icon" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24"><use xlink:href="#user"></use></svg></i>
                </a>
              <?php endif; ?>
            </li>
            
            <?php if ($username): ?>
            <li class="position-relative" id="header-logout">
              <a href="user/dangxuat.php" id="header-logout-link" class="rounded-circle bg-light p-2" title="Đăng xuất">
                <i class="fi fi-sr-user-logout header-action-icon" aria-hidden="true"></i>
              </a>
            </li>
            <?php endif; ?>

            <!-- Favorites / Wishlist -->
            <li class="position-relative">
              <a href="#" id="open-favorites" class="rounded-circle bg-light p-2">
                <i class="uicon-fav header-action-icon" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24"><use xlink:href="#heart"></use></svg></i>

                <!-- Favorites Count Badge -->
                <span id="favorites-count"
                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                  0
                </span>
              </a>
            </li>


            <!-- Search (mobile) -->
            <li class="d-lg-none">
              <a href="#" class="rounded-circle bg-light p-2" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasSearch" aria-controls="offcanvasSearch">
                <svg width="24" height="24" viewBox="0 0 24 24">
                  <use xlink:href="#search"></use>
                </svg>
              </a>
            </li>
          </ul>

        </div>

      </div>
    </div>

    <!-- NAVIGATION MENU -->
    <div class="container-fluid">
      <div class="row py-3">
        <div class="d-flex justify-content-center justify-content-sm-between align-items-center">

          <nav class="main-menu navbar navbar-expand-lg">

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
              aria-controls="offcanvasNavbar">
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
              aria-labelledby="offcanvasNavbarLabel">

              <div class="offcanvas-header justify-content-center">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
              </div>

              <div class="offcanvas-body">
                <ul
                  class="navbar-nav justify-content-end menu-list list-unstyled d-flex flex-column flex-lg-row gap-3 mb-0">
                  <li class="nav-item active"><a href="#flash-sale" class="nav-link">Flash Sale</a></li>
                  <li class="nav-item"><a href="#promo" class="nav-link">Siêu ưu đãi</a></li>
                  <li class="nav-item"><a href="#tech" class="nav-link">Điện máy – Công nghệ</a></li>
                  <li class="nav-item"><a href="#new" class="nav-link">Hàng mới về</a></li>
                  <li class="nav-item"><a href="#service" class="nav-link">Dịch vụ</a></li>
                  <li class="nav-item"><a href="#brands" class="nav-link">Thương hiệu</a></li>
                  <li class="nav-item"><a href="#blog" class="nav-link">Blog</a></li>
                </ul>
              </div>

            </div>
          </nav>
        </div>
      </div>
    </div>

  </header>
  <!-- ======================= HEADER END ======================= -->

  <!-- Rest of page unchanged: copy content from index.html (product sections, banners, footer, scripts) -->

  <!-- ======================= BANNER CHÍNH BEGIN ======================= -->
  <section class="py-3"
    style="background-image: url('images/background-pattern.jpg'); background-repeat: no-repeat; background-size: cover;">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

          <!-- ======================= BANNER BLOCKS BEGIN ======================= -->
          <div class="banner-blocks">

            <!-- ======================= MAIN SWIPER BANNER BEGIN ======================= -->
            <div class="banner-ad large bg-info block-1">
              <div class="swiper main-swiper">
                <div class="swiper-wrapper">

                  <!-- Slide 1 -->
                  <div class="swiper-slide">
                    <div class="row banner-content p-5">
                      <div class="content-wrapper col-md-7">
                        <div class="categories my-3">Hot Deal</div>
                        <h3 class="display-4">Điện tử giảm giá mùa hè</h3>
                        <p>Khám phá các sản phẩm điện tử với ưu đãi hấp dẫn, miễn phí giao hàng.</p>
                        <a href="#" class="btn btn-outline-dark btn-lg text-uppercase fs-6 rounded-1 px-4 py-3 mt-3">Mua
                          Ngay</a>
                      </div>
                      <div class="img-wrapper col-md-5">
                        <img src="images/product-electronics.png" class="img-fluid" alt="Điện tử giảm giá">
                      </div>
                    </div>
                  </div>

                  <!-- Slide 2 -->
                  <div class="swiper-slide">
                    <div class="row banner-content p-5">
                      <div class="content-wrapper col-md-7">
                        <div class="categories my-3">Khuyến mãi</div>
                        <h3 class="display-4">Thời trang & Phụ kiện</h3>
                        <p>Rinh ngay các bộ sưu tập mới nhất với giá ưu đãi chỉ trong hôm nay.</p>
                        <a href="#" class="btn btn-outline-dark btn-lg text-uppercase fs-6 rounded-1">Mua Ngay</a>
                      </div>
                      <div class="img-wrapper col-md-5">
                        <img src="images/product-fashion.png" class="img-fluid" alt="Thời trang & phụ kiện">
                      </div>
                    </div>
                  </div>

                </div>

                <div class="swiper-pagination"></div>
              </div>
            </div>
            <!-- ======================= MAIN SWIPER BANNER END ======================= -->

            <!-- ======================= SMALL BANNER 1 BEGIN ======================= -->
            <div class="banner-ad bg-success-subtle block-2"
              style="background:url('images/ad-image-1.png') no-repeat;background-position:right bottom;background-size:cover;width:100%;">
              <div class="row banner-content p-5">
                <div class="content-wrapper col-md-7">
                  <div class="categories sale mb-3 pb-3">20% Off</div>
                  <h3 class="banner-title">Đồ gia dụng</h3>
                  <a href="#" class="d-flex align-items-center nav-link">
                    Xem Bộ Sưu Tập
                    <svg width="24" height="24">
                      <use xlink:href="#arrow-right"></use>
                    </svg>
                  </a>
                </div>
              </div>
            </div>
            <!-- ======================= SMALL BANNER 1 END ======================= -->

            <!-- ======================= SMALL BANNER 2 BEGIN ======================= -->
            <div class="banner-ad bg-danger block-3"
              style="background:url('images/ad-image-2.png') no-repeat;background-position:right bottom;background-size:cover;width:100%;">
              <div class="row banner-content p-5">
                <div class="content-wrapper col-md-7">
                  <div class="categories sale mb-3 pb-3">15% Off</div>
                  <h3 class="item-title">Sách & Văn phòng</h3>
                  <a href="#" class="d-flex align-items-center nav-link">
                    Xem Bộ Sưu Tập
                    <svg width="24" height="24">
                      <use xlink:href="#arrow-right"></use>
                    </svg>
                  </a>
                </div>
              </div>
            </div>
            <!-- ======================= SMALL BANNER 2 END ======================= -->

          </div>
          <!-- ======================= BANNER BLOCKS END ======================= -->

        </div>
      </div>
    </div>
  </section>
  <!-- ======================= BANNER CHÍNH END ======================= -->

  <!-- ======================= BÀI VIẾT (FROM DB) BEGIN ======================= -->
  <?php
    require_once __DIR__ . '/config/connect.php';
    $limit = 8;
        $sql = "SELECT DISTINCT p.id, p.title, p.description, p.price, p.currency, p.created_at, p.status, p.views,
                    (SELECT filename FROM product_images pi2 WHERE pi2.product_id = p.id ORDER BY pi2.sort_order ASC LIMIT 1) AS image_file,
                    c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.status = 'approved'
          ORDER BY p.created_at DESC, p.id DESC
            LIMIT ?";
    $posts = [];
    if ($stmt = $conn->prepare($sql)) {
      $stmt->bind_param('i', $limit);
      $stmt->execute();
      $res = $stmt->get_result();
      while ($row = $res->fetch_assoc()) { $posts[] = $row; }
      $stmt->close();
    }
    function sm_db_excerpt($html, $len=250) {
      $txt = strip_tags($html ?? '');
      $txt = trim($txt);
      if (mb_strlen($txt,'UTF-8') <= $len) return $txt;
      return mb_substr($txt,0,$len,'UTF-8') . '...';
    }
    function sm_db_image($image_file) {
      $uploads = realpath(__DIR__ . '/uploads');
      if ($image_file && $uploads) {
        $candidate = $uploads . DIRECTORY_SEPARATOR . $image_file;
        if (is_file($candidate)) return 'uploads/' . $image_file;
        $base = strtolower(basename($image_file));
        $files = @scandir($uploads);
        if (is_array($files)) {
          foreach ($files as $f) { if (strtolower($f) === $base) return 'uploads/' . $f; }
        }
      }
      return 'images/default-product.jpg';
    }
  ?>
  <section class="py-5" id="posts-section">
    <div class="container">
      <h2 class="section-title text-center mb-4">Bài viết</h2>
      <div class="row products-compact-row" id="posts-container" data-limit="8">
        <?php if (!empty($posts)): foreach ($posts as $p):
          $title = htmlspecialchars($p['title'] ?? '', ENT_QUOTES, 'UTF-8');
          $img = sm_db_image($p['image_file'] ?? '');
          $excerpt = htmlspecialchars(sm_db_excerpt($p['description'] ?? ''), ENT_QUOTES, 'UTF-8');
          $link = 'product.php?id=' . (int)$p['id'];
          $price = '';
          if ($p['price'] !== null && $p['price'] !== '') {
            $val = is_numeric($p['price']) ? number_format($p['price'],0,',','.') : htmlspecialchars($p['price'], ENT_QUOTES, 'UTF-8');
            $cur = htmlspecialchars($p['currency'] ?? '', ENT_QUOTES, 'UTF-8');
            $price = '<div class="price">' . $val . ' ' . $cur . '</div>';
          }
          $status = htmlspecialchars($p['status'] ?? '', ENT_QUOTES, 'UTF-8');
          $views = (int)($p['views'] ?? 0);
          $dateStr = '';
          if (!empty($p['created_at'])) { $dateStr = date('d/m/Y', strtotime($p['created_at'])); }
        ?>
        <div class="col-md-3 col-sm-6 mb-4">
          <div class="product-item border p-3 position-relative compact h-100" data-category="<?php echo htmlspecialchars($p['category_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-product-id="<?php echo (int)$p['id']; ?>">
            <a href="<?php echo $link; ?>" class="btn-wishlist favorite-btn"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>
            <figure class="text-center mb-3">
              <a href="<?php echo $link; ?>"><img src="<?php echo $img; ?>" class="tab-image" alt="<?php echo $title; ?>"></a>
            </figure>
            <h3 class="product-name"><?php echo $title; ?></h3>
            <p class="card-text small text-muted"><?php echo $excerpt; ?></p>
            <a href="<?php echo $link; ?>#description" class="small">Xem thêm</a>
            <?php
              if ($price) {
                // Convert to span like search results styling
                $priceText = strip_tags($price);
                echo '<span class="price d-block mt-2 text-success fw-bold">'.$priceText.'</span>';
              }
            ?>
          </div>
        </div>
        <?php endforeach; else: ?>
          <div class="col-12 text-center text-muted">Chưa có bài viết nào.</div>
        <?php endif; ?>
      </div>
      <div class="text-center mt-3">
        <button id="load-more-posts" class="load-more-posts btn btn-outline-primary">Tải thêm</button>
      </div>
    </div>
  </section>
  <!-- ======================= BÀI VIẾT (FROM DB) END ======================= -->

  <!-- ======================= SEARCH RESULTS (dynamic) BEGIN ======================= -->
  <section class="py-5" id="search-results-section" data-q="" data-category="" data-offset="0" data-limit="4">
    <div class="container">
      <h2 class="section-title text-center mb-4">Sản phẩm được tìm kiếm</h2>
      <div class="products-carousel position-relative">
        <div class="swiper">
          <div class="swiper-wrapper">
            <!-- slides injected by JavaScript -->
            <div class="swiper-slide no-results p-3">Chưa có kết quả tìm kiếm.</div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- ======================= SEARCH RESULTS (dynamic) END ======================= -->

  <!-- (the rest of the original `index.html` content continues here unchanged) -->

  <!-- ======================= FOOTER BEGIN ======================= -->
  <footer class="py-5">
    <div class="container-fluid">
      <div class="row">

        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="footer-menu">
            <img src="images/logo.png" alt="logo" height="80" class="logo">
          </div>
        </div>

        <div class="col-md-2 col-sm-6">
          <div class="footer-menu">
            <h5 class="product-name widget-title">Về Chúng Tôi</h5>
            <ul class="menu-list list-unstyled">
              <li><a href="#" class="nav-link">Giới thiệu</a></li>
              <li><a href="#" class="nav-link">Điều khoản</a></li>
              <li><a href="#" class="nav-link">Bài viết của chúng tôi</a></li>
              <li><a href="#" class="nav-link">Tuyển dụng</a></li>
              <li><a href="#" class="nav-link">Chương trình liên kết</a></li>
              <li><a href="#" class="nav-link">Báo chí Ultras</a></li>
            </ul>
          </div>
        </div>

        <div class="col-md-2 col-sm-6">
          <div class="footer-menu">
            <h5 class="product-name widget-title">Dịch vụ Khách hàng</h5>
            <ul class="menu-list list-unstyled">
              <li><a href="#" class="nav-link">Câu hỏi thường gặp</a></li>
              <li><a href="#" class="nav-link">Liên hệ</a></li>
              <li><a href="#" class="nav-link">Chính sách bảo mật</a></li>
              <li><a href="#" class="nav-link">Đổi trả & Hoàn tiền</a></li>
              <li><a href="#" class="nav-link">Chính sách Cookie</a></li>
              <li><a href="#" class="nav-link">Thông tin giao hàng</a></li>
            </ul>
          </div>
        </div>

        <div class="col-md-2 col-sm-6">
          <div class="footer-menu">
            <h5 class="product-name widget-title">Hỗ trợ Khách hàng</h5>
            <ul class="menu-list list-unstyled">
              <li><a href="#" class="nav-link">Câu hỏi thường gặp</a></li>
              <li><a href="#" class="nav-link">Liên hệ</a></li>
              <li><a href="#" class="nav-link">Chính sách bảo mật</a></li>
              <li><a href="#" class="nav-link">Đổi trả & Hoàn tiền</a></li>
              <li><a href="#" class="nav-link">Chính sách Cookie</a></li>
              <li><a href="#" class="nav-link">Thông tin giao hàng</a></li>
            </ul>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="footer-menu">
            <h5 class="product-name widget-title">Đăng ký nhận tin</h5>
            <p>Đăng ký nhận bản tin của chúng tôi để cập nhật các chương trình ưu đãi mới nhất.</p>
            <form class="d-flex mt-3" role="newsletter">
              <input class="form-control rounded-start bg-light" type="email" placeholder="Địa chỉ Email">
              <button class="btn btn-dark rounded-end" type="submit">Đăng ký</button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </footer>

  <div id="footer-bottom">
    <div class="container-fluid d-flex justify-content-center">
      <div class="col-md-6 text-center">
        <p>©2025 SmartMarket. Bản quyền thuộc về SmartMarket.</p>
      </div>
    </div>
  </div>
  <!-- ======================= FOOTER END ======================= -->


  </div>
  <script src="js/jquery-1.11.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
    crossorigin="anonymous"></script>
  <script src="js/plugins.js"></script>
  <script src="js/script.js"></script>
  <script src="js/main.js"></script>

  <script>
    // Compatibility shim: ensure new `.post-item` markup matches legacy selectors
    document.addEventListener('DOMContentLoaded', function () {
      try {
        document.querySelectorAll('.post-item').forEach(function (el) {
          if (!el.classList.contains('product-item')) el.classList.add('product-item');
          var cat = el.getAttribute('data-category') || '';
          if (cat && !el.getAttribute('data-category-key')) {
            try {
              var key = cat.toString().trim().toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
              key = key.replace(/[^a-z0-9]/g, '');
              el.setAttribute('data-category-key', key);
            } catch (e) {
              var key = cat.toString().trim().toLowerCase().replace(/[^a-z0-9\s]/g, '');
              key = key.replace(/\s+/g, '');
              el.setAttribute('data-category-key', key);
            }
          }
        });

        // Attach wishlist class normalization
        document.querySelectorAll('.favorite-btn').forEach(function (b) {
          if (!b.classList.contains('btn-wishlist')) b.classList.add('btn-wishlist');
        });

        // Load more posts via AJAX (posts-section) - attach once, outside favorites loop
        (function(){
          var loadBtn = document.getElementById('load-more-posts');
          var container = document.getElementById('posts-container');
          if (!loadBtn || !container) return;
          var limit = parseInt(container.getAttribute('data-limit')||8,10);
          var offset = container.querySelectorAll('.col-md-3, .col-md-4').length;
          loadBtn.addEventListener('click', function(){
            loadBtn.disabled = true; loadBtn.textContent = 'Đang tải...';
            var existingIds = Array.prototype.map.call(
              container.querySelectorAll('[data-product-id]'),
              function(el){ return String(el.getAttribute('data-product-id')); }
            );
            var excludeParam = existingIds.length ? '&exclude_ids='+encodeURIComponent(existingIds.join(',')) : '';
            var xhr = new XMLHttpRequest();
            // With server-side exclude_ids support, use offset=0 to avoid skipping valid items
            xhr.open('GET', 'favorites/get_products.php?limit='+encodeURIComponent(limit)+'&offset=0'+excludeParam, true);
            xhr.responseType = 'json';
            xhr.onload = function(){
              loadBtn.disabled = false; loadBtn.textContent = 'Tải thêm';
              // Check HTTP status first
              if (xhr.status !== 200) {
                try {
                  var msgId = 'posts-load-error-msg';
                  var msg = document.getElementById(msgId);
                  if (!msg) {
                    msg = document.createElement('div');
                    msg.id = msgId;
                    msg.className = 'text-danger small mt-2';
                    msg.textContent = 'Không tải được dữ liệu (mã ' + xhr.status + '). Vui lòng thử lại.';
                    loadBtn.parentNode.appendChild(msg);
                  } else {
                    msg.style.display = '';
                    msg.textContent = 'Không tải được dữ liệu (mã ' + xhr.status + '). Vui lòng thử lại.';
                  }
                  setTimeout(function(){ if (msg) msg.style.display = 'none'; }, 3000);
                } catch(e) { console.debug('Status error UI failed', e); }
                return;
              }
              var resp = xhr.response;
              // Fallback parse if responseType=json isn't honored
              if (!resp || (typeof resp !== 'object')) {
                try { resp = JSON.parse(xhr.responseText || '{}'); } catch(e) { resp = null; }
              }
              if (!resp || resp.status !== 'success' || !Array.isArray(resp.products)) {
                console.debug('Load-more: invalid response', resp && resp.message);
                try {
                  var msgId2 = 'posts-load-error-msg';
                  var msg2 = document.getElementById(msgId2);
                  if (!msg2) {
                    msg2 = document.createElement('div');
                    msg2.id = msgId2;
                    msg2.className = 'text-danger small mt-2';
                    msg2.textContent = 'Phản hồi không hợp lệ từ máy chủ.';
                    loadBtn.parentNode.appendChild(msg2);
                  } else {
                    msg2.style.display = '';
                    msg2.textContent = 'Phản hồi không hợp lệ từ máy chủ.';
                  }
                  setTimeout(function(){ if (msg2) msg2.style.display = 'none'; }, 3000);
                } catch(e) { console.debug('Invalid response UI failed', e); }
                return;
              }
              var prods = resp.products;
              // Ensure Swiper initialized for search results section
              (function(){
                var searchSection = document.getElementById('search-results-section');
                if (!searchSection) return;
                var swiperEl = searchSection.querySelector('.swiper');
                if (swiperEl && !swiperEl.swiper) {
                  try {
                    var instance = new Swiper(swiperEl, {
                      slidesPerView: 4,
                      spaceBetween: 16,
                      breakpoints: {
                        0: { slidesPerView: 1 },
                        576: { slidesPerView: 2 },
                        992: { slidesPerView: 3 },
                        1200: { slidesPerView: 4 }
                      },
                      pagination: { el: searchSection.querySelector('.swiper-pagination'), clickable: true }
                    });
                    window.searchResultsSwiper = instance;
                  } catch(e) { console.debug('Search Swiper init failed', e); }
                }
              })();
              console.debug('Posts load-more returned', {offset: offset, exclude: existingIds, count: prods.length, ids: (prods||[]).map(function(p){return p.id;})});
              if (prods.length === 0) {
                // Show end-of-list message once
                try {
                  var endId = 'posts-load-end-msg';
                  var endMsg = document.getElementById(endId);
                  if (!endMsg) {
                    endMsg = document.createElement('div');
                    endMsg.id = endId;
                    endMsg.className = 'text-muted small mt-2';
                    endMsg.textContent = 'Không còn sản phẩm.';
                    loadBtn.parentNode.appendChild(endMsg);
                  }
                } catch(e) {}
                loadBtn.style.display = 'none';
                return;
              }
              // Deduplicate by existing product ids in the grid
              var existingIds = Array.prototype.map.call(
                container.querySelectorAll('[data-product-id]'),
                function(el){ return String(el.getAttribute('data-product-id')); }
              );
              var html = '';
              prods.forEach(function(row){
                var rid = String(row.id);
                if (existingIds.indexOf(rid) !== -1) { return; }
                var title = (row.title||'');
                var desc = (row.description||'').replace(/<[^>]*>?/gm,'');
                var excerpt = desc.trim().substring(0,250) + (desc.length>250? '...':'');
                var img = row.image || 'images/default-product.jpg';
                var link = 'product.php?id=' + encodeURIComponent(row.id);
                var priceText = '';
                if (row.price !== undefined && row.price !== null && row.price !== '') {
                  var p = row.price;
                  if (!isNaN(p)) { p = Number(p).toLocaleString('vi-VN'); }
                  priceText = p + ' ' + (row.currency||'');
                }
                html += '<div class="col-md-3 col-sm-6 mb-4">';
                html += '<div class="product-item border p-3 position-relative compact h-100" data-category="'+(row.category_name||'')+'" data-product-id="'+row.id+'">';
                html += '<a href="#" class="btn-wishlist favorite-btn"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>';
                html += '<figure class="text-center mb-3"><a href="'+link+'"><img src="'+img+'" class="tab-image" alt="'+title+'"></a></figure>';
                html += '<h3 class="product-name">'+title+'</h3>';
                html += '<p class="card-text small text-muted">'+excerpt+'</p>';
                html += '<a href="'+link+'#description" class="small">Xem thêm</a>';
                if (priceText) html += '<span class="price d-block mt-2 text-success fw-bold">'+priceText+'</span>';
                html += '</div>';
                html += '</div>';
              });
              if (html) {
                container.insertAdjacentHTML('beforeend', html);
                // Update offset only by newly appended count
                var appendedCount = (html.match(/data-product-id=/g)||[]).length;
                offset += appendedCount;
                console.debug('Posts appended', {appendedCount: appendedCount, newOffset: offset});
              }
            };
            xhr.onerror = function(){
              loadBtn.disabled = false;
              // Show a brief error state on the button
              var originalText = 'Tải thêm';
              loadBtn.textContent = 'Lỗi, thử lại';
              // Also surface a lightweight message under the button
              try {
                var msgId = 'posts-load-error-msg';
                var msg = document.getElementById(msgId);
                if (!msg) {
                  msg = document.createElement('div');
                  msg.id = msgId;
                  msg.className = 'text-danger small mt-2';
                  msg.textContent = 'Không tải được dữ liệu. Vui lòng thử lại.';
                  loadBtn.parentNode.appendChild(msg);
                } else {
                  msg.style.display = '';
                  msg.textContent = 'Không tải được dữ liệu. Vui lòng thử lại.';
                }
                // Auto-hide the message after a short delay
                setTimeout(function(){ if (msg) msg.style.display = 'none'; }, 3000);
              } catch (e) {
                console.debug('Load-more error UI failed', e);
              }
              // Revert button text back after a short delay
              setTimeout(function(){ loadBtn.textContent = originalText; }, 1500);
            };
            xhr.send();
          });
        })();
      } catch (e) {
        console.debug('Compatibility shim failed', e);
      }
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const favoriteButtons = document.querySelectorAll('.favorite-btn');

      favoriteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
          event.preventDefault();
          var btn = this;
          // Locate the product card robustly across sections
          var productItem = btn.closest('.product-item') || btn.closest('.post-item');
          if (!productItem) {
            console.error('Product element not found');
            console.debug('Không tìm thấy sản phẩm.');
            return;
          }

          var productId = productItem.getAttribute('data-product-id') || productItem.dataset.productId || productItem.getAttribute('data-id') || '';

          btn.classList.add('btn-wishlist--loading');

          var formData = new FormData();
          formData.append('product_id', productId);

          fetch('favorites/handle_favorite.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin'
          })
            .then(function (response) {
              return response.json().catch(function () {
                throw new Error('Invalid JSON from server');
              });
            })
            .then(function (data) {
              btn.classList.remove('btn-wishlist--loading');
              if (data && data.status === 'success') {
                if (data.action === 'added') btn.classList.add('favorited'); else btn.classList.remove('favorited');
                console.debug(data.message || 'Đã cập nhật yêu thích.');
                // Refresh favorites UI to avoid duplicates
                if (typeof updateFavoritesBadges === 'function') updateFavoritesBadges();
                if (typeof window.refreshFavoritesList !== 'function') {
                  window.refreshFavoritesList = function(){
                    var listEl = document.getElementById('favorites-list');
                    var countBadge = document.getElementById('favorites-count-offcanvas');
                    fetch('favorites/get_favorites.php', { credentials: 'same-origin' })
                      .then(function(r){ return r.json().catch(function(){ throw new Error('Invalid JSON'); }); })
                      .then(function(payload){
                        if (!payload || payload.status !== 'success' || !Array.isArray(payload.favorites)) return;
                        // Deduplicate by product_id
                        var seen = new Set();
                        var items = [];
                        payload.favorites.forEach(function(f){
                          var pid = String(f.product_id || f.id || '');
                          if (!pid || seen.has(pid)) return;
                          seen.add(pid);
                          items.push(f);
                        });
                        // Render
                        if (listEl) {
                          listEl.innerHTML = '';
                          if (items.length === 0) {
                            listEl.innerHTML = '<li class="list-group-item text-center text-muted">Chưa có sản phẩm</li>';
                          } else {
                            items.forEach(function(f){
                              var title = (f.title||'Sản phẩm');
                              var img = f.image || 'images/default-product.jpg';
                              var li = document.createElement('li');
                              li.className = 'list-group-item d-flex align-items-center gap-2';
                                li.setAttribute('data-product-id', String(f.product_id||f.id||''));
                                li.setAttribute('data-favorite-id', String(f.favorite_id||''));
                                li.innerHTML = '<img src="'+img+'" alt="'+title+'" width="40" height="40" class="rounded">' +
                                               '<span class="flex-grow-1">'+title+'</span>';
                              listEl.appendChild(li);
                            });
                          }
                        }
                        if (countBadge) { countBadge.textContent = String(items.length); }
                      })
                      .catch(function(err){ console.debug('Refresh favorites failed', err); });
                  };
                }
                window.refreshFavoritesList();
              } else {
                var msg = (data && data.message) ? data.message : 'Lỗi khi xử lý yêu thích.';
                console.debug('Lỗi: ' + msg);
              }
            })
            .catch(function (err) {
              console.error('Favorite request failed', err);
              btn.classList.remove('btn-wishlist--loading');
              console.debug('Đã xảy ra lỗi. Vui lòng thử lại.');
            });
        });
      });

      // Load more for Search Results section (#load-more-search)
      (function(){
        var searchSection = document.getElementById('search-results-section');
        var loadBtn = document.getElementById('load-more-search');
        if (!searchSection) return;
        var wrapper = searchSection.querySelector('.swiper .swiper-wrapper');
        var limit = parseInt(searchSection.getAttribute('data-limit')||8,10);
        // Derive offset from current rendered slides to keep paging accurate
        var offset = (function(){
          try {
            var count = searchSection.querySelectorAll('.swiper .swiper-wrapper .swiper-slide .product-item').length;
            return parseInt(count||0,10);
          } catch(e) { return parseInt(searchSection.getAttribute('data-offset')||0,10); }
        })();
        // Try to read current filters from section attributes or URL query
        var q = (searchSection.getAttribute('data-q')||'').trim();
        var category = (searchSection.getAttribute('data-category')||'').trim();
        try {
          var params = new URLSearchParams(window.location.search);
          if (!q && params.has('query')) q = (params.get('query')||'').trim();
          if (!category && params.has('category')) category = (params.get('category')||'').trim();
        } catch(e) {}

        // If there's no load-more button (button removed), auto-load only the first `limit` items
        if (!loadBtn) {
          // Initialize Swiper if not present
          try {
            var swiperElInit = searchSection.querySelector('.swiper');
            if (swiperElInit && !swiperElInit.swiper) {
              window.searchResultsSwiper = new Swiper(swiperElInit, {
                slidesPerView: 4,
                spaceBetween: 16,
                breakpoints: {
                  0: { slidesPerView: 1 },
                  576: { slidesPerView: 2 },
                  992: { slidesPerView: 3 },
                  1200: { slidesPerView: 4 }
                },
                pagination: { el: searchSection.querySelector('.swiper-pagination'), clickable: true }
              });
            }
          } catch(e) { console.debug('Search init Swiper failed', e); }

          var paramsStr = 'limit='+encodeURIComponent(limit)+'&offset=0';
          if (q) paramsStr += '&q='+encodeURIComponent(q);
          if (category) paramsStr += '&category='+encodeURIComponent(category);
          var xhrInit = new XMLHttpRequest();
          xhrInit.open('GET', 'favorites/get_products.php?'+paramsStr, true);
          xhrInit.responseType = 'json';
          xhrInit.onload = function(){
            if (xhrInit.status !== 200) {
              console.debug('Search initial load failed', xhrInit.status);
              return;
            }
            var respI = xhrInit.response;
            if (!respI || (typeof respI !== 'object')) {
              try { respI = JSON.parse(xhrInit.responseText || '{}'); } catch(e) { respI = null; }
            }
            if (!respI || respI.status !== 'success' || !Array.isArray(respI.products)) return;
            var prodsI = respI.products;
            var htmlI = '';
            prodsI.forEach(function(row){
              var title = (row.title||'');
              var desc = (row.description||'').replace(/<[^>]*>?/gm,'');
              var excerpt = desc.trim().substring(0,250) + (desc.length>250? '...':'');
              var img = row.image || 'images/default-product.jpg';
              var link = 'product.php?id=' + encodeURIComponent(row.id);
              var priceText = '';
              if (row.price !== undefined && row.price !== null && row.price !== '') {
                var p = row.price; if (!isNaN(p)) { p = Number(p).toLocaleString('vi-VN'); }
                priceText = p + ' ' + (row.currency||'');
              }
              htmlI += '<div class="swiper-slide">';
              htmlI += '<div class="product-item border p-3 position-relative compact h-100" data-category="'+(row.category_name||'')+'" data-product-id="'+row.id+'">';
              htmlI += '<a href="#" class="btn-wishlist favorite-btn"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>';
              htmlI += '<figure class="text-center mb-3"><a href="'+link+'"><img src="'+img+'" class="tab-image" alt="'+title+'"></a></figure>';
              htmlI += '<h3 class="product-name">'+title+'</h3>';
              htmlI += '<p class="card-text small text-muted">'+excerpt+'</p>';
              htmlI += '<a href="'+link+'#description" class="small">Xem thêm</a>';
              if (priceText) htmlI += '<span class="price d-block mt-2 text-success fw-bold">'+priceText+'</span>';
              htmlI += '</div>';
              htmlI += '</div>';
            });
            if (wrapper && htmlI) {
              wrapper.innerHTML = htmlI; // replace any no-results
              try {
                var swiperEl2 = searchSection.querySelector('.swiper');
                if (swiperEl2 && swiperEl2.swiper && typeof swiperEl2.swiper.update === 'function') {
                  swiperEl2.swiper.update();
                }
              } catch(e) {}
            }
          };
          xhrInit.onerror = function(){ console.debug('Search initial load error'); };
          xhrInit.send();
          return; // do not attach click handler
        }

        loadBtn.addEventListener('click', function(){
          loadBtn.disabled = true; loadBtn.textContent = 'Đang tải...';
          // For search paging, use classic offset without exclude to avoid over-filtering
          var params = 'limit='+encodeURIComponent(limit)+'&offset='+encodeURIComponent(offset);
          if (q) params += '&q='+encodeURIComponent(q);
          if (category) params += '&category='+encodeURIComponent(category);
          var xhr = new XMLHttpRequest();
          xhr.open('GET', 'favorites/get_products.php?'+params, true);
          xhr.responseType = 'json';
          xhr.onload = function(){
            loadBtn.disabled = false; loadBtn.textContent = 'Tải thêm';
            if (xhr.status !== 200) {
              try {
                var msgId = 'search-load-error-msg';
                var msg = document.getElementById(msgId);
                if (!msg) {
                  msg = document.createElement('div');
                  msg.id = msgId;
                  msg.className = 'text-danger small mt-2';
                  msg.textContent = 'Không tải được dữ liệu (mã ' + xhr.status + '). Vui lòng thử lại.';
                  loadBtn.parentNode.appendChild(msg);
                } else {
                  msg.style.display = '';
                  msg.textContent = 'Không tải được dữ liệu (mã ' + xhr.status + '). Vui lòng thử lại.';
                }
                setTimeout(function(){ if (msg) msg.style.display = 'none'; }, 3000);
              } catch(e) { console.debug('Search status error UI failed', e); }
              return;
            }
            var resp = xhr.response;
            if (!resp || (typeof resp !== 'object')) {
              try { resp = JSON.parse(xhr.responseText || '{}'); } catch(e) { resp = null; }
            }
            if (!resp || resp.status !== 'success' || !Array.isArray(resp.products)) {
              try {
                var msgId2 = 'search-load-error-msg';
                var msg2 = document.getElementById(msgId2);
                if (!msg2) {
                  msg2 = document.createElement('div');
                  msg2.id = msgId2;
                  msg2.className = 'text-danger small mt-2';
                  msg2.textContent = 'Phản hồi không hợp lệ từ máy chủ.';
                  loadBtn.parentNode.appendChild(msg2);
                } else {
                  msg2.style.display = '';
                  msg2.textContent = 'Phản hồi không hợp lệ từ máy chủ.';
                }
                setTimeout(function(){ if (msg2) msg2.style.display = 'none'; }, 3000);
              } catch(e) { console.debug('Search invalid response UI failed', e); }
              return;
            }
            var prods = resp.products;
            console.debug('Search load-more returned', {offset: offset, q: q, category: category, count: prods.length, ids: (prods||[]).map(function(p){return p.id;})});
            if (prods.length === 0) {
              try {
                var endId2 = 'search-load-end-msg';
                var endMsg2 = document.getElementById(endId2);
                if (!endMsg2) {
                  endMsg2 = document.createElement('div');
                  endMsg2.id = endId2;
                  endMsg2.className = 'text-muted small mt-2';
                  endMsg2.textContent = 'Không còn sản phẩm.';
                  loadBtn.parentNode.appendChild(endMsg2);
                }
              } catch(e) {}
              loadBtn.style.display = 'none';
              return;
            }
            // Deduplicate by existing product ids in the swiper wrapper
            var existingIds = Array.prototype.map.call(
              wrapper.querySelectorAll('[data-product-id]'),
              function(el){ return String(el.getAttribute('data-product-id')); }
            );
            var html = '';
            prods.forEach(function(row){
              var rid = String(row.id);
              if (existingIds.indexOf(rid) !== -1) { return; }
              var title = (row.title||'');
              var desc = (row.description||'').replace(/<[^>]*>?/gm,'');
              var excerpt = desc.trim().substring(0,250) + (desc.length>250? '...':'');
              var img = row.image || 'images/default-product.jpg';
              var link = 'product.php?id=' + encodeURIComponent(row.id);
              var priceText = '';
              if (row.price !== undefined && row.price !== null && row.price !== '') {
                var p = row.price;
                if (!isNaN(p)) { p = Number(p).toLocaleString('vi-VN'); }
                priceText = p + ' ' + (row.currency||'');
              }
              html += '<div class="swiper-slide">';
              html += '<div class="product-item border p-3 position-relative compact h-100" data-category="'+(row.category_name||'')+'" data-product-id="'+row.id+'">';
              html += '<a href="#" class="btn-wishlist favorite-btn"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>';
              html += '<figure class="text-center mb-3"><a href="'+link+'"><img src="'+img+'" class="tab-image" alt="'+title+'"></a></figure>';
              html += '<h3 class="product-name">'+title+'</h3>';
              html += '<p class="card-text small text-muted">'+excerpt+'</p>';
              html += '<a href="'+link+'#description" class="small">Xem thêm</a>';
              if (priceText) html += '<span class="price d-block mt-2 text-success fw-bold">'+priceText+'</span>';
              html += '</div>';
              html += '</div>';
            });
            if (wrapper && html) {
              wrapper.insertAdjacentHTML('beforeend', html);
              // Remove "no-results" slide if present
              var nr = wrapper.querySelector('.no-results');
              if (nr) nr.parentNode.removeChild(nr);
              // Update Swiper if present so new slides render
              try {
                if (window.searchResultsSwiper && typeof window.searchResultsSwiper.update === 'function') {
                  window.searchResultsSwiper.update();
                } else {
                  // Attempt to find an existing swiper instance attached to the element
                  var swiperEl = searchSection.querySelector('.swiper');
                  if (swiperEl && swiperEl.swiper && typeof swiperEl.swiper.update === 'function') {
                    swiperEl.swiper.update();
                  }
                }
              } catch(e) { console.debug('Swiper update failed', e); }
            }
            if (html) {
              var appendedCount = (html.match(/data-product-id=/g)||[]).length;
              // Recompute offset from DOM to be safe
              try {
                offset = searchSection.querySelectorAll('.swiper .swiper-wrapper .swiper-slide .product-item').length;
              } catch(e) {
                offset += appendedCount;
              }
              console.debug('Search appended', {appendedCount: appendedCount, newOffset: offset});
            }
            searchSection.setAttribute('data-offset', String(offset));
          };
          xhr.onerror = function(){
            loadBtn.disabled = false;
            var originalText = 'Tải thêm';
            loadBtn.textContent = 'Lỗi, thử lại';
            try {
              var msgId = 'search-load-error-msg';
              var msg = document.getElementById(msgId);
              if (!msg) {
                msg = document.createElement('div');
                msg.id = msgId;
                msg.className = 'text-danger small mt-2';
                msg.textContent = 'Không tải được dữ liệu. Vui lòng thử lại.';
                loadBtn.parentNode.appendChild(msg);
              } else {
                msg.style.display = '';
                msg.textContent = 'Không tải được dữ liệu. Vui lòng thử lại.';
              }
              setTimeout(function(){ if (msg) msg.style.display = 'none'; }, 3000);
            } catch(e) { console.debug('Search load-more error UI failed', e); }
            setTimeout(function(){ loadBtn.textContent = originalText; }, 1500);
          };
          xhr.send();
        });
      })();
    });
  </script>

  <!-- Add this element where the product grid or list is located -->
  <div id="no-products-message"
    style="display: none; text-align: center; margin-top: 20px; font-size: 18px; color: #888;">
    Không có sản phẩm nào
  </div>

</body>

</html>
