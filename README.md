# Nexus Gear — Full-Stack Online Gadget Store

## Tech Stack
- **Backend**: PHP 8+ with PDO (MySQL)
- **Database**: MySQL 8+
- **Frontend**: Vanilla JS + Custom CSS (Dark Tech Theme)
- **Fonts**: Orbitron, Rajdhani, Inter (Google Fonts)
- **Maps**: Leaflet.js (OpenStreetMap)

---

## Setup

### 1. Configure Database
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_mysql_user');
define('DB_PASS', 'your_mysql_password');
define('DB_NAME', 'nexus_gear');
define('SITE_URL', 'http://localhost/nexus-gear');
```


### 2. Access the Store
- **Storefront**: http://localhost/nexus-gear/
- **Admin Panel**: http://localhost/nexus-gear/admin/


> Update via Admin Panel → Users, or run:
> `UPDATE users SET password = '$2y$10$...' WHERE email = 'admin@nexusgear.ph';`

---

## Project Structure
```
nexus-gear/
├── index.php              — Homepage
├── shop.php               — Shop / Product listing
├── product.php            — Product detail page
├── checkout.php           — Checkout page
├── orders.php             — Customer orders
├── profile.php            — User profile + map
├── wishlist.php           — Wishlist
├── database.sql           — Database schema + seed data
│
├── admin/
│   └── index.php          — Admin panel (all features)
│
├── includes/
│   ├── config.php         — DB config + helpers
│   ├── header.php         — Shared nav header
│   └── footer-common.php  — Shared cart, modals, footer
│
├── php/
│   └── api.php            — Central REST-like API
│
├── css/
│   └── main.css           — Full dark tech stylesheet
│
├── js/
│   └── app.js             — Frontend application logic
│
├── images/
│   ├── logo.png           — YOUR LOGO (place here)
│   ├── no-image.png       — Fallback product image
│   ├── default-avatar.png — Default user avatar
│   ├── hero-product.png   — Hero section product
│   ├── categories/        — Category icons
│   └── brands/            — Brand logos
│
├── uploads/
│   ├── products/          — Product photos (admin upload)
│   └── avatars/           — User profile photos
│
└── videos/
    └── hero-montage.mp4   — Hero background video
```

---

## Features Summary

### Customer Features
- Browse products by category, brand, search
- Filter by price range, rating, availability
- Product detail with image gallery, specs, reviews
- Add to cart (sidebar drawer)
- Wishlist
- Checkout with COD, GCash, Credit Card
- Discount code support
- Order tracking with progress steps
- Profile management with map pin
- Product reviews (verified purchasers only)

### Admin Features
- Dashboard with revenue chart, stats, low stock alerts
- Product management (CRUD + multi-image upload)
- Category & brand management
- Order management with status updates + auto stock deduction
- User management (suspend/restore)
- Revenue analytics & top products
- Discount code management
- Review moderation

---

## Discount Codes (Seeded)
| Code | Type | Value | Min Order |
|------|------|-------|-----------|
| NEXUS10 | % | 10% | ₱500 |
| NEXUS20 | % | 20% | ₱2,000 |
| SAVE500 | Fixed | ₱500 | ₱5,000 |
| WELCOME | % | 15% | ₱0 |

# © 2026 Nexus Gear | Developed by Kyle Dominic Yap
