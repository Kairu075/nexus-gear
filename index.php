<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Nexus Gear';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?></title>
<link rel="icon" href="/nexus-gear/images/logo.png">
<link rel="stylesheet" href="/nexus-gear/css/main.css">
</head>
<body>

<!-- ============ HEADER ============ -->
<header id="header">
  <nav class="nav-wrap">
    <a href="/nexus-gear/index.php" class="logo">
      <img src="/nexus-gear/images/logo.png" alt="Nexus Gear" onerror="this.style.display='none'">
      <span class="footer-logo-text"><span class="logo-accent">NEXUS</span> GEAR</span>
    </a>

    <ul class="nav-links">
      <li><a href="/nexus-gear/index.php" class="active">Home</a></li>
      <li class="nav-dropdown">
        <a href="/nexus-gear/shop.php">Shop</a>
        <div class="dropdown-menu" id="categoriesDropdown">
          <a href="/nexus-gear/shop.php?category=smartphones">Smartphones</a>
          <a href="/nexus-gear/shop.php?category=laptops">Laptops</a>
          <a href="/nexus-gear/shop.php?category=desktop-pcs">Desktop PCs</a>
          <a href="/nexus-gear/shop.php?category=pc-parts">PC Parts</a>
          <a href="/nexus-gear/shop.php?category=tablets">Tablets</a>
          <a href="/nexus-gear/shop.php?category=gaming-peripherals">Gaming Peripherals</a>
          <a href="/nexus-gear/shop.php?category=audio">Audio</a>
          <a href="/nexus-gear/shop.php?category=wearables">Wearables</a>
        </div>
      </li>
      <li><a href="/nexus-gear/shop.php?sort=popular">Best Sellers</a></li>
      <li><a href="/nexus-gear/shop.php?featured=1">Featured</a></li>
      <li><a href="/nexus-gear/shop.php?sale=1">Sale</a></li>
    </ul>

    <div class="search-wrapper">
      <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <input type="text" id="searchInput" class="search-input" placeholder="Search phones, laptops, gear...">
      <div id="searchResults" class="search-results hidden"></div>
    </div>

    <div class="nav-actions">
      <!-- Wishlist -->
      <button class="nav-icon-btn" onclick="window.location='/nexus-gear/wishlist.php'" id="wishlistBtn" title="Wishlist">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </button>

      <!-- Cart -->
      <button class="nav-icon-btn" id="cartBtn" title="Cart">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span class="badge hidden" id="cartBadge">0</span>
      </button>

      <!-- Auth Buttons -->
      <div id="authBtns" class="flex gap-2">
        <button class="btn btn-outline btn-sm" data-open-auth="login">Sign In</button>
        <button class="btn btn-primary btn-sm" data-open-auth="register">Join Now</button>
      </div>

      <!-- User Menu -->
      <div class="user-menu hidden" id="userMenu">
        <img src="/nexus-gear/images/default-avatar.png" alt="User" class="user-avatar" id="userAvatarBtn">
        <div class="user-dropdown" id="userDropdown">
          <div class="user-dropdown-header">
            <div class="user-dropdown-name"></div>
            <div class="user-dropdown-email"></div>
          </div>
          <a href="/nexus-gear/profile.php">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            My Profile
          </a>
          <a href="/nexus-gear/orders.php">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            My Orders
          </a>
          <a href="/nexus-gear/wishlist.php">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            Wishlist
          </a>
          <a href="/nexus-gear/admin/index.php" class="admin-link hidden">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Admin Panel
          </a>
          <button class="logout-btn">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Sign Out
          </button>
        </div>
      </div>
    </div>
  </nav>
</header>

<!-- ============ HERO ============ -->
<section class="hero" id="heroSection">
  <div class="hero-video-bg">
    <video autoplay muted loop playsinline id="heroVideo">
      <source src="/nexus-gear/videos/hero-montage.mp4" type="video/mp4">
    </video>
  </div>
 
  <div class="hero-glow hero-glow-1"></div>
  <div class="hero-glow hero-glow-2"></div>

  <div class="hero-content">
    <div class="hero-text">
      <h1 class="hero-title">
        <br>
        <span class="accent">NEXUS</span>
        GEAR<br>
        <span class="accent-orange">Gear Up<br>Level Up</span>
      </h1>
      <p class="hero-subtitle">
        Premium smartphones, laptops, gaming gear &amp;<br> PC components at a very low prices.
      </p>
      <div class="hero-actions">
        <a href="/nexus-gear/shop.php" class="btn btn-primary btn-lg">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          Shop Now
        </a>
        <a href="/nexus-gear/shop.php?featured=1" class="btn btn-outline btn-lg">
          View Featured
        </a>
      </div>
      <div class="hero-stats">
        <div>
          <div class="hero-stat-value">500+</div>
          <div class="hero-stat-label">Products</div>
        </div>
        <div>
          <div class="hero-stat-value">12K+</div>
          <div class="hero-stat-label">Customers</div>
        </div>
        <div>
          <div class="hero-stat-value">99%</div>
          <div class="hero-stat-label">Satisfaction</div>
        </div>
      </div>
    </div>
  </div>

  <div class="scroll-indicator">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    <span style="font-size:10px;letter-spacing:3px">SCROLL</span>
  </div>
</section>

<!-- ============ CATEGORY SECTION ============ -->
<section style="padding:80px 0">
  <div class="container">
    <div class="section-header flex justify-between items-center">
      <div>
        <div class="section-eyebrow">BROWSE BY CATEGORY</div>
        <h2 class="section-title">Shop by <span class="accent">Category</span></h2>
      </div>
      <a href="/nexus-gear/shop.php" class="btn btn-outline">All Categories</a>
    </div>
    <div class="category-grid" id="categoryGrid">
      <!-- Loaded via JS -->
      <?php for($i=0;$i<10;$i++): ?>
      <div class="skeleton" style="height:140px;border-radius:16px"></div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<div class="section-divider"></div>

<!-- ============ FEATURED PRODUCTS ============ -->
<section>
  <div class="container">
    <div class="section-header flex justify-between items-center">
      <div>
        <div class="section-eyebrow">JUST DROPPED</div>
        <h2 class="section-title">Featured <span class="accent">Products</span></h2>
        <p class="section-subtitle">The latest and greatest gear, handpicked for you.</p>
      </div>
      <a href="/nexus-gear/shop.php?featured=1" class="btn btn-outline">View All</a>
    </div>
    <div class="product-grid" id="featuredProducts">
      <?php for($i=0;$i<8;$i++): ?>
      <div class="skeleton" style="height:380px;border-radius:16px"></div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<div class="section-divider"></div>

<!-- ============ PROMO BANNER ============ -->
<section style="padding:60px 0">
  <div class="container">
    <div style="background:linear-gradient(135deg,rgba(232,25,44,0.07) 0%,rgba(232,25,44,0.03) 100%);border:1px solid var(--border-med);border-radius:24px;padding:60px;display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center;position:relative;overflow:hidden">
      <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(232,25,44,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(232,25,44,0.03) 1px,transparent 1px);background-size:40px 40px;pointer-events:none"></div>
      <div style="position:relative">
        <div class="section-eyebrow">LIMITED TIME</div>
        <h2 style="font-size:2.5rem;margin-bottom:16px">Use code <span style="color:var(--amber);font-family:'Syne',sans-serif">NEXUS20</span> for <span class="accent">20% OFF</span></h2>
        <p style="color:var(--text-secondary);margin-bottom:28px">On orders over ₱2,000. Valid for new customers. Limited slots only.</p>
        <a href="/nexus-gear/shop.php" class="btn btn-primary btn-lg">Shop the Sale</a>
      </div>
      <div style="text-align:center;position:relative">
        <div style="font-family:'Syne',sans-serif;font-size:80px;font-weight:900;line-height:1;background:linear-gradient(135deg,var(--red),var(--red-hover));-webkit-background-clip:text;-webkit-text-fill-color:transparent">20%<br><span style="font-size:40px">OFF</span></div>
      </div>
    </div>
  </div>
</section>

<!-- ============ BEST SELLERS ============ -->
<section>
  <div class="container">
    <div class="section-header flex justify-between items-center">
      <div>
        <div class="section-eyebrow">CROWD FAVORITES</div>
        <h2 class="section-title">Best <span class="accent">Sellers</span></h2>
        <p class="section-subtitle">Top-rated products loved by thousands of customers.</p>
      </div>
      <a href="/nexus-gear/shop.php?sort=popular" class="btn btn-outline">View All</a>
    </div>
    <div class="product-grid" id="bestSellerProducts">
      <?php for($i=0;$i<8;$i++): ?>
      <div class="skeleton" style="height:380px;border-radius:16px"></div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<div class="section-divider"></div>

<!-- ============ BRANDS ============ -->
<section>
  <div class="container">
    <div class="section-header">
      <div class="section-eyebrow">OFFICIAL PARTNERS</div>
      <h2 class="section-title">Shop by <span class="accent">Brand</span></h2>
    </div>
    <div class="brand-grid" id="brandGrid">
      <?php for($i=0;$i<12;$i++): ?>
      <div class="skeleton" style="height:90px;border-radius:12px"></div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<div class="section-divider"></div>

<!-- ============ PHONES SECTION ============ -->
<section class="scroll-section">
  <div class="container">
    <div class="section-header flex justify-between items-center">
      <div>
        <div class="section-eyebrow">LATEST HANDHELDS</div>
        <h2 class="section-title">Smartphones</h2>
      </div>
      <a href="/nexus-gear/shop.php?category=smartphones" class="btn btn-outline">See All</a>
    </div>
    <div class="scroll-track" id="phonesSection">
      <?php for($i=0;$i<5;$i++): ?>
      <div class="skeleton" style="flex:0 0 260px;height:380px;border-radius:16px"></div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- ============ LAPTOPS SECTION ============ -->
<section class="scroll-section">
  <div class="container">
    <div class="section-header flex justify-between items-center">
      <div>
        <div class="section-eyebrow">PERFORMANCE MACHINES</div>
        <h2 class="section-title">Laptops</h2>
      </div>
      <a href="/nexus-gear/shop.php?category=laptops" class="btn btn-outline">See All</a>
    </div>
    <div class="scroll-track" id="laptopsSection">
      <?php for($i=0;$i<5;$i++): ?>
      <div class="skeleton" style="flex:0 0 260px;height:380px;border-radius:16px"></div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- ============ GAMING SECTION ============ -->
<section class="scroll-section">
  <div class="container">
    <div class="section-header flex justify-between items-center">
      <div>
        <div class="section-eyebrow">LEVEL UP YOUR SETUP</div>
        <h2 class="section-title">Gaming Peripherals</h2>
      </div>
      <a href="/nexus-gear/shop.php?category=gaming-peripherals" class="btn btn-outline">See All</a>
    </div>
    <div class="scroll-track" id="gamingSection">
      <?php for($i=0;$i<5;$i++): ?>
      <div class="skeleton" style="flex:0 0 260px;height:380px;border-radius:16px"></div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<div class="section-divider"></div>

<!-- ============ WHY NEXUS GEAR ============ -->
<section>
  <div class="container">
    <div class="section-header" style="text-align:center">
      <div class="section-eyebrow" style="justify-content:center">WHY CHOOSE US</div>
      <h2 class="section-title">Why <span class="accent">Nexus Gear?</span></h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px">
      <?php
      $features = [
        ['icon'=>'shield','color'=>'var(--red)','title'=>'Authentic Products','desc'=>'100% genuine products sourced directly from official distributors and brands.'],
        ['icon'=>'truck','color'=>'var(--amber)','title'=>'Fast Delivery','desc'=>'Nationwide delivery with real-time order tracking and status updates.'],
        ['icon'=>'headphones','color'=>'var(--green)','title'=>'24/7 Support','desc'=>'Our expert tech team is always ready to help you find the perfect gear.'],
        ['icon'=>'refresh','color'=>'#a78bfa','title'=>'Easy Returns','desc'=>'Hassle-free 7-day returns and exchanges on all eligible products.'],
      ];
      foreach($features as $f): ?>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:16px;padding:32px 24px;text-align:center;transition:var(--transition)" onmouseover="this.style.borderColor='var(--border-med)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='';this.style.transform=''">
        <div style="width:64px;height:64px;background:<?= $f['color'] ?>22;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;border:1px solid <?= $f['color'] ?>44">
          <svg width="28" height="28" fill="none" stroke="<?= $f['color'] ?>" stroke-width="2" viewBox="0 0 24 24">
            <?php if($f['icon']==='shield'): ?><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <?php elseif($f['icon']==='truck'): ?><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
            <?php elseif($f['icon']==='headphones'): ?><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
            <?php else: ?><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.49-3.91"/>
            <?php endif; ?>
          </svg>
        </div>
        <h3 style="font-size:18px;margin-bottom:10px"><?= $f['title'] ?></h3>
        <p style="color:var(--text-secondary);font-size:14px;line-height:1.7"><?= $f['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ FOOTER ============ -->
<footer>
  <div class="container">
    <div class="footer-grid">
      <div class="footer-about">
        <a href="/nexus-gear/index.php" class="logo">
          <img src="/nexus-gear/images/logo.png" alt="Nexus Gear" style="height:30px" onerror="this.style.display='none'">
          <span class="footer-logo-text"><span class="logo-accent">NEXUS</span> GEAR</span>
        </a>
        <p>Your trusted destination for premium tech and gadgets in the Philippines. Authentic products, competitive prices, and exceptional service.</p>
        <div class="footer-social" style="margin-top:20px">
          <a href="#" class="social-btn" title="Facebook">f</a>
          <a href="#" class="social-btn" title="Instagram">in</a>
          <a href="#" class="social-btn" title="TikTok">tk</a>
          <a href="#" class="social-btn" title="YouTube">yt</a>
        </div>
      </div>
      <div>
        <div class="footer-col-title">Shop</div>
        <ul class="footer-links">
          <li><a href="/nexus-gear/shop.php?category=smartphones">Smartphones</a></li>
          <li><a href="/nexus-gear/shop.php?category=laptops">Laptops</a></li>
          <li><a href="/nexus-gear/shop.php?category=desktop-pcs">Desktop PCs</a></li>
          <li><a href="/nexus-gear/shop.php?category=gaming-peripherals">Gaming</a></li>
          <li><a href="/nexus-gear/shop.php?category=pc-parts">PC Parts</a></li>
          <li><a href="/nexus-gear/shop.php?sale=1">Sale Items</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-col-title">Account</div>
        <ul class="footer-links">
          <li><a href="/nexus-gear/profile.php">My Profile</a></li>
          <li><a href="/nexus-gear/orders.php">My Orders</a></li>
          <li><a href="/nexus-gear/wishlist.php">Wishlist</a></li>
          <li><a href="#" data-open-auth="login">Sign In</a></li>
          <li><a href="#" data-open-auth="register">Create Account</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-col-title">Support</div>
        <ul class="footer-links">
          <li><a href="#">FAQs</a></li>
          <li><a href="#">Contact Us</a></li>
          <li><a href="#">Shipping Policy</a></li>
          <li><a href="#">Return Policy</a></li>
          <li><a href="#">Privacy Policy</a></li>
        </ul>
        <div style="margin-top:20px;padding:16px;background:rgba(255,255,255,0.06);border-radius:10px;border:1px solid rgba(255,255,255,0.08)">
          <div style="font-size:11px;color:rgba(255,255,255,0.35);margin-bottom:4px;text-transform:uppercase;letter-spacing:.08em;font-weight:700">NEED HELP?</div>
          <div style="font-size:14px;font-weight:600;color:var(--red)">support@nexusgear.ph</div>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?= date('Y') ?> Nexus Gear — Created by Kyle Dominic Yap.<br>
    Disclaimer: This website is a sample project for educational and portfolio purposes only.<br>
     It does not sell real products and has no affiliation, partnership, or endorsement with any brands, companies, or trademarks featured.
</span>
      <span>Powered by <span style="color:var(--red)">Nexus Tech</span></span>
    </div>
  </div>
</footer>

<!-- ============ CART SIDEBAR ============ -->
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-sidebar" id="cartSidebar">
  <div class="cart-header">
    <span class="cart-title">YOUR CART</span>
    <button class="cart-close" id="cartClose">✕</button>
  </div>
  <div class="cart-items" id="cartItems">
    <div class="cart-empty">
      <p>Loading cart...</p>
    </div>
  </div>
  <div class="cart-footer">
    <div class="cart-total-row">
      <span class="cart-total-label">Total</span>
      <span class="cart-total-value" id="cartTotal">₱0.00</span>
    </div>
    <a href="/nexus-gear/checkout.php" class="btn btn-primary" style="width:100%;justify-content:center" id="cartCheckoutBtn">
      Proceed to Checkout
    </a>
    <button onclick="closeModal('cartSidebar')" class="btn btn-ghost" style="width:100%;justify-content:center;margin-top:8px" onclick="document.getElementById('cartOverlay').classList.remove('open');document.getElementById('cartSidebar').classList.remove('open')">
      Continue Shopping
    </button>
  </div>
</div>

<!-- ============ AUTH MODAL ============ -->
<div class="modal-overlay" id="authModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">NEXUS GEAR ACCESS</span>
      <button class="modal-close" data-close-modal="authModal">✕</button>
    </div>
    <div class="modal-body">
      <div class="auth-tabs">
        <button class="auth-tab active" data-tab="login">Sign In</button>
        <button class="auth-tab" data-tab="register">Create Account</button>
      </div>

      <!-- Login -->
      <div id="loginPanel" class="auth-panel">
        <form id="loginForm">
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-input" placeholder="your@email.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-input" placeholder="••••••••" required>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">Sign In</button>
        </form>
        <p style="text-align:center;margin-top:16px;font-size:13px;color:var(--text-dim)">
          Don't have an account? <button class="auth-tab" data-tab="register" style="color:var(--red);background:none;border:none;cursor:pointer;font-size:13px">Create one</button>
        </p>
      </div>

      <!-- Register -->
      <div id="registerPanel" class="auth-panel hidden">
        <form id="registerForm">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-input" placeholder="Juan dela Cruz" required>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-input" placeholder="your@email.com" required>
            </div>
            <div class="form-group">
              <label class="form-label">Phone</label>
              <input type="tel" name="phone" class="form-input" placeholder="09xxxxxxxxx">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-input" placeholder="Min. 6 chars" required>
            </div>
            <div class="form-group">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="confirm_password" class="form-input" placeholder="Repeat password" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">Create Account</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ============ CONFIRM MODAL ============ -->
<div class="modal-overlay" id="confirmModal">
  <div class="modal" style="max-width:380px">
    <div class="modal-header">
      <span class="modal-title confirm-title">Confirm Action</span>
      <button class="modal-close" data-close-modal="confirmModal">✕</button>
    </div>
    <div class="modal-body">
      <p class="confirm-message" style="color:var(--text-secondary);font-size:14px"></p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" data-close-modal="confirmModal">Cancel</button>
      <button class="btn btn-danger confirm-yes">Confirm</button>
    </div>
  </div>
</div>

<!-- Toast container -->
<div class="toast-container" id="toastContainer"></div>

<script src="/nexus-gear/js/app.js"></script>
</body>
</html>