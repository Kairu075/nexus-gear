<?php
// includes/header.php — shared across all pages
?>
<header id="header">
  <nav class="nav-wrap">
    <a href="/nexus-gear/index.php" class="logo">
      <img src="/nexus-gear/images/logo.png" alt="Nexus Gear" onerror="this.style.display='none'">
      <span class="logo-text"><span class="logo-accent">NEXUS</span> GEAR</span>
    </a>

    <ul class="nav-links">
      <li><a href="/nexus-gear/index.php">Home</a></li>
      <li class="nav-dropdown">
        <a href="/nexus-gear/shop.php">Shop</a>
        <div class="dropdown-menu">
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
      <button class="nav-icon-btn" onclick="window.location='/nexus-gear/wishlist.php'" id="wishlistBtn" title="Wishlist">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </button>
      <button class="nav-icon-btn" id="cartBtn" title="Cart">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span class="badge hidden" id="cartBadge">0</span>
      </button>
      <div id="authBtns" class="flex gap-2">
        <button class="btn btn-outline btn-sm" data-open-auth="login">Sign In</button>
        <button class="btn btn-primary btn-sm" data-open-auth="register">Join Now</button>
      </div>
      <div class="user-menu hidden" id="userMenu">
        <img src="/nexus-gear/images/default-avatar.png" alt="User" class="user-avatar" id="userAvatarBtn">
        <div class="user-dropdown" id="userDropdown">
          <div class="user-dropdown-header">
            <div class="user-dropdown-name"></div>
            <div class="user-dropdown-email"></div>
          </div>
          <a href="/nexus-gear/profile.php"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> My Profile</a>
          <a href="/nexus-gear/orders.php"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg> My Orders</a>
          <a href="/nexus-gear/wishlist.php"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg> Wishlist</a>
          <a href="/nexus-gear/admin/index.php" class="admin-link hidden"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Admin Panel</a>
          <button class="logout-btn"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Sign Out</button>
        </div>
      </div>
    </div>
  </nav>
</header>
