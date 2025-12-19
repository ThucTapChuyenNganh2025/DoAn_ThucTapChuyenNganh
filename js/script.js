(function ($) {
  "use strict";

  var initPreloader = function () {
    $(document).ready(function ($) {
      var Body = $("body");
      Body.addClass("preloader-site");
    });
    $(window).load(function () {
      $(".preloader-wrapper").fadeOut();
      $("body").removeClass("preloader-site");
    });
  };

  // init Chocolat light box
  var initChocolat = function () {
    Chocolat(document.querySelectorAll(".image-link"), {
      imageSize: "contain",
      loop: true,
    });
  };

  var initSwiper = function () {
    var swiper = new Swiper(".main-swiper", {
      speed: 500,
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
    });

    var category_swiper = new Swiper(".category-carousel", {
      slidesPerView: 6,
      spaceBetween: 30,
      speed: 500,
      navigation: {
        nextEl: ".category-carousel-next",
        prevEl: ".category-carousel-prev",
      },
      breakpoints: {
        0: {
          slidesPerView: 2,
        },
        768: {
          slidesPerView: 3,
        },
        991: {
          slidesPerView: 4,
        },
        1500: {
          slidesPerView: 6,
        },
      },
    });

    var brand_swiper = new Swiper(".brand-carousel", {
      slidesPerView: 6,
      spaceBetween: 30,
      speed: 500,
      navigation: {
        nextEl: ".brand-carousel-next",
        prevEl: ".brand-carousel-prev",
      },
      breakpoints: {
        0: {
          slidesPerView: 2,
        },
        768: {
          slidesPerView: 3,
        },
        991: {
          slidesPerView: 4,
        },
        1200: {
          slidesPerView: 5,
        },
        1500: {
          slidesPerView: 6,
        },
      },
    });

    // Initialize each products-carousel individually and bind navigation
    var productCarousels = document.querySelectorAll(".products-carousel");
    productCarousels.forEach(function (carouselEl) {
      try {
        var containerScope =
          carouselEl.closest("section") ||
          carouselEl.closest(".container-fluid") ||
          document;
        var prevBtn = containerScope.querySelector(".products-carousel-prev");
        var nextBtn = containerScope.querySelector(".products-carousel-next");

        new Swiper(carouselEl, {
          slidesPerView: 5,
          spaceBetween: 30,
          speed: 500,
          navigation: {
            nextEl: nextBtn ? nextBtn : ".products-carousel-next",
            prevEl: prevBtn ? prevBtn : ".products-carousel-prev",
          },
          breakpoints: {
            0: { slidesPerView: 1 },
            768: { slidesPerView: 3 },
            991: { slidesPerView: 4 },
            1500: { slidesPerView: 6 },
          },
        });
      } catch (e) {
        console.error("products-carousel init error:", e);
      }
    });
  };

  var initProductQty = function () {
    $(".product-qty").each(function () {
      var $el_product = $(this);

      $el_product.find(".quantity-right-plus").click(function (e) {
        e.preventDefault();
        var $input = $el_product.find(".input-number"); // lấy input theo class
        var quantity = parseInt($input.val());
        $input.val(quantity + 1);
      });

      $el_product.find(".quantity-left-minus").click(function (e) {
        e.preventDefault();
        var $input = $el_product.find(".input-number");
        var quantity = parseInt($input.val());
        if (quantity > 1) {
          // hạn chế xuống 1
          $input.val(quantity - 1);
        }
      });
    });
  };

  // init jarallax parallax
  var initJarallax = function () {
    jarallax(document.querySelectorAll(".jarallax"));

    jarallax(document.querySelectorAll(".jarallax-keep-img"), {
      keepImg: true,
    });
  };

  // document ready
  $(document).ready(function () {
    initPreloader();
    initSwiper();
    initProductQty();
    initJarallax();
    initChocolat();
  }); // End of a document
})(jQuery);
$(document).ready(function () {
  // click vào ô hiện dropdown
  $(".dropdown-selected").click(function (e) {
    e.stopPropagation(); // tránh click ngoài đóng luôn
    $(this).siblings(".dropdown-list").slideToggle(150);
  });

  // Delegated handler (fallback) — helps when direct binding fails or element is dynamic
  $(document).on("click", ".dropdown-selected", function (e) {
    e.preventDefault();
    e.stopPropagation();
    try {
      var $list = $(this).siblings(".dropdown-list");
      if ($list.length) {
        $list.slideToggle(150);
        $list.toggleClass("open");
      }
      console.log("dropdown-selected clicked");
    } catch (err) {
      console.error("dropdown toggle error", err);
    }
  });

  // click chọn 1 item
  $(".dropdown-list li").click(function () {
    var selectedText = $(this).text();
    $(this).addClass("selected").siblings().removeClass("selected");
    $(this)
      .closest(".custom-dropdown")
      .find(".dropdown-selected")
      .text(selectedText)
      .attr("data-category-key", normalizeKey(selectedText));
    // save selected category into any search form hidden inputs
    $("input[name='category']").val(selectedText);
    $(this).parent(".dropdown-list").slideUp(150);
    // also apply a grid filter if user clicked a category in the header dropdown
    try {
      console.debug("[debug] dropdown-list selected category:", selectedText);
      filterProductsByCategory(selectedText);
      try {
        renderSearchResults("", selectedText);
      } catch (err) {}
    } catch (e) {
      console.error(e);
    }
  });

  // click ngoài đóng dropdown
  $(document).click(function () {
    $(".dropdown-list").slideUp(150);
  });
});

// Standardize all `.product-item` markup to canonical product structure (like iPhone 15 Pro Max)
// This runs at DOM ready and rebuilds inner markup while preserving data attributes and visual state.
$(document).ready(function () {
  try {
    function buildCanonicalProduct($pi) {
      // Preserve attributes and state
      var dataAttrs = {};
      $.each($pi[0].attributes, function () {
        if (this && this.name && this.value) dataAttrs[this.name] = this.value;
      });

      var pid =
        dataAttrs["data-product-id"] || $pi.attr("data-product-id") || "";
      var category =
        dataAttrs["data-category"] || $pi.attr("data-category") || "";

      // Extract existing values where possible
      var imgSrc = $pi.find("img").first().attr("src") || "";
      var title = $pi.find(".product-name, h3, h5").first().text().trim() || "";
      var ratingHtml = $pi.find(".rating").first().html() || "";
      var priceText = $pi.find(".price").first().text().trim() || "";
      var qtyVal = (function () {
        var $inp = $pi.find(".input-number").first();
        if ($inp.length) return $inp.val() || "1";
        var $qtySpan = $pi.find(".qty").first();
        if ($qtySpan.length)
          return $qtySpan.text().replace(/[^0-9]/g, "") || "1";
        return "1";
      })();
      var favorited = $pi.find(".btn-wishlist").first().hasClass("favorited");

      // Build canonical inner elements
      var $wish = $(
        '<a href="#" class="btn-wishlist" aria-pressed="false"><svg width="24" height="24"><use xlink:href="#heart"></use></svg></a>'
      );
      if (favorited) $wish.addClass("favorited").attr("aria-pressed", "true");

      var $fig = $("<figure></figure>");
      var $figLink = $('<a href="#" class="product-link"></a>').attr(
        "title",
        title
      );
      var $img = $('<img class="tab-image" />')
        .attr("src", imgSrc)
        .attr("alt", title);
      $figLink.append($img);
      $fig.append($figLink);

      var $h = $('<h3 class="product-name"></h3>').text(title);
      var $qtySpan = $('<span class="qty"></span>').text(qtyVal + " cái");
      var $rating = $('<span class="rating"></span>').html(ratingHtml);
      var $price = $('<span class="price"></span>').text(priceText);

      // Controls: qty + cart
      var $controls = $(
        "<div class='qty-cart-wrapper d-flex align-items-center justify-content-between mt-2'></div>"
      );
      var $pq = $("<div class='input-group product-qty'></div>");
      var $minusWrap = $("<span class='input-group-btn'></span>");
      var $minus = $(
        "<button type='button' class='quantity-left-minus btn btn-danger btn-number' data-type='minus'><svg width='16' height='16'><use xlink:href='#minus'></use></svg></button>"
      );
      $minusWrap.append($minus);
      var $input = $(
        "<input type='text' class='form-control input-number' />"
      ).val(qtyVal);
      var $plusWrap = $("<span class='input-group-btn'></span>");
      var $plus = $(
        "<button type='button' class='quantity-right-plus btn btn-success btn-number' data-type='plus'><svg width='16' height='16'><use xlink:href='#plus'></use></svg></button>"
      );
      $plusWrap.append($plus);
      $pq.append($minusWrap).append($input).append($plusWrap);

      var $cartLink = $(
        "<a href='#' class='nav-link'><i class='fi fi-sr-shopping-cart-add cart-icon'></i></a>"
      );

      $controls.append($pq).append($cartLink);

      // Rebuild product inner content in canonical order
      $pi.empty();
      // Restore attributes (data-*)
      if (pid) $pi.attr("data-product-id", pid);
      if (category) $pi.attr("data-category", category);

      $pi
        .append($wish)
        .append($fig)
        .append($h)
        .append($qtySpan)
        .append($rating)
        .append($price)
        .append($controls);

      // ensure specific classes are present for handlers
      $pi.addClass("swiper-slide");
    }

    $(".product-item").each(function () {
      try {
        buildCanonicalProduct($(this));
      } catch (e) {
        console.error("standardize product failed for item", this, e);
      }
    });
  } catch (e) {
    console.error("product standardization failed", e);
  }
});
var related_products_swiper = new Swiper(".related-products-carousel", {
  slidesPerView: 5,
  spaceBetween: 15,
  navigation: {
    nextEl: ".related-products-carousel .related-next",
    prevEl: ".related-products-carousel .related-prev",
  },
  breakpoints: {
    0: { slidesPerView: 2 },
    576: { slidesPerView: 3 },
    768: { slidesPerView: 4 },
    991: { slidesPerView: 5 },
    1200: { slidesPerView: 6 },
  },
});
// Lấy tất cả phần tử có class "price"
const priceElements = document.querySelectorAll(".price");

priceElements.forEach((el) => {
  let rawPrice = el.textContent.trim();

  // Loại bỏ các ký tự không phải số
  let number = rawPrice.replace(/\D/g, "");

  if (number) {
    // Thêm dấu chấm phân cách hàng nghìn
    let formatted = number.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    // Gán lại vào HTML với "₫" phía sau
    el.textContent = formatted + " ₫";
  }
});
$(document).on("click", ".category-item", function (e) {
  e.preventDefault();
  let selectedCategory = $(this).data("category");
  // highlight category được chọn
  $(".category-item").removeClass("active-category");
  $(this).addClass("active-category");
  // update dropdown and hidden input
  $(".dropdown-selected").text(selectedCategory);
  $("input[name='category']").val(selectedCategory);
  // Luôn dùng filterProductsByCategory để lọc sản phẩm
  if (typeof filterProductsByCategory === "function") {
    try {
      console.debug(
        "[debug] category-item clicked, data-category:",
        selectedCategory
      );
      filterProductsByCategory(selectedCategory);
      try {
        renderSearchResults("", selectedCategory);
      } catch (err) {}
    } catch (e) {}
  }
});

// Initialize normalized category keys on DOM ready for robust matching
$(document).ready(function () {
  try {
    // For product items
    $(".product-item").each(function () {
      var cat = $(this).data("category") || "";
      var key = normalizeKey(cat);
      if (key) $(this).attr("data-category-key", key);
    });

    // For category selectors
    $(".category-item").each(function () {
      var cat =
        $(this).data("category") ||
        $(this).find(".category-title").text() ||
        "";
      var key = normalizeKey(cat);
      if (key) $(this).attr("data-category-key", key);
    });

    // For dropdown list entries (if any)
    $(".dropdown-list li").each(function () {
      var cat = $(this).text() || "";
      var key = normalizeKey(cat);
      if (key) $(this).attr("data-category-key", key);
    });

    console.debug("[debug] category keys initialized");
  } catch (err) {
    console.error("Error initializing category keys", err);
  }
});

/* Favorites (wishlist) handling */
function loadFavorites() {
  try {
    var raw = localStorage.getItem("sm_favorites") || "[]";
    return JSON.parse(raw);
  } catch (e) {
    return [];
  }
}

// Remove duplicate product DOM nodes by normalized name + category (keep first occurrence)
function dedupeProductItems() {
  try {
    var seen = {};
    $(".product-item").each(function () {
      var $p = $(this);
      var name = $p.find(".product-name, h3, h5").first().text().trim() || "";
      var cat = ($p.data("category") || $p.attr("data-category") || "")
        .toString()
        .trim();
      var key = (name + "||" + cat).toLowerCase();
      if (key === "||") return; // skip empty rows
      if (seen[key]) {
        $p.remove();
      } else {
        seen[key] = true;
      }
    });
  } catch (e) {
    console.error("dedupeProductItems failed", e);
  }
}

function saveFavorites(arr) {
  try {
    localStorage.setItem("sm_favorites", JSON.stringify(arr));
  } catch (e) {}
}

function getProductId($product) {
  // prefer server-side data-product-id or data-id if present
  try {
    var pid =
      $product.attr("data-product-id") ||
      $product.data("productId") ||
      $product.data("product-id") ||
      $product.data("id");
    if (pid) return pid.toString();
  } catch (e) {}
  var name = $product.find(".product-name").first().text() || "";
  var cat = ($product.data("category") || "").toString();
  var key = (name + "|" + cat).trim();
  return normalizeTextGlobal(key);
}

function updateFavoritesCount() {
  // Load count from server instead of localStorage
  $.ajax({
    url: "favorites/get_favorites_count.php",
    method: "GET",
    dataType: "json",
    success: function (data) {
      var count = data.count || 0;
      var $badge = $("#favorites-count");
      if (count > 0) {
        $badge.text(count).removeClass("d-none");
      } else {
        $badge.addClass("d-none");
      }
    },
    error: function () {
      // Fallback to localStorage if server fails
      var arr = loadFavorites();
      var count = Array.isArray(arr) ? arr.length : 0;
      var $badge = $("#favorites-count");
      if (count > 0) {
        $badge.text(count).removeClass("d-none");
      } else {
        $badge.addClass("d-none");
      }
    },
  });
}

// also update offcanvas badge and keep both in sync
function updateFavoritesBadges() {
  updateFavoritesCount();
  try {
    var arr = loadFavorites();
    var count = Array.isArray(arr) ? arr.length : 0;
    var $off = $("#favorites-count-offcanvas");
    if ($off.length) {
      $off.text(count);
    }
  } catch (e) {}
}

// initialize favorited buttons on page load
$(document).ready(function () {
  try {
    var favs = loadFavorites();
    $(".product-item").each(function () {
      var $p = $(this);
      var pid = getProductId($p);
      if (favs.indexOf(pid) !== -1) {
        $p.find(".btn-wishlist").addClass("favorited");
      }
    });
    updateFavoritesBadges();
  } catch (e) {}
});

// toggle favorite when clicking heart button
$(document).on("click", ".btn-wishlist", function (e) {
  e.preventDefault();
  e.stopPropagation();
  var $btn = $(this);
  // add loading state immediately for visual feedback
  $btn.addClass("btn-wishlist--loading");
  var $product = $btn.closest(".product-item");
  if (!$product.length) return;

  // Get product ID from data-product-id attribute
  var productId = $product.attr("data-product-id");
  if (productId) productId = productId.toString();
  // If no server-side product id, fallback to client-side id and localStorage
  if (!productId) {
    try {
      var clientPid = getProductId($product);
      // toggle in localStorage favorites
      var favs = loadFavorites();
      var idx = favs.indexOf(clientPid);
      if (idx === -1) {
        favs.push(clientPid);
        $btn.addClass("favorited").attr("aria-pressed", "true");
        console.debug("Đã thêm vào yêu thích (local)");
      } else {
        favs.splice(idx, 1);
        $btn.removeClass("favorited").attr("aria-pressed", "false");
        console.debug("Đã bỏ yêu thích (local)");
      }
      saveFavorites(favs);
      updateFavoritesBadges();
      // remove loading and show a short pulse animation
      $btn.removeClass("btn-wishlist--loading");
      $btn.addClass("btn-wishlist-pulse");
      setTimeout(function () {
        $btn.removeClass("btn-wishlist-pulse");
      }, 400);
      // refresh offcanvas if open
      if ($("#offcanvasFavorites").hasClass("show")) {
        renderFavoritesIntoOffcanvas();
      }
      return;
    } catch (e) {
      console.error("wishlist local toggle error", e);
      console.debug("Không tìm thấy ID sản phẩm.");
      return;
    }
  }

  // Check if user is logged in by checking session
  // collect metadata to send to server so favorites stored with useful info
  var metaTitle = $product.find(".product-name").first().text().trim() || "";
  var metaPrice = $product.find(".price").first().text().trim() || "";
  var metaImage = $product.find("img").first().attr("src") || "";

  $.ajax({
    url: "favorites/handle_favorite.php",
    method: "POST",
    data: {
      product_id: productId,
      title: metaTitle,
      price: metaPrice,
      image: metaImage,
    },
    dataType: "json",
    success: function (data) {
      if (data.status === "success") {
        if (data.action === "added") {
          $btn.addClass("favorited").attr("aria-pressed", "true");
        } else {
          $btn.removeClass("favorited").attr("aria-pressed", "false");
        }
        // visual feedback
        $btn.removeClass("btn-wishlist--loading");
        // add pulse then remove it on animation end for reliability
        $btn.addClass("btn-wishlist-pulse");
        $btn
          .off("animationend.smPulse")
          .on("animationend.smPulse", function () {
            $btn.removeClass("btn-wishlist-pulse");
          });
        console.debug("Favorites server response:", data.message, data.action);
        updateFavoritesBadges();

        // Sync localStorage favorites with server response to avoid stale local state
        try {
          var sid = productId ? productId.toString() : getProductId($product);
          var lf = loadFavorites();
          if (!Array.isArray(lf)) lf = [];
          if (data.action === "added") {
            if (lf.indexOf(sid) === -1) lf.push(sid);
          } else if (data.action === "removed") {
            var ridx = lf.indexOf(sid);
            if (ridx !== -1) lf.splice(ridx, 1);
          }
          saveFavorites(lf);
        } catch (e) {
          console.error("sync local favorites after server toggle failed", e);
        }

        // if favorites panel is open, refresh its contents
        if ($("#offcanvasFavorites").hasClass("show")) {
          if (typeof loadFavoritesFromServer === "function") {
            loadFavoritesFromServer();
          } else {
            renderFavoritesIntoOffcanvas();
          }
        }
      } else {
        // If error is about login, redirect to login page
        if (data.message && data.message.indexOf("đăng nhập") !== -1) {
          console.debug("Favorites require login:", data.message);
          setTimeout(function () {
            window.location.href = "user/dangnhap.php";
          }, 1200);
        } else {
          console.debug("Favorites error:", data.message || "Không xác định");
        }
      }
    },
    error: function (xhr, status, error) {
      // more detailed logging for debugging
      try {
        console.error("Favorites AJAX error:", {
          status: status,
          error: error,
          responseText: xhr && xhr.responseText,
        });
      } catch (e) {}

      // remove loading state
      try {
        $btn.removeClass("btn-wishlist--loading");
      } catch (e) {}

      // try to extract server message if available
      var msg = "Đã xảy ra lỗi. Vui lòng thử lại.";
      try {
        if (xhr && xhr.responseJSON && xhr.responseJSON.message)
          msg = xhr.responseJSON.message;
        else if (xhr && xhr.responseText) {
          var parsed = JSON.parse(xhr.responseText);
          if (parsed && parsed.message) msg = parsed.message;
        }
      } catch (e) {}

      // Fallback: if server failed, toggle favorite locally so user action isn't lost
      try {
        var pidLocal = productId || getProductId($product);
        if (pidLocal) {
          var favs = loadFavorites();
          var idx = favs.indexOf(pidLocal);
          if (idx === -1) {
            favs.push(pidLocal);
            $btn.addClass("favorited").attr("aria-pressed", "true");
            console.debug("Đã thêm vào yêu thích (cục bộ)");
          } else {
            favs.splice(idx, 1);
            $btn.removeClass("favorited").attr("aria-pressed", "false");
            console.debug("Đã bỏ yêu thích (cục bộ)");
          }
          saveFavorites(favs);
          updateFavoritesBadges();
          // refresh offcanvas if open
          if (
            "#offcanvasFavorites" &&
            $("#offcanvasFavorites").hasClass("show")
          ) {
            renderFavoritesIntoOffcanvas();
          }
          // short pulse animation
          $btn.addClass("btn-wishlist-pulse");
          $btn
            .off("animationend.smPulse")
            .on("animationend.smPulse", function () {
              $btn.removeClass("btn-wishlist-pulse");
            });
          return;
        }
      } catch (e) {
        console.error("Favorites fallback error:", e);
      }

      console.debug("Favorites AJAX error:", msg);
    },
  });
});

// Render favorites into the offcanvas and open it
function renderFavoritesIntoOffcanvas() {
  $.ajax({
    url: "favorites/get_favorites.php",
    method: "GET",
    dataType: "json",
    success: function (data) {
      var $list = $("#favorites-list");
      $list.empty();

      if (
        data.status === "error" ||
        !data.favorites ||
        data.favorites.length === 0
      ) {
        $list.append(
          '<li class="list-group-item text-center text-muted">Chưa có sản phẩm yêu thích</li>'
        );
        return;
      }

      // Render each favorite product
      data.favorites.forEach(function (product) {
        var imgSrc = product.image
          ? product.image
          : "images/default-product.jpg";
        var name = product.title || product.name || "Sản phẩm";
        var price = product.price || "0";
        var priceFormatted =
          price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + " ₫";

        var $li = $(
          '<li class="list-group-item d-flex align-items-center justify-content-between"></li>'
        );
        var $left = $("<div class='d-flex align-items-center gap-3'></div>");
        var $img = $("<img />").attr({ src: imgSrc, alt: name }).css({
          width: "64px",
          height: "64px",
          objectFit: "cover",
          borderRadius: "8px",
        });
        var $meta = $("<div></div>");
        $meta.append($("<div class='fw-semibold'></div>").text(name));
        $meta.append(
          $("<div class='small text-muted'></div>").text(priceFormatted)
        );
        $left.append($img).append($meta);

        var $actions = $("<div class='d-flex gap-2'></div>");
        var $remove = $(
          "<button class='btn btn-sm btn-outline-danger remove-favorite-server'>Xóa</button>"
        ).attr("data-pid", product.id);
        $actions.append($remove);

        $li.append($left).append($actions);
        $list.append($li);
      });
    },
    error: function () {
      // Fallback to localStorage if server fails
      var favs = loadFavorites();
      var $list = $("#favorites-list");
      $list.empty();
      if (!favs || !favs.length) {
        $list.append(
          '<li class="list-group-item text-center text-muted">Chưa có sản phẩm yêu thích</li>'
        );
        return;
      }

      // For each product on the page, if its id is in favorites, render an entry
      $(".product-item").each(function () {
        var $p = $(this);
        var pid = getProductId($p);
        if (favs.indexOf(pid) === -1) return;

        var imgSrc = $p.find("img").first().attr("src") || "";
        var name = $p.find(".product-name").first().text().trim() || "Sản phẩm";
        var price = $p.find(".price").first().text().trim() || "";

        var $li = $(
          '<li class="list-group-item d-flex align-items-center justify-content-between"></li>'
        );
        var $left = $("<div class='d-flex align-items-center gap-3'></div>");
        var $img = $("<img />").attr({ src: imgSrc, alt: name }).css({
          width: "64px",
          height: "64px",
          objectFit: "cover",
          borderRadius: "8px",
        });
        var $meta = $("<div></div>");
        $meta.append($("<div class='fw-semibold'></div>").text(name));
        $meta.append($("<div class='small text-muted'></div>").text(price));
        $left.append($img).append($meta);

        var $actions = $("<div class='d-flex gap-2'></div>");
        var $remove = $(
          "<button class='btn btn-sm btn-outline-danger remove-favorite'>Xóa</button>"
        ).attr("data-pid", pid);
        $actions.append($remove);

        $li.append($left).append($actions);
        $list.append($li);
      });
    },
  });
}

// initialize Offcanvas instance when DOM ready
var favoritesOffcanvas = null;
$(document).ready(function () {
  var el = document.getElementById("offcanvasFavorites");
  if (el) {
    try {
      favoritesOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(el);
    } catch (e) {
      try {
        favoritesOffcanvas = new bootstrap.Offcanvas(el);
      } catch (err) {}
    }
  }
});

// Open favorites offcanvas and render content
$(document).on("click", "#open-favorites", function (e) {
  e.preventDefault();
  try {
    renderFavoritesIntoOffcanvas();
    if (favoritesOffcanvas) favoritesOffcanvas.show();
  } catch (err) {
    console.error("open favorites error", err);
    var favs = loadFavorites();
    if (!favs || !favs.length) {
      console.debug("Chưa có sản phẩm yêu thích.");
    } else {
      console.debug("Sản phẩm yêu thích: " + favs.length + " mục.");
    }
  }
});

// Remove a favorite from the offcanvas list (server-based)
$(document).on("click", ".remove-favorite-server", function (e) {
  e.preventDefault();
  var pid = $(this).data("pid");
  if (!pid) return;
  // ensure pid is string for consistent comparison with session-stored ids
  pid = pid.toString();

  $.ajax({
    url: "favorites/handle_favorite.php",
    method: "POST",
    data: { product_id: pid, action: "remove" },
    dataType: "json",
    success: function (data) {
      if (data.status === "success") {
        updateFavoritesBadges();
        // remove favorited class from any matching buttons
        $(".btn-wishlist").each(function () {
          var $b = $(this);
          var $prod = $b.closest(".product-item");
          var prodId = $prod.attr("data-product-id");
          if (prodId == pid) {
            $b.removeClass("favorited");
          }
        });
        // re-render list (no toast)
        console.debug(data.message || "Đã bỏ yêu thích.");
        renderFavoritesIntoOffcanvas();
      }
    },
    error: function (xhr, status, err) {
      console.error("remove-favorite-server ajax error", {
        status: status,
        err: err,
        responseText: xhr && xhr.responseText,
      });
      console.debug("Không thể xóa trên server. Vui lòng thử lại.");
    },
  });
});

// Remove a favorite from the offcanvas list (localStorage fallback)
$(document).on("click", ".remove-favorite", function (e) {
  e.preventDefault();
  var pid = $(this).data("pid");
  if (!pid) return;
  pid = pid.toString();
  var favs = loadFavorites();
  var idx = favs.indexOf(pid);
  if (idx !== -1) {
    favs.splice(idx, 1);
    saveFavorites(favs);
    updateFavoritesBadges();
    // remove favorited class from any matching buttons
    $(".btn-wishlist").each(function () {
      var $b = $(this);
      var $prod = $b.closest(".product-item");
      if (getProductId($prod) === pid) {
        $b.removeClass("favorited");
      }
    });
    // re-render list
    renderFavoritesIntoOffcanvas();
  }
});

// Clear all favorites
$(document).on("click", "#clear-favorites", function (e) {
  e.preventDefault();
  showConfirm("Xóa tất cả sản phẩm yêu thích?", function() {
    $.ajax({
      url: "favorites/clear_favorites.php",
      method: "POST",
      dataType: "json",
      success: function (data) {
        if (data.status === "success") {
          toastSuccess('Đã xóa tất cả sản phẩm yêu thích!');
          updateFavoritesBadges();
          // remove favorited class from page buttons
          $(".btn-wishlist").removeClass("favorited");
          renderFavoritesIntoOffcanvas();
        } else {
          console.debug("Lỗi: " + (data.message || "Không xác định"));
        }
      },
      error: function () {
        // Fallback to localStorage
        try {
          saveFavorites([]);
          updateFavoritesBadges();
          $(".btn-wishlist").removeClass("favorited");
          renderFavoritesIntoOffcanvas();
          console.debug("Đã xóa tất cả sản phẩm yêu thích (cục bộ).");
        } catch (err) {
          console.error(err);
          console.debug("Không thể xóa sản phẩm yêu thích cục bộ.");
        }
      },
    });
  }, null, {
    title: 'Xác nhận xóa',
    confirmText: 'Xóa tất cả',
    cancelText: 'Hủy',
    type: 'danger'
  });
});

// Helper: normalize text (lowercase + strip diacritics)
function normalizeTextGlobal(s) {
  if (!s) return "";
  try {
    return s
      .toString()
      .trim()
      .toLowerCase()
      .normalize("NFD")
      .replace(/\p{Diacritic}/gu, "");
  } catch (e) {
    return s
      .toString()
      .trim()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");
  }
}

// Helper: normalized key (no spaces/punctuation) for category matching
function normalizeKey(s) {
  try {
    return normalizeTextGlobal(s || "").replace(/[^a-z0-9]/g, "");
  } catch (e) {
    return (s || "")
      .toString()
      .trim()
      .toLowerCase()
      .replace(/[^a-z0-9]/g, "");
  }
}

// Toast helper removed (replaced with console.debug in favorites flows)

// Filter products in the main grids by category (shows/hides columns)
function filterProductsByCategory(category) {
  var catNorm = normalizeTextGlobal(category || "");
  var catKey = normalizeKey(category || "");
  // Read possible search inputs: header form, offcanvas form, or legacy #search-box
  var rawSearch = "";
  try {
    rawSearch = $("#search-form input[name='query']").first().val() || "";
  } catch (e) {}
  if (!rawSearch) {
    try {
      rawSearch =
        $("form[role='search'] input[name='query']").first().val() || "";
    } catch (e) {}
  }
  if (!rawSearch) {
    try {
      rawSearch = $("#search-box").val() || "";
    } catch (e) {}
  }
  var searchQuery = normalizeTextGlobal(rawSearch || "");
  console.debug("[debug] filterProductsByCategory start", {
    category: category,
    catNorm: catNorm,
    rawSearch: rawSearch,
    searchQuery: searchQuery,
  });
  var $items = $(".product-item");
  var noProductsMessage = $("#no-products-message");

  // If the search box is empty and a category is selected, filter by category
  if (!searchQuery && catNorm) {
    var hasVisibleProducts = false;
    var matchedCount = 0;

    $items.each(function () {
      var $item = $(this);
      var itemCat = $item.data("category") || "";
      // prefer precomputed data-category-key if set
      var itemCatKey = $item.attr("data-category-key") || normalizeKey(itemCat);
      var itemCatNorm = normalizeTextGlobal(itemCat);
      var match = itemCatKey === catKey || itemCatNorm === catNorm;
      if (match) matchedCount++;
      var $col = $item.closest(".col");
      var $slide = $item.closest(".swiper-slide");

      if (match) {
        $item.removeClass("d-none").show();
        if ($col.length) $col.show();
        if ($slide.length) $slide.show();
        hasVisibleProducts = true;
      } else {
        if ($col.length) $col.hide();
        $item.addClass("d-none").hide();
        if ($slide.length && !$col.length) $slide.hide();
      }
    });

    if (hasVisibleProducts) {
      noProductsMessage.hide();
    } else {
      noProductsMessage.show();
    }
    console.debug("[debug] category-only filter results", {
      catNorm: catNorm,
      matchedCount: matchedCount,
      hasVisibleProducts: hasVisibleProducts,
    });
    return;
  }

  // If both category and search query provided: match both
  if (searchQuery && catNorm) {
    var hasVisibleBoth = false;
    $items.each(function () {
      var $item = $(this);
      var itemCat = $item.data("category") || "";
      var itemCatKey = $item.attr("data-category-key") || normalizeKey(itemCat);
      var itemCatNorm = normalizeTextGlobal(itemCat);
      var productName = normalizeTextGlobal(
        $item.find(".product-name, h3, h5").first().text() || ""
      );
      var categoryMatch = itemCatKey === catKey || itemCatNorm === catNorm;
      var nameMatch = productName.indexOf(searchQuery) !== -1;
      var shouldShow = categoryMatch && nameMatch;
      var $col = $item.closest(".col");
      var $slide = $item.closest(".swiper-slide");

      if (shouldShow) {
        $item.removeClass("d-none").show();
        if ($col.length) $col.show();
        if ($slide.length) $slide.show();
        hasVisibleBoth = true;
      } else {
        if ($col.length) $col.hide();
        $item.addClass("d-none").hide();
        if ($slide.length && !$col.length) $slide.hide();
      }
    });
    if (hasVisibleBoth) {
      noProductsMessage.hide();
    } else {
      noProductsMessage.show();
    }
    return;
  }

  // If only search (no category selected), filter across all products by name
  if (searchQuery && !catNorm) {
    var hasVisibleSearch = false;
    $items.each(function () {
      var $item = $(this);
      var productName = normalizeTextGlobal(
        $item.find(".product-name, h3, h5").first().text() || ""
      );
      var nameMatch = productName.indexOf(searchQuery) !== -1;
      var $col = $item.closest(".col");
      var $slide = $item.closest(".swiper-slide");
      if (nameMatch) {
        $item.removeClass("d-none").show();
        if ($col.length) $col.show();
        if ($slide.length) $slide.show();
        hasVisibleSearch = true;
      } else {
        if ($col.length) $col.hide();
        $item.addClass("d-none").hide();
        if ($slide.length && !$col.length) $slide.hide();
      }
    });
    if (hasVisibleSearch) {
      noProductsMessage.hide();
    } else {
      noProductsMessage.show();
    }
    return;
  }

  // If no category is selected, show all products
  if (!catNorm) {
    $items.each(function () {
      var $col = $(this).closest(".col");
      var $slide = $(this).closest(".swiper-slide");
      $(this).removeClass("d-none").show();
      if ($col.length) $col.show();
      if ($slide.length) $slide.show();
    });
    noProductsMessage.hide();
    return;
  }

  // Additional logic for combined filtering can be added here if needed
}
// When the offcanvas/global search form is submitted, render results into
// the "Sản phẩm được tìm kiếm" carousel instead of hiding the featured grid.
$("form[role='search']").on("submit", function (e) {
  e.preventDefault();
  var q = $(this).find("input[name='query']").val() || "";
  q = q.trim().toLowerCase();
  var cat = $(this).find("input[name='category']").val() || "";
  cat = cat.trim();
  renderSearchResults(q, cat);
});

// Also handle the header search form (desktop) if present
$(document).on("submit", "#search-form", function (e) {
  e.preventDefault();
  var q = $(this).find("input[name='query']").first().val() || "";
  q = q.trim().toLowerCase();
  var cat = $(this).find("input[name='category']").val() || "";
  cat = cat.trim();
  renderSearchResults(q, cat);
});

// store the original HTML of the "Sản phẩm được tìm kiếm" carousel so we can restore it
var originalResultsHtml = null;
var originalResultsSlides = null; // array of slide HTML strings

function ensureOriginalResultsSaved() {
  if (originalResultsHtml !== null) return;
  var header = $("h2.section-title")
    .filter(function () {
      return $(this).text().trim() === "Sản phẩm được tìm kiếm";
    })
    .first();
  if (!header.length) return;
  var carousel = header.closest("section").find(".products-carousel").first();
  var wrapper = carousel.find(".swiper-wrapper").first();
  originalResultsHtml = wrapper.html();
  // split into slides for easy restore when swiper API used
  originalResultsSlides = [];
  wrapper.children().each(function () {
    originalResultsSlides.push($(this).prop("outerHTML"));
  });
}

function restoreOriginalResults() {
  var header = $("h2.section-title")
    .filter(function () {
      return $(this).text().trim() === "Sản phẩm được tìm kiếm";
    })
    .first();
  if (!header.length) return;
  var carousel = header.closest("section").find(".products-carousel").first();
  var wrapper = carousel.find(".swiper-wrapper").first();
  var el = carousel[0];
  var useSwiperApi = el && el.swiper;
  if (useSwiperApi) {
    try {
      el.swiper.removeAllSlides();
      // append original slides back
      originalResultsSlides.forEach(function (s) {
        el.swiper.appendSlide(s);
      });
      el.swiper.update();
    } catch (e) {
      wrapper.html(originalResultsHtml);
    }
  } else {
    wrapper.html(originalResultsHtml);
  }
}

function renderSearchResults(query, category) {
  ensureOriginalResultsSaved();
  // find the search-results section by heading text
  var header = $("h2.section-title")
    .filter(function () {
      return $(this).text().trim() === "Sản phẩm được tìm kiếm";
    })
    .first();

  if (!header.length) return; // can't find the section

  var carousel = header.closest("section").find(".products-carousel").first();
  var wrapper = carousel.find(".swiper-wrapper").first();

  // clear previous results (use Swiper API if already initialized)
  var el = carousel[0];
  var useSwiperApi = el && el.swiper;
  if (useSwiperApi) {
    try {
      el.swiper.removeAllSlides();
    } catch (e) {}
  } else {
    wrapper.empty();
  }

  // If both query and category are empty, restore original results
  if (!query && !category) {
    restoreOriginalResults();
    return;
  }

  // normalizer: lowercase + strip diacritics for robust comparisons
  function normalizeText(s) {
    if (!s) return "";
    try {
      return s
        .toString()
        .trim()
        .toLowerCase()
        .normalize("NFD")
        .replace(/\p{Diacritic}/gu, "");
    } catch (e) {
      // fallback for environments without Unicode property escapes
      return s
        .toString()
        .trim()
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
    }
  }

  var queryNorm = normalizeText(query);
  var categoryNorm = normalizeText(category);

  // search across all product-item elements on the page (exclude those inside the results carousel)
  var items = $(".product-item").not(carousel.find(".product-item"));
  var found = 0;

  items.each(function () {
    var $p = $(this);
    var nameEl = $p.find(".product-name, h3, h5").first();
    var pname = normalizeTextGlobal(nameEl.text() || "");
    var itemCat = ($p.data("category") || "").toString().trim();
    var itemCatNorm = normalizeText(itemCat);
    // Nếu có category, chỉ lấy sản phẩm đúng category
    if (category && itemCatNorm !== categoryNorm) {
      return;
    }
    // Nếu có query, chỉ lấy sản phẩm đúng tên
    if (query && pname.indexOf(queryNorm) === -1) {
      return;
    }
    // clone and adapt to slide HTML
    var slideEl = $p.clone();
    slideEl.removeClass("col");
    slideEl.find(".input-number").val(1);
    slideEl.addClass("search-result-item");
    var slideHtml = $("<div>").append(slideEl).html();
    var wrapped = '<div class="swiper-slide">' + slideHtml + "</div>";
    if (useSwiperApi) {
      try {
        el.swiper.appendSlide(wrapped);
      } catch (e) {
        wrapper.append($(wrapped));
      }
    } else {
      wrapper.append($(wrapped));
    }
    found++;
  });

  if (found === 0) {
    var noResultsSlide =
      '<div class="swiper-slide no-results p-3">Không tìm thấy sản phẩm nào.</div>';
    if (useSwiperApi) {
      try {
        el.swiper.appendSlide(noResultsSlide);
      } catch (e) {
        wrapper.append($(noResultsSlide));
      }
    } else {
      wrapper.append($(noResultsSlide));
    }
  }

  // update or init swiper for this carousel
  if (carousel.length) {
    if (useSwiperApi) {
      try {
        el.swiper.update();
      } catch (e) {}
    } else {
      try {
        new Swiper(carousel[0], {
          slidesPerView: 4,
          spaceBetween: 20,
          breakpoints: {
            0: { slidesPerView: 1 },
            576: { slidesPerView: 2 },
            768: { slidesPerView: 3 },
            991: { slidesPerView: 4 },
          },
        });
      } catch (e) {}
    }
  }

  // scroll to the results section to make it visible
  $("html, body").animate(
    { scrollTop: header.closest("section").offset().top - 80 },
    300
  );
}

/* ======================= SHOPPING CART FUNCTIONALITY ======================= */

// Load cart from localStorage
function loadCart() {
  try {
    var raw = localStorage.getItem("sm_cart") || "[]";
    return JSON.parse(raw);
  } catch (e) {
    return [];
  }
}

// Save cart to localStorage
function saveCart(arr) {
  try {
    localStorage.setItem("sm_cart", JSON.stringify(arr));
  } catch (e) {}
}

// Get product data from product-item element
function getProductData($product) {
  var pid = getProductId($product);
  var imgSrc = $product.find("img").first().attr("src") || "";
  var name = $product.find(".product-name").first().text().trim() || "Sản phẩm";
  var priceText = $product.find(".price").first().text().trim() || "0";
  // Extract numeric value from price
  var price = priceText.replace(/[^\d]/g, "") || "0";

  return {
    id: pid,
    name: name,
    price: price,
    priceFormatted: priceText,
    image: imgSrc,
  };
}

// Add product to cart with quantity
function addToCart(productData, quantity) {
  var cart = loadCart();
  var qty = parseInt(quantity) || 1;

  // Check if product already exists in cart
  var existingIndex = -1;
  for (var i = 0; i < cart.length; i++) {
    if (cart[i].id === productData.id) {
      existingIndex = i;
      break;
    }
  }

  if (existingIndex !== -1) {
    // Update quantity if already in cart
    cart[existingIndex].quantity += qty;
  } else {
    // Add new item to cart
    cart.push({
      id: productData.id,
      name: productData.name,
      price: productData.price,
      priceFormatted: productData.priceFormatted,
      image: productData.image,
      quantity: qty,
    });
  }

  saveCart(cart);
  updateCartBadges();
  return cart;
}

// Remove product from cart
function removeFromCart(pid) {
  var cart = loadCart();
  var filtered = [];
  for (var i = 0; i < cart.length; i++) {
    if (cart[i].id !== pid) {
      filtered.push(cart[i]);
    }
  }
  saveCart(filtered);
  updateCartBadges();
}

// Update cart item quantity
function updateCartItemQuantity(pid, newQty) {
  var cart = loadCart();
  var qty = parseInt(newQty) || 1;
  if (qty < 1) qty = 1;

  for (var i = 0; i < cart.length; i++) {
    if (cart[i].id === pid) {
      cart[i].quantity = qty;
      break;
    }
  }
  saveCart(cart);
  updateCartBadges();
  renderCartIntoOffcanvas();
}

// Update cart count in header
function updateCartCount() {
  var cart = loadCart();
  var totalItems = 0;
  for (var i = 0; i < cart.length; i++) {
    totalItems += cart[i].quantity;
  }
  $(".cart-total").text(totalItems);
}

// Update all cart badges
function updateCartBadges() {
  updateCartCount();
  try {
    var cart = loadCart();
    var count = cart.length;
    $("#cart-count").text(count);
  } catch (e) {}
}

// Render cart items into offcanvas
function renderCartIntoOffcanvas() {
  var cart = loadCart();
  var $list = $("#cart-list");
  $list.empty();

  if (!cart || cart.length === 0) {
    $list.append(
      '<li class="list-group-item text-center text-muted">Chưa có sản phẩm</li>'
    );
    return;
  }

  var totalPrice = 0;

  for (var i = 0; i < cart.length; i++) {
    var item = cart[i];
    var itemTotal = parseInt(item.price) * item.quantity;
    totalPrice += itemTotal;

    var $li = $(
      '<li class="list-group-item d-flex align-items-center justify-content-between"></li>'
    );

    var $left = $("<div class='d-flex align-items-center gap-3'></div>");
    var $img = $("<img />").attr({ src: item.image, alt: item.name }).css({
      width: "64px",
      height: "64px",
      objectFit: "cover",
      borderRadius: "8px",
    });
    var $meta = $("<div></div>");
    $meta.append($("<div class='fw-semibold'></div>").text(item.name));
    $meta.append(
      $("<div class='small text-muted'></div>").text(
        item.priceFormatted + " x " + item.quantity
      )
    );
    $left.append($img).append($meta);

    var $right = $("<div class='d-flex flex-column gap-2'></div>");

    // Quantity controls
    var $qtyControls = $("<div class='d-flex align-items-center gap-1'></div>");
    var $minusBtn = $(
      "<button class='btn btn-sm btn-outline-secondary cart-qty-minus'>-</button>"
    ).attr("data-pid", item.id);
    var $qtyDisplay = $("<span class='px-2'></span>").text(item.quantity);
    var $plusBtn = $(
      "<button class='btn btn-sm btn-outline-secondary cart-qty-plus'>+</button>"
    ).attr("data-pid", item.id);
    $qtyControls.append($minusBtn).append($qtyDisplay).append($plusBtn);

    // Remove button
    var $removeBtn = $(
      "<button class='btn btn-sm btn-outline-danger cart-remove-item'>Xóa</button>"
    ).attr("data-pid", item.id);

    $right.append($qtyControls).append($removeBtn);

    $li.append($left).append($right);
    $list.append($li);
  }

  // Add total price display
  var totalFormatted = totalPrice
    .toString()
    .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  var $totalLi = $(
    '<li class="list-group-item d-flex justify-content-between"><strong>Tổng cộng:</strong><strong>' +
      totalFormatted +
      " ₫</strong></li>"
  );
  $list.append($totalLi);
}

// Initialize cart on page load
$(document).ready(function () {
  try {
    updateCartBadges();
  } catch (e) {}
});

// Handle add to cart button click
$(document).on("click", ".cart-icon, .fi-sr-shopping-cart-add", function (e) {
  e.preventDefault();
  e.stopPropagation();
  var $btn = $(this);
  var $product = $btn.closest(".product-item");
  if (!$product.length) return;

  var productData = getProductData($product);
  var $qtyInput = $product.find(".input-number");
  var quantity = parseInt($qtyInput.val()) || 1;

  addToCart(productData, quantity);

  // Show feedback
  var originalText = $btn.html();
  $btn.html('<i class="fi fi-sr-check"></i>');
  setTimeout(function () {
    $btn.html(originalText);
  }, 1000);

  // Reset quantity to 1
  $qtyInput.val(1);
});

// Handle cart quantity increase
$(document).on("click", ".cart-qty-plus", function (e) {
  e.preventDefault();
  var pid = $(this).data("pid");
  var cart = loadCart();
  for (var i = 0; i < cart.length; i++) {
    if (cart[i].id === pid) {
      updateCartItemQuantity(pid, cart[i].quantity + 1);
      break;
    }
  }
});

// Handle cart quantity decrease
$(document).on("click", ".cart-qty-minus", function (e) {
  e.preventDefault();
  var pid = $(this).data("pid");
  var cart = loadCart();
  for (var i = 0; i < cart.length; i++) {
    if (cart[i].id === pid) {
      var newQty = cart[i].quantity - 1;
      if (newQty >= 1) {
        updateCartItemQuantity(pid, newQty);
      }
      break;
    }
  }
});

// Handle remove item from cart
$(document).on("click", ".cart-remove-item", function (e) {
  e.preventDefault();
  var pid = $(this).data("pid");
  removeFromCart(pid);
  renderCartIntoOffcanvas();
});

// Open cart offcanvas and render content
var cartOffcanvas = null;
$(document).ready(function () {
  var el = document.getElementById("offcanvasCart");
  if (el) {
    try {
      cartOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(el);
    } catch (e) {
      try {
        cartOffcanvas = new bootstrap.Offcanvas(el);
      } catch (err) {}
    }
  }

  // Render cart when offcanvas is shown
  $("#offcanvasCart").on("show.bs.offcanvas", function () {
    renderCartIntoOffcanvas();
  });
});

// Search form - support both form IDs
['search-form', 'headerSearchForm'].forEach(function(formId) {
  var form = document.getElementById(formId);
  if (!form) return;
  
  // Form submit sẽ hoạt động bình thường (GET đến index.php)
  // Nếu muốn AJAX search, uncomment đoạn bên dưới
  
  /*
  form.addEventListener('submit', function(e){
    e.preventDefault();

    const keyword = form.querySelector('[name="query"]')?.value.trim() || '';
    const categoryId = form.querySelector('[name="category"]')?.value || '';

    fetch(`api/search_products.php?keyword=${encodeURIComponent(keyword)}&category_id=${categoryId}`)
      .then(r => r.json())
      .then(resp => {
        const wrapper = document.querySelector('#search-results-section .swiper-wrapper');
        if (!wrapper) return;
        wrapper.innerHTML = '';

        if (!resp || resp.status !== 'success' || resp.products.length === 0) {
          wrapper.innerHTML = `<div class="swiper-slide no-results p-3">Không tìm thấy sản phẩm</div>`;
          return;
        }

        resp.products.forEach(p => {
          const imgSrc = p.image || 'images/default-product.jpg';
          const price = Number(p.price) > 0 
            ? Number(p.price).toLocaleString('vi-VN') + ' ' + (p.currency || 'đ')
            : 'Thỏa thuận';
          
          wrapper.innerHTML += `
            <div class="swiper-slide">
              <div class="product-item border p-3 h-100" data-product-id="${p.id}">
                <figure class="text-center mb-3">
                  <a href="product.php?id=${p.id}">
                    <img src="${imgSrc}" class="tab-image" alt="${p.title}">
                  </a>
                </figure>
                <h3 class="product-name">${p.title}</h3>
                <span class="price d-block mt-2 text-success fw-bold">${price}</span>
              </div>
            </div>`;
        });

        if (window.searchResultsSwiper) window.searchResultsSwiper.update();
      });
  });
  */
});


