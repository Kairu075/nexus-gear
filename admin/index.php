<?php
require_once __DIR__ . '/../includes/config.php';
if (!isAdmin()) { header('Location: /nexus-gear/index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Panel — Nexus Gear</title>
<link rel="icon" href="/nexus-gear/images/logo.png">
<link rel="stylesheet" href="/nexus-gear/css/main.css">
<style>
.chart-bar-wrap{display:flex;align-items:flex-end;gap:8px;height:180px;padding:16px 0}
.chart-bar-col{flex:1;display:flex;flex-direction:column;align-items:center;gap:6px}
.chart-bar{width:100%;background:linear-gradient(180deg,var(--red) 0%,rgba(232,25,44,0.25) 100%);border-radius:4px 4px 0 0;min-height:4px;transition:height 1s ease;position:relative}
.chart-bar:hover::after{content:attr(data-value);position:absolute;top:-28px;left:50%;transform:translateX(-50%);background:var(--navy);color:#fff;padding:4px 8px;border-radius:6px;font-size:11px;white-space:nowrap;font-weight:700}
.chart-label{font-size:10px;color:var(--text-secondary);text-align:center;font-weight:600}
.low-stock-item{display:flex;align-items:center;gap:12px;padding:12px;background:rgba(232,25,44,0.04);border:1px solid rgba(232,25,44,0.14);border-radius:var(--radius);margin-bottom:8px}
.admin-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:24px}
.product-img-grid-wrap{display:flex;flex-wrap:wrap;gap:12px;min-height:40px;align-items:flex-start}
</style>
</head>
<body>

<div class="admin-wrap">
  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <div class="admin-sidebar-logo">
      <span style="color:var(--red)">NEXUS</span> ADMIN
    </div>
    <nav class="admin-nav">
      <div class="admin-nav-section">
        <div class="admin-nav-label">Overview</div>
        <button type="button" class="admin-nav-link active" onclick="showPanel('dashboard',this)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          Dashboard
        </button>
      </div>
      <div class="admin-nav-section">
        <div class="admin-nav-label">Catalog</div>
        <button type="button" class="admin-nav-link" onclick="showPanel('products',this)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          Products
        </button>
        <button type="button" class="admin-nav-link" onclick="showPanel('categories',this)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          Categories
        </button>
      </div>
      <div class="admin-nav-section">
        <div class="admin-nav-label">Sales</div>
        <button type="button" class="admin-nav-link" onclick="showPanel('orders',this)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          Orders
        </button>
        <button type="button" class="admin-nav-link" onclick="showPanel('revenue',this)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          Revenue
        </button>
        <button type="button" class="admin-nav-link" onclick="showPanel('discounts',this)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
          Discount Codes
        </button>
      </div>
      <div class="admin-nav-section">
        <div class="admin-nav-label">Users</div>
        <button type="button" class="admin-nav-link" onclick="showPanel('users',this)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Users
        </button>
        <button type="button" class="admin-nav-link" onclick="showPanel('reviews',this)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          Reviews
        </button>
      </div>
      <div class="admin-nav-section" style="margin-top:auto;padding-top:20px">
        <a href="/nexus-gear/index.php" class="admin-nav-link">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          Storefront
        </a>
        <button type="button" class="admin-nav-link logout-btn" style="color:var(--red)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Sign Out
        </button>
      </div>
    </nav>
  </aside>

  <!-- Main content -->
  <main class="admin-main">
    <div class="admin-topbar">
      <div class="admin-page-title" id="adminPageTitle">DASHBOARD</div>
      <div style="display:flex;align-items:center;gap:12px">
        <div style="font-size:13px;color:var(--text-secondary)">Welcome, <span style="color:var(--red)"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span></div>
      </div>
    </div>

    <!-- ====== DASHBOARD PANEL ====== -->
    <div id="panel-dashboard">
      <div class="stats-grid" id="statsGrid">
        <div class="skeleton" style="height:110px;border-radius:16px"></div>
        <div class="skeleton" style="height:110px;border-radius:16px"></div>
        <div class="skeleton" style="height:110px;border-radius:16px"></div>
        <div class="skeleton" style="height:110px;border-radius:16px"></div>
      </div>
      <div class="admin-grid-2">
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px">
          <div style="font-family:'Syne',sans-serif;font-size:11px;color:var(--red);letter-spacing:2px;margin-bottom:16px">MONTHLY REVENUE</div>
          <div class="chart-bar-wrap" id="revenueChart"><div class="spinner"></div></div>
        </div>
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px">
          <div style="font-family:'Syne',sans-serif;font-size:11px;color:var(--red);letter-spacing:2px;margin-bottom:16px">LOW STOCK ALERTS</div>
          <div id="lowStockList"><div class="spinner"></div></div>
        </div>
      </div>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;margin-top:24px">
        <div style="font-family:'Syne',sans-serif;font-size:11px;color:var(--red);letter-spacing:2px;margin-bottom:16px">RECENT ORDERS</div>
        <div id="recentOrdersList"><div class="spinner"></div></div>
      </div>
    </div>

    <!-- ====== PRODUCTS PANEL ====== -->
    <div id="panel-products" class="hidden">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <input type="text" class="form-input" style="max-width:280px" placeholder="Search products..." oninput="loadAdminProducts(this.value)" id="productSearch">
        <button type="button" class="btn btn-primary" onclick="openProductModal(null)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add Product
        </button>
      </div>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
        <table class="data-table" id="productsTable">
          <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="productsBody"><tr><td colspan="6" style="text-align:center;padding:40px"><div class="spinner" style="margin:0 auto"></div></td></tr></tbody>
        </table>
      </div>
    </div>

    <!-- ====== CATEGORIES PANEL ====== -->
    <div id="panel-categories" class="hidden">
      <div style="display:flex;justify-content:flex-end;margin-bottom:20px">
        <button type="button" class="btn btn-primary" onclick="openCategoryModal(null)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add Category
        </button>
      </div>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
        <table class="data-table">
          <thead><tr><th>Name</th><th>Slug</th><th>Products</th><th>Actions</th></tr></thead>
          <tbody id="categoriesBody"><tr><td colspan="4" style="text-align:center;padding:40px"><div class="spinner" style="margin:0 auto"></div></td></tr></tbody>
        </table>
      </div>
    </div>

    <!-- ====== ORDERS PANEL ====== -->
    <div id="panel-orders" class="hidden">
      <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap">
        <?php foreach(['','pending','processing','approved','shipped','delivered','cancelled'] as $s): ?>
        <button type="button" class="btn btn-ghost btn-sm order-filter-btn <?= $s===''?'active':'' ?>" data-status="<?=$s?>" onclick="filterOrders('<?=$s?>',this)">
          <?= $s ? ucfirst($s) : 'All' ?>
        </button>
        <?php endforeach; ?>
      </div>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
        <table class="data-table">
          <thead><tr><th>Order #</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
          <tbody id="ordersBody"><tr><td colspan="8" style="text-align:center;padding:40px"><div class="spinner" style="margin:0 auto"></div></td></tr></tbody>
        </table>
      </div>
    </div>

    <!-- ====== REVENUE PANEL ====== -->
    <div id="panel-revenue" class="hidden">
      <div class="admin-grid-2" style="margin-bottom:24px">
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px">
          <div style="font-family:'Syne',sans-serif;font-size:11px;color:var(--red);letter-spacing:2px;margin-bottom:16px">MONTHLY REVENUE CHART</div>
          <div class="chart-bar-wrap" id="fullRevenueChart"><div class="spinner"></div></div>
        </div>
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px">
          <div style="font-family:'Syne',sans-serif;font-size:11px;color:var(--amber);letter-spacing:2px;margin-bottom:16px">TOP SELLING PRODUCTS</div>
          <div id="topProductsList"><div class="spinner"></div></div>
        </div>
      </div>
    </div>

    <!-- ====== DISCOUNTS PANEL ====== -->
    <div id="panel-discounts" class="hidden">
      <div style="display:flex;justify-content:flex-end;margin-bottom:20px">
        <button type="button" class="btn btn-primary" onclick="openDiscountModal(null)">Add Discount Code</button>
      </div>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
        <table class="data-table">
          <thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Used</th><th>Expires</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="discountsBody"><tr><td colspan="8" style="text-align:center;padding:40px"><div class="spinner" style="margin:0 auto"></div></td></tr></tbody>
        </table>
      </div>
    </div>

    <!-- ====== USERS PANEL ====== -->
    <div id="panel-users" class="hidden">
      <div style="margin-bottom:20px">
        <input type="text" class="form-input" style="max-width:280px" placeholder="Search users..." oninput="loadAdminUsers(this.value)">
      </div>
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
        <table class="data-table">
          <thead><tr><th>User</th><th>Email</th><th>Phone</th><th>Orders</th><th>Joined</th><th>Status</th><th>Action</th></tr></thead>
          <tbody id="usersBody"><tr><td colspan="7" style="text-align:center;padding:40px"><div class="spinner" style="margin:0 auto"></div></td></tr></tbody>
        </table>
      </div>
    </div>

    <!-- ====== REVIEWS PANEL ====== -->
    <div id="panel-reviews" class="hidden">
      <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
        <table class="data-table">
          <thead><tr><th>User</th><th>Product</th><th>Rating</th><th>Review</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
          <tbody id="reviewsBody"><tr><td colspan="7" style="text-align:center;padding:40px"><div class="spinner" style="margin:0 auto"></div></td></tr></tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<!-- Product Modal -->
<div class="modal-overlay" id="productModal">
  <div class="modal modal-xl">
    <div class="modal-header">
      <span class="modal-title" id="productModalTitle">ADD PRODUCT</span>
      <button type="button" class="modal-close" data-close-modal="productModal">✕</button>
    </div>
    <div class="modal-body">
      <form id="productForm">
        <input type="hidden" name="id" id="productId">
        <div class="form-row">
          <div class="form-group" style="grid-column:1/-1"><label class="form-label">Product Name</label><input type="text" name="name" class="form-input" required placeholder="e.g. iPhone 15 Pro Max 256GB"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Category</label><select name="category_id" class="form-select" id="productCategory"><option value="">Select category</option></select></div>
          <div class="form-group"><label class="form-label">Brand</label><select name="brand_id" class="form-select" id="productBrand"><option value="">Select brand</option></select></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Price (₱)</label><input type="number" name="price" class="form-input" step="0.01" min="0" required placeholder="0.00"></div>
          <div class="form-group"><label class="form-label">Original Price (₱)</label><input type="number" name="original_price" class="form-input" step="0.01" min="0" placeholder="0.00"></div>
          <div class="form-group"><label class="form-label">Stock</label><input type="number" name="stock" class="form-input" min="0" required placeholder="0"></div>
        </div>
        <div class="form-group"><label class="form-label">Short Description</label><input type="text" name="short_description" class="form-input" placeholder="Brief summary (shown in cards)"></div>
        <div class="form-group"><label class="form-label">Full Description</label><textarea name="description" class="form-textarea" style="min-height:120px" placeholder="Full product description..."></textarea></div>
        <div class="form-group"><label class="form-label">Specifications (JSON format)</label><textarea name="specifications" class="form-textarea" style="min-height:80px;font-family:monospace;font-size:12px" placeholder='{"Processor":"Apple A17 Pro","RAM":"8GB","Storage":"256GB"}'></textarea></div>
        <div class="form-row">
          <div class="form-group"><label style="display:flex;align-items:center;gap:10px;cursor:pointer"><input type="checkbox" name="is_featured" value="1" style="accent-color:var(--red)"> <span class="form-label" style="margin:0">Featured Product</span></label></div>
          <div class="form-group"><label style="display:flex;align-items:center;gap:10px;cursor:pointer"><input type="checkbox" name="is_best_seller" value="1" style="accent-color:var(--red)"> <span class="form-label" style="margin:0">Best Seller</span></label></div>
        </div>

        <!-- Image upload section -->
        <div id="productImageSection" class="hidden">
          <div style="border-top:1px solid var(--border);padding-top:20px;margin-top:4px">
            <div class="form-label" style="margin-bottom:12px">PRODUCT IMAGES</div>
            <div id="productImagesGrid" style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:16px"></div>
            <div style="display:flex;gap:12px;align-items:center">
              <label style="cursor:pointer">
                <input type="file" id="imageUploadInput" accept="image/*" multiple class="hidden" onchange="uploadProductImages(this)">
                <div class="btn btn-outline btn-sm">Upload Images</div>
              </label>
              <span style="font-size:12px;color:var(--text-secondary)">JPG, PNG, WEBP — Max 5MB each</span>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-ghost" data-close-modal="productModal">Cancel</button>
      <button type="button" class="btn btn-primary" onclick="saveProduct()">Save Product</button>
    </div>
  </div>
</div>

<!-- Order Update Modal -->
<div class="modal-overlay" id="orderModal">
  <div class="modal modal-lg">
    <div class="modal-header">
      <span class="modal-title">ORDER DETAILS</span>
      <button type="button" class="modal-close" data-close-modal="orderModal">✕</button>
    </div>
    <div class="modal-body" id="orderModalBody"></div>
    <div class="modal-footer">
      <button type="button" class="btn btn-ghost" data-close-modal="orderModal">Close</button>
      <select id="orderStatusSelect" class="form-select" style="max-width:180px">
        <option value="pending">Pending</option>
        <option value="processing">Processing</option>
        <option value="approved">Approved</option>
        <option value="shipped">Shipped</option>
        <option value="delivered">Delivered</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <button type="button" class="btn btn-primary" onclick="updateOrderStatus()">Update Status</button>
    </div>
  </div>
</div>

<!-- Category Modal -->
<div class="modal-overlay" id="categoryModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title" id="categoryModalTitle">ADD CATEGORY</span>
      <button type="button" class="modal-close" data-close-modal="categoryModal">✕</button>
    </div>
    <div class="modal-body">
      <form id="categoryForm">
        <input type="hidden" name="id" id="categoryId">
        <div class="form-group"><label class="form-label">Category Name</label><input type="text" name="name" class="form-input" required></div>
        <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-textarea"></textarea></div>
        <div class="form-group"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-input" value="0"></div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-ghost" data-close-modal="categoryModal">Cancel</button>
      <button type="button" class="btn btn-primary" onclick="saveCategory()">Save</button>
    </div>
  </div>
</div>

<!-- Discount Modal -->
<div class="modal-overlay" id="discountModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title" id="discountModalTitle">ADD DISCOUNT CODE</span>
      <button type="button" class="modal-close" data-close-modal="discountModal">✕</button>
    </div>
    <div class="modal-body">
      <form id="discountForm">
        <input type="hidden" name="id" id="discountId">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Code</label><input type="text" name="code" class="form-input" required placeholder="NEXUS10" style="text-transform:uppercase"></div>
          <div class="form-group"><label class="form-label">Type</label><select name="type" class="form-select"><option value="percentage">Percentage (%)</option><option value="fixed">Fixed Amount (₱)</option></select></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Value</label><input type="number" name="value" class="form-input" step="0.01" min="0" required placeholder="e.g. 10 for 10%"></div>
          <div class="form-group"><label class="form-label">Min Order (₱)</label><input type="number" name="min_order" class="form-input" step="0.01" min="0" value="0"></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Max Uses</label><input type="number" name="max_uses" class="form-input" min="1" placeholder="Unlimited"></div>
          <div class="form-group"><label class="form-label">Expires At</label><input type="datetime-local" name="expires_at" class="form-input"></div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-ghost" data-close-modal="discountModal">Cancel</button>
      <button type="button" class="btn btn-primary" onclick="saveDiscount()">Save Code</button>
    </div>
  </div>
</div>

<!-- Confirm Modal -->
<div class="modal-overlay" id="confirmModal">
  <div class="modal" style="max-width:380px">
    <div class="modal-header"><span class="modal-title confirm-title">Confirm</span><button type="button" class="modal-close" data-close-modal="confirmModal">✕</button></div>
    <div class="modal-body"><p class="confirm-message" style="color:var(--text-primary);font-size:14px"></p></div>
    <div class="modal-footer"><button type="button" class="btn btn-ghost" data-close-modal="confirmModal">Cancel</button><button type="button" class="btn btn-danger confirm-yes">Confirm</button></div>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script src="/nexus-gear/js/app.js"></script>
<script>
const ADMIN_API = '/nexus-gear/php/api.php';
let currentOrderId = null;
let categories = [], brands = [];

function showPanel(name, btn) {
  document.querySelectorAll('[id^="panel-"]').forEach(p=>p.classList.add('hidden'));
  document.querySelectorAll('.admin-nav-link').forEach(b=>b.classList.remove('active'));
  document.getElementById('panel-'+name).classList.remove('hidden');
  document.getElementById('adminPageTitle').textContent = name.toUpperCase();
  if(btn) btn.classList.add('active');
  const loaders = { dashboard:loadDashboard, products:loadAdminProducts, categories:loadAdminCategories, orders:loadAdminOrders, revenue:loadRevenue, discounts:loadDiscounts, users:loadAdminUsers, reviews:loadAdminReviews };
  loaders[name]?.();
}

// DASHBOARD
async function loadDashboard() {
  const data = await fetch(`${ADMIN_API}?action=admin_get_dashboard`,{credentials:'include'}).then(r=>r.json());
  if (!data.success) return;
  const d = data.data;
  document.getElementById('statsGrid').innerHTML = `
    <div class="stat-card cyan"><div class="stat-label">Total Products</div><div class="stat-value">${d.total_products}</div></div>
    <div class="stat-card amber"><div class="stat-label">Total Orders</div><div class="stat-value">${d.total_orders}</div><div class="stat-trend" style="color:var(--amber)">${d.pending_orders} pending</div></div>
    <div class="stat-card green"><div class="stat-label">Total Revenue</div><div class="stat-value" style="font-size:22px">₱${parseFloat(d.total_revenue).toLocaleString('en-PH',{minimumFractionDigits:0})}</div></div>
    <div class="stat-card red"><div class="stat-label">Total Customers</div><div class="stat-value">${d.total_users}</div></div>`;

  renderRevenueChart(d.monthly_sales, 'revenueChart');

  document.getElementById('lowStockList').innerHTML = d.low_stock.length ? d.low_stock.map(p=>`
    <div class="low-stock-item">
      <div style="flex:1"><div style="font-size:13px;font-weight:500">${p.name}</div></div>
      <span style="font-family:'Syne',sans-serif;font-size:16px;color:${p.stock===0?'var(--red)':'var(--amber)'};">${p.stock}</span>
      <span style="font-size:11px;color:var(--text-secondary)">left</span>
    </div>`).join('') : '<div style="text-align:center;padding:20px;color:var(--green);font-size:13px">All products well-stocked!</div>';

  document.getElementById('recentOrdersList').innerHTML = `
    <table class="data-table"><thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
    <tbody>${d.recent_orders.map(o=>`<tr><td style="color:var(--red);font-family:'Syne',sans-serif;font-size:12px">${o.order_number}</td><td>${o.customer_name}</td><td style="font-family:'Syne',sans-serif">${formatPrice(o.total)}</td><td><span class="status-badge status-${o.order_status}">${o.order_status.toUpperCase()}</span></td><td style="color:var(--text-secondary);font-size:12px">${new Date(o.created_at).toLocaleDateString('en-PH')}</td></tr>`).join('')}</tbody></table>`;
}

function renderRevenueChart(data, containerId) {
  if (!data.length) { document.getElementById(containerId).innerHTML = '<div style="color:var(--text-secondary);text-align:center;padding:40px">No data yet</div>'; return; }
  const max = Math.max(...data.map(d=>parseFloat(d.revenue)));
  document.getElementById(containerId).innerHTML = data.map(d=>{
    const h = max > 0 ? Math.round((parseFloat(d.revenue)/max)*160) : 4;
    const label = d.period?.substring(5) || d.period;
    return `<div class="chart-bar-col"><div class="chart-bar" style="height:${h}px" data-value="${formatPrice(d.revenue)}"></div><div class="chart-label">${label}</div></div>`;
  }).join('');
}

// ── PRODUCTS ───────────────────────────────────────────────────────────────

const productCache = {};

async function loadAdminProducts(search = '') {
  document.getElementById('productsBody').innerHTML =
    '<tr><td colspan="6" style="text-align:center;padding:40px"><div class="spinner" style="margin:0 auto"></div></td></tr>';

  const url = `${ADMIN_API}?action=admin_get_products&search=${encodeURIComponent(search)}`;
  const data = await fetch(url, { credentials: 'include' }).then(r => r.json());
  if (!data.success) return;

  data.products.forEach(p => { productCache[p.id] = p; });

  document.getElementById('productsBody').innerHTML = data.products.map(p => {
    const img = p.primary_image
      ? `${UPLOAD}products/${p.primary_image}`
      : '/nexus-gear/images/no-image.png';
    const stockColor = parseInt(p.stock) === 0
      ? 'var(--red)' : parseInt(p.stock) <= 10
      ? 'var(--amber)' : 'var(--green)';
    return `
    <tr>
      <td>
        <div style="display:flex;align-items:center;gap:12px">
          <img src="${img}" style="width:52px;height:52px;border-radius:10px;object-fit:contain;background:var(--bg-card2)" onerror="this.src='/nexus-gear/images/no-image.png'">
          <div>
            <div style="font-size:13px;font-weight:600;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-primary)">${p.name}</div>
            <div style="font-size:11px;color:var(--text-secondary);margin-top:2px">${p.brand_name || '—'}</div>
          </div>
        </div>
      </td>
      <td style="font-size:13px;color:var(--text-secondary)">${p.category_name || '—'}</td>
      <td style="font-size:14px;font-weight:700;color:var(--text-primary)">${formatPrice(p.price)}</td>
      <td><span style="font-weight:700;font-size:14px;color:${stockColor}">${p.stock}</span></td>
      <td>
        <div style="display:flex;gap:5px;flex-wrap:wrap">
          ${p.is_featured == 1  ? '<span class="product-badge badge-featured">FEATURED</span>' : ''}
          ${p.is_best_seller == 1 ? '<span class="product-badge badge-bestseller">BEST SELLER</span>' : ''}
          ${p.is_active == 0    ? '<span class="product-badge badge-outofstock">INACTIVE</span>' : ''}
        </div>
      </td>
      <td>
        <div style="display:flex;gap:6px">
          <button type="button" class="btn btn-outline btn-sm" onclick="openProductById(${p.id})">Edit</button>
          <button type="button" class="btn btn-danger btn-sm" onclick="deleteProduct(${p.id}, ${JSON.stringify(p.name)})">Delete</button>
        </div>
      </td>
    </tr>`;
  }).join('') || '<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-secondary)">No products found</td></tr>';
}

async function openProductById(id) {
  const freshRes = await fetch(`${ADMIN_API}?action=admin_get_product_by_id&id=${id}`, { credentials: 'include' }).then(r => r.json());
  if (freshRes.success && freshRes.product) {
    await openProductModal(freshRes.product);
  } else if (productCache[id]) {
    await openProductModal(productCache[id]);
  }
}

async function openProductModal(product) {
  const [catData, brandData] = await Promise.all([
    fetch(`${ADMIN_API}?action=get_categories`).then(r => r.json()),
    fetch(`${ADMIN_API}?action=get_brands`).then(r => r.json())
  ]);
  categories = catData.categories || [];
  brands     = brandData.brands   || [];

  document.getElementById('productCategory').innerHTML =
    '<option value="">Select category</option>' +
    categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
  document.getElementById('productBrand').innerHTML =
    '<option value="">Select brand</option>' +
    brands.map(b => `<option value="${b.id}">${b.name}</option>`).join('');

  const form = document.getElementById('productForm');
  form.reset();

  if (product) {
    document.getElementById('productModalTitle').textContent = 'Edit Product';
    document.getElementById('productId').value = product.id;
    form.name.value             = product.name             || '';
    form.price.value            = product.price            || '';
    form.original_price.value   = product.original_price   || '';
    form.stock.value            = product.stock            || 0;
    form.short_description.value = product.short_description || '';
    form.description.value      = product.description      || '';
    form.category_id.value      = product.category_id      || '';
    form.brand_id.value         = product.brand_id         || '';
    form.is_featured.checked    = product.is_featured == 1;
    form.is_best_seller.checked = product.is_best_seller == 1;

    if (product.specifications) {
      try {
        const s = typeof product.specifications === 'string'
          ? JSON.parse(product.specifications) : product.specifications;
        form.specifications.value = JSON.stringify(s, null, 2);
      } catch { form.specifications.value = product.specifications; }
    } else {
      form.specifications.value = '';
    }

    document.getElementById('productImageSection').classList.remove('hidden');
    document.getElementById('imageUploadInput').dataset.productId = product.id;
    document.getElementById('imageUploadInput').dataset.productSlug = product.slug || '';
    await refreshProductImagesGrid(product.slug);
  } else {
    document.getElementById('productModalTitle').textContent = 'Add New Product';
    document.getElementById('productId').value = '';
    document.getElementById('productImageSection').classList.add('hidden');
    document.getElementById('productImagesGrid').innerHTML = '';
  }

  openModal('productModal');
}

async function saveProduct() {
  const form = document.getElementById('productForm');
  const fd   = new FormData(form);
  fd.set('is_featured',    form.is_featured.checked    ? '1' : '0');
  fd.set('is_best_seller', form.is_best_seller.checked ? '1' : '0');

  const btn = document.querySelector('#productModal .modal-footer .btn-primary');
  if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }

  const data = await apiCall('admin_save_product', fd, 'POST');

  if (btn) { btn.disabled = false; btn.textContent = 'Save Product'; }

  if (data.success) {
    toast(data.message, 'success');
    loadAdminProducts();

    const isNew = !form.id.value || form.id.value === '0';
    if (isNew && data.id) {
      form.id.value = data.id;
      document.getElementById('imageUploadInput').dataset.productId  = data.id;
      document.getElementById('imageUploadInput').dataset.productSlug = data.slug || '';
      document.getElementById('productImagesGrid').innerHTML =
        '<div style="color:var(--text-secondary);font-size:13px;padding:4px 0">Product saved! Upload photos below.</div>';
      document.getElementById('productImageSection').classList.remove('hidden');
      document.getElementById('productModalTitle').textContent = 'Edit Product — Add Photos';
    } else {
      closeModal('productModal');
    }
  } else {
    toast(data.message || 'Save failed.', 'error');
  }
}

// ── IMAGE GRID (FIXED) ─────────────────────────────────────────────────────
// Key fixes vs original:
//   1. Tile wrapper gets class="img-tile" so closest('.img-tile') works reliably
//   2. deleteProductImage(event, imageId) — event is passed explicitly, not relied on as global
//   3. setPrimaryImage(event, imageId, pid) — event passed explicitly; pid is a concrete
//      integer rendered into the onclick string so it's never "undefined"

async function refreshProductImagesGrid(slugOrId) {
  const grid = document.getElementById('productImagesGrid');
  if (!grid) return;

  grid.innerHTML = '<div style="color:var(--text-secondary);font-size:13px;padding:8px 0">Loading photos…</div>';

  // Always read productId from the upload input's dataset (set before this is called)
  const productId = document.getElementById('imageUploadInput')?.dataset.productId;
  let imgs = [];

  try {
    if (productId) {
      const res = await fetch(`${ADMIN_API}?action=admin_get_product_by_id&id=${productId}`, { credentials: 'include' }).then(r => r.json());
      imgs = res.product?.images || [];
    } else if (slugOrId && slugOrId !== '__refresh__') {
      const res = await fetch(`${ADMIN_API}?action=get_product&slug=${encodeURIComponent(slugOrId)}`, { credentials: 'include' }).then(r => r.json());
      imgs = res.product?.images || [];
    }
  } catch(e) { /* silent */ }

  if (!imgs.length) {
    grid.innerHTML = '<div style="color:var(--text-secondary);font-size:13px;padding:4px 0">No photos yet — upload below.</div>';
    return;
  }

  // pid must be a concrete integer so the inline onclick string renders as a number, not "undefined"
  const pid = parseInt(productId, 10) || 0;

  grid.innerHTML = imgs.map(img => `
    <div class="img-tile" style="position:relative;width:90px;flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:4px">
      <div style="position:relative;width:90px;height:90px">
        <img
          src="${UPLOAD}products/${img.image_path}?t=${Date.now()}"
          style="width:90px;height:90px;border-radius:10px;object-fit:contain;background:var(--bg-card2);border:2px solid ${img.is_primary ? 'var(--red)' : 'var(--border-med)'};transition:border-color 0.2s"
          onerror="this.src='/nexus-gear/images/no-image.png'"
        >
        ${img.is_primary
          ? `<div style="position:absolute;bottom:5px;left:50%;transform:translateX(-50%);background:var(--red);color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:3px;white-space:nowrap;pointer-events:none;letter-spacing:.5px">PRIMARY</div>`
          : ''
        }
        <button
          type="button"
          onclick="deleteProductImage(event, ${img.id})"
          title="Remove photo"
          style="position:absolute;top:-7px;right:-7px;width:22px;height:22px;background:var(--red);border:2px solid #1a1a2e;border-radius:50%;color:#fff;cursor:pointer;font-size:14px;line-height:1;display:flex;align-items:center;justify-content:center;font-weight:700"
        >×</button>
      </div>
      ${!img.is_primary
        ? `<button
            type="button"
            onclick="setPrimaryImage(event, ${img.id}, ${pid})"
            title="Set as primary image"
            style="width:90px;padding:3px 0;font-size:10px;font-weight:600;letter-spacing:.4px;background:var(--bg-card2);border:1px solid var(--border-med);border-radius:5px;color:var(--text-secondary);cursor:pointer;transition:all .15s"
            onmouseover="this.style.background='var(--red)';this.style.color='#fff';this.style.borderColor='var(--red)'"
            onmouseout="this.style.background='var(--bg-card2)';this.style.color='var(--text-secondary)';this.style.borderColor='var(--border-med)'"
          >★ Set Primary</button>`
        : `<div style="width:90px;height:22px"></div>`
      }
    </div>`).join('');
}

async function setPrimaryImage(evt, imageId, productId) {
  if (evt) { evt.preventDefault(); evt.stopPropagation(); }
  if (!imageId || !productId) return;

  const fd = new FormData();
  fd.append('image_id', imageId);
  fd.append('product_id', productId);

  const res = await fetch(`${ADMIN_API}?action=admin_set_primary_image`, {
    method: 'POST',
    credentials: 'include',
    body: fd
  }).then(r => r.json());

  if (res.success) {
    toast('Primary image updated!', 'success');
    await refreshProductImagesGrid(String(productId));
    loadAdminProducts();
  } else {
    toast(res.message || 'Failed to set primary image.', 'error');
  }
}

function deleteProductImage(evt, imageId) {
  if (evt) { evt.preventDefault(); evt.stopPropagation(); }
  const btn = evt?.target || evt?.currentTarget;
  const domEl = btn ? btn.closest('.img-tile') : null;
  deleteProductImageById(imageId, domEl);
}

async function deleteProductImageById(id, domEl) {
  if (!id) { domEl?.remove(); return; }
  window.ngConfirm('Remove Photo', 'Delete this product photo permanently?', async () => {
    const data = await apiCall('admin_delete_image', { id }, 'POST');
    if (data.success) {
      domEl?.remove();
      const grid = document.getElementById('productImagesGrid');
      if (grid && grid.querySelectorAll('.img-tile').length === 0) {
        grid.innerHTML = '<div style="color:var(--text-secondary);font-size:13px;padding:4px 0">No photos yet — upload below.</div>';
      }
      toast('Photo removed.', 'info');
      setTimeout(() => loadAdminProducts(), 300);
    } else {
      toast('Failed to delete photo.', 'error');
    }
  });
}

async function uploadProductImages(input) {
  const productId = input.dataset.productId;
  if (!productId) { toast('Save the product first, then upload photos.', 'warning'); input.value = ''; return; }

  const files = Array.from(input.files);
  if (!files.length) return;

  const grid = document.getElementById('productImagesGrid');
  const hasExisting = grid.querySelectorAll('.img-tile').length > 0;

  let uploaded = 0;
  for (let i = 0; i < files.length; i++) {
    const fd = new FormData();
    fd.append('image', files[i]);
    fd.append('product_id', productId);
    fd.append('is_primary', (i === 0 && !hasExisting) ? '1' : '0');

    try {
      const res  = await fetch(`${ADMIN_API}?action=admin_upload_image`, { method: 'POST', body: fd, credentials: 'include' });
      const data = await res.json();
      if (data.success) {
        uploaded++;
      } else {
        toast(`Upload failed: ${data.message}`, 'error');
      }
    } catch (e) {
      toast('Upload error — check file size (max 5MB).', 'error');
    }
  }

  input.value = '';

  if (uploaded > 0) {
    toast(`${uploaded} photo${uploaded > 1 ? 's' : ''} uploaded!`, 'success');
    await refreshProductImagesGrid(productId);
    loadAdminProducts();
  }
}

async function deleteProduct(id, name) {
  window.ngConfirm('Delete Product', `Are you sure you want to remove "${name}"?`, async ()=>{
    const data = await apiCall('admin_delete_product',{id},'POST');
    if(data.success) { toast('Product removed.','success'); loadAdminProducts(); }
    else toast(data.message,'error');
  });
}

// ORDERS
let currentOrderStatus = '';
async function filterOrders(status, btn) {
  currentOrderStatus = status;
  document.querySelectorAll('.order-filter-btn').forEach(b=>b.classList.remove('active','btn-primary'));
  document.querySelectorAll('.order-filter-btn').forEach(b=>b.classList.add('btn-ghost'));
  btn.classList.add('active');
  loadAdminOrders();
}

async function loadAdminOrders() {
  document.getElementById('ordersBody').innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px"><div class="spinner" style="margin:0 auto"></div></td></tr>';
  const data = await fetch(`${ADMIN_API}?action=admin_get_orders&status=${currentOrderStatus}`,{credentials:'include'}).then(r=>r.json());
  if (!data.success) return;
  document.getElementById('ordersBody').innerHTML = data.orders.map(o=>`
    <tr>
      <td style="color:var(--red);font-family:'Syne',sans-serif;font-size:11px">${o.order_number}</td>
      <td><div style="font-size:13px">${o.customer_name}</div><div style="font-size:11px;color:var(--text-secondary)">${o.customer_email}</div></td>
      <td style="font-size:13px">${o.item_count}</td>
      <td style="font-family:'Syne',sans-serif;font-size:13px;color:var(--red)">${formatPrice(o.total)}</td>
      <td style="font-size:12px;text-transform:uppercase">${o.payment_method.replace('_',' ')}</td>
      <td><span class="status-badge status-${o.order_status}">${o.order_status.toUpperCase()}</span></td>
      <td style="font-size:12px;color:var(--text-secondary)">${new Date(o.created_at).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'})}</td>
      <td><button type="button" class="btn btn-outline btn-sm" onclick="openOrderModal(${o.id})">Manage</button></td>
    </tr>`).join('') || '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-secondary)">No orders found</td></tr>';
}

async function openOrderModal(id) {
  currentOrderId = id;
  const data = await fetch(`${ADMIN_API}?action=get_order&id=${id}`,{credentials:'include'}).then(r=>r.json());
  if (!data.success) return;
  const o = data.order;
  document.getElementById('orderStatusSelect').value = o.order_status;
  document.getElementById('orderModalBody').innerHTML = `
    <div style="margin-bottom:16px">
      <div style="font-family:'Syne',sans-serif;color:var(--red);font-size:16px;margin-bottom:4px">${o.order_number}</div>
      <div style="font-size:13px;color:var(--text-secondary)">${new Date(o.created_at).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'})}</div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
      <div style="background:var(--bg-card2);border-radius:var(--radius);padding:14px;border:1px solid var(--border)">
        <div style="font-size:10px;color:var(--text-secondary);margin-bottom:6px">CUSTOMER</div>
        <div style="font-weight:600">${o.shipping_name}</div>
        <div style="font-size:12px;color:var(--text-secondary)">${o.shipping_phone}</div>
        <div style="font-size:12px;color:var(--text-secondary);margin-top:4px">${o.shipping_address}, ${o.shipping_city}, ${o.shipping_province}</div>
      </div>
      <div style="background:var(--bg-card2);border-radius:var(--radius);padding:14px;border:1px solid var(--border)">
        <div style="font-size:10px;color:var(--text-secondary);margin-bottom:6px">PAYMENT</div>
        <div style="font-weight:600;text-transform:uppercase">${o.payment_method.replace('_',' ')}</div>
        <span class="status-badge status-${o.payment_status}" style="margin-top:6px;display:inline-block">${o.payment_status.toUpperCase()}</span>
        ${o.gcash_ref?`<div style="font-size:12px;margin-top:6px;color:var(--text-secondary)">Ref: ${o.gcash_ref}</div>`:''}
      </div>
    </div>
    <div style="margin-bottom:16px">
      ${o.items.map(item=>{
        const img = item.image ? UPLOAD+'products/'+item.image : '/nexus-gear/images/no-image.png';
        return `<div style="display:flex;align-items:center;gap:12px;padding:10px;background:var(--bg-card2);border-radius:var(--radius);border:1px solid var(--border);margin-bottom:8px">
          <img src="${img}" style="width:48px;height:48px;border-radius:6px;object-fit:contain;background:var(--bg-page)" onerror="this.src='/nexus-gear/images/no-image.png'">
          <div style="flex:1"><div style="font-size:13px;font-weight:500">${item.product_name}</div><div style="font-size:12px;color:var(--text-secondary)">×${item.quantity}</div></div>
          <span style="font-family:'Syne',sans-serif;color:var(--red);font-size:13px">${formatPrice(item.subtotal)}</span>
        </div>`;
      }).join('')}
    </div>
    <div style="background:var(--bg-card2);border-radius:var(--radius);padding:14px;border:1px solid var(--border)">
      ${o.discount_amount>0?`<div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;color:var(--green)"><span>Discount</span><span>-${formatPrice(o.discount_amount)}</span></div>`:''}
      <div style="display:flex;justify-content:space-between;padding:8px 0;border-top:1px solid var(--border);margin-top:4px"><span style="font-weight:700">Total</span><span style="font-family:'Syne',sans-serif;color:var(--red);font-size:18px">${formatPrice(o.total)}</span></div>
    </div>
    ${o.notes?`<div style="margin-top:12px;padding:12px;background:rgba(0,212,255,0.04);border-radius:var(--radius);border:1px solid var(--border)"><div style="font-size:10px;color:var(--text-secondary);margin-bottom:4px">NOTES</div><div style="font-size:13px;color:var(--text-secondary)">${o.notes}</div></div>`:''}
  `;
  openModal('orderModal');
}

async function updateOrderStatus() {
  const status = document.getElementById('orderStatusSelect').value;
  const data = await apiCall('admin_update_order',{id:currentOrderId,status},'POST');
  if(data.success) { toast('Order status updated!','success'); closeModal('orderModal'); loadAdminOrders(); }
  else toast(data.message,'error');
}

// CATEGORIES
async function loadAdminCategories() {
  const data = await fetch(`${ADMIN_API}?action=admin_get_categories`,{credentials:'include'}).then(r=>r.json());
  document.getElementById('categoriesBody').innerHTML = data.categories?.map(c=>`
    <tr><td style="font-weight:500">${c.name}</td><td style="color:var(--text-secondary);font-size:12px">${c.slug}</td><td>${c.product_count}</td>
    <td><button type="button" class="btn btn-outline btn-sm" onclick="openCategoryModal(${JSON.stringify(c).replace(/"/g,'&quot;')})">Edit</button></td></tr>`).join('') || '';
}

function openCategoryModal(cat) {
  const form = document.getElementById('categoryForm');
  form.reset();
  if(cat){document.getElementById('categoryModalTitle').textContent='EDIT CATEGORY';form.id.value=cat.id;form.name.value=cat.name;form.description.value=cat.description||'';form.sort_order.value=cat.sort_order||0;}
  else{document.getElementById('categoryModalTitle').textContent='ADD CATEGORY';form.id.value='';}
  openModal('categoryModal');
}

async function saveCategory() {
  const fd = new FormData(document.getElementById('categoryForm'));
  const data = await apiCall('admin_save_category',fd,'POST');
  if(data.success){toast('Category saved!','success');closeModal('categoryModal');loadAdminCategories();}
  else toast(data.message,'error');
}

// REVENUE
async function loadRevenue() {
  const data = await fetch(`${ADMIN_API}?action=admin_get_revenue`,{credentials:'include'}).then(r=>r.json());
  if (!data.success) return;
  renderRevenueChart(data.chart_data, 'fullRevenueChart');
  document.getElementById('topProductsList').innerHTML = data.top_products.map((p,i)=>`
    <div style="display:flex;align-items:center;gap:12px;padding:12px;background:var(--bg-card2);border-radius:var(--radius);margin-bottom:8px;border:1px solid var(--border)">
      <span style="font-family:'Syne',sans-serif;font-size:20px;color:var(--amber);width:28px">#${i+1}</span>
      <div style="flex:1"><div style="font-size:13px;font-weight:500">${p.name}</div><div style="font-size:12px;color:var(--text-secondary)">${p.sold} sold</div></div>
      <span style="font-family:'Syne',sans-serif;color:var(--red);font-size:13px">${formatPrice(p.revenue)}</span>
    </div>`).join('') || '<div style="color:var(--text-secondary);text-align:center;padding:20px">No sales data yet</div>';
}

// DISCOUNTS
async function loadDiscounts() {
  const data = await fetch(`${ADMIN_API}?action=admin_get_discounts`,{credentials:'include'}).then(r=>r.json());
  document.getElementById('discountsBody').innerHTML = data.discounts?.map(d=>`
    <tr>
      <td style="font-family:'Syne',sans-serif;color:var(--red);font-size:12px">${d.code}</td>
      <td style="text-transform:capitalize">${d.type}</td>
      <td>${d.type==='percentage'?d.value+'%':'₱'+parseFloat(d.value).toLocaleString()}</td>
      <td>₱${parseFloat(d.min_order).toLocaleString()}</td>
      <td>${d.used_count}${d.max_uses?'/'+d.max_uses:''}</td>
      <td style="font-size:12px;color:var(--text-secondary)">${d.expires_at?new Date(d.expires_at).toLocaleDateString('en-PH'):'Never'}</td>
      <td><span class="status-badge ${d.is_active?'status-approved':'status-cancelled'}">${d.is_active?'ACTIVE':'INACTIVE'}</span></td>
      <td>
        <div style="display:flex;gap:6px">
          <button type="button" class="btn btn-outline btn-sm" onclick="toggleDiscount(${d.id})">${d.is_active?'Deactivate':'Activate'}</button>
          <button type="button" class="btn btn-outline btn-sm" onclick="openDiscountModal(${JSON.stringify(d).replace(/"/g,'&quot;')})">Edit</button>
        </div>
      </td>
    </tr>`).join('') || '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-secondary)">No discount codes</td></tr>';
}

function openDiscountModal(discount) {
  const form = document.getElementById('discountForm');
  form.reset();
  if(discount){document.getElementById('discountModalTitle').textContent='EDIT DISCOUNT';form.id.value=discount.id;form.code.value=discount.code;form.type.value=discount.type;form.value.value=discount.value;form.min_order.value=discount.min_order;form.max_uses.value=discount.max_uses||'';form.expires_at.value=discount.expires_at?discount.expires_at.replace(' ','T').slice(0,16):'';}
  else{document.getElementById('discountModalTitle').textContent='ADD DISCOUNT CODE';form.id.value='';}
  openModal('discountModal');
}

async function saveDiscount() {
  const fd = new FormData(document.getElementById('discountForm'));
  const data = await apiCall('admin_save_discount',fd,'POST');
  if(data.success){toast('Discount code saved!','success');closeModal('discountModal');loadDiscounts();}
  else toast(data.message,'error');
}

async function toggleDiscount(id) {
  const data = await apiCall('admin_toggle_discount',{id},'POST');
  if(data.success) loadDiscounts();
}

// USERS
async function loadAdminUsers(search='') {
  const data = await fetch(`${ADMIN_API}?action=admin_get_users&search=${encodeURIComponent(search)}`,{credentials:'include'}).then(r=>r.json());
  document.getElementById('usersBody').innerHTML = data.users?.map(u=>`
    <tr>
      <td><div style="display:flex;align-items:center;gap:10px"><img src="${u.avatar&&u.avatar!=='default.png'?UPLOAD+'avatars/'+u.avatar:'/nexus-gear/images/default-avatar.png'}" style="width:36px;height:36px;border-radius:50%;object-fit:cover" onerror="this.src='/nexus-gear/images/default-avatar.png'"><span style="font-weight:500">${u.name}</span></div></td>
      <td style="font-size:13px">${u.email}</td>
      <td style="font-size:13px">${u.phone||'—'}</td>
      <td style="font-family:'Syne',sans-serif;color:var(--red)">${u.order_count}</td>
      <td style="font-size:12px;color:var(--text-secondary)">${new Date(u.created_at).toLocaleDateString('en-PH')}</td>
      <td><span class="status-badge ${u.is_active?'status-approved':'status-cancelled'}">${u.is_active?'ACTIVE':'SUSPENDED'}</span></td>
      <td><button type="button" class="btn ${u.is_active?'btn-danger':'btn-outline'} btn-sm" onclick="toggleUser(${u.id},${u.is_active?0:1})">${u.is_active?'Suspend':'Restore'}</button></td>
    </tr>`).join('') || '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-secondary)">No users found</td></tr>';
}

async function toggleUser(id, is_active) {
  const data = await apiCall('admin_update_user',{id,is_active},'POST');
  if(data.success) { toast(is_active?'User restored.':'User suspended.',is_active?'success':'warning'); loadAdminUsers(); }
}

// REVIEWS
async function loadAdminReviews() {
  const data = await fetch(`${ADMIN_API}?action=admin_get_reviews`,{credentials:'include'}).then(r=>r.json());
  document.getElementById('reviewsBody').innerHTML = data.reviews?.map(r=>`
    <tr>
      <td style="font-size:13px;font-weight:500">${r.user_name}</td>
      <td style="font-size:13px;color:var(--text-secondary);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.product_name}</td>
      <td>${[1,2,3,4,5].map(i=>`<span style="color:${i<=r.rating?'#fbbf24':'var(--text-secondary)'}">★</span>`).join('')}</td>
      <td style="font-size:12px;color:var(--text-secondary);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.body||r.title||'—'}</td>
      <td style="font-size:12px;color:var(--text-secondary)">${new Date(r.created_at).toLocaleDateString('en-PH')}</td>
      <td><span class="status-badge ${r.is_approved?'status-approved':'status-pending'}">${r.is_approved?'APPROVED':'PENDING'}</span></td>
      <td><button type="button" class="btn btn-outline btn-sm" onclick="toggleReview(${r.id},${r.is_approved?0:1})">${r.is_approved?'Hide':'Approve'}</button></td>
    </tr>`).join('') || '<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-secondary)">No reviews</td></tr>';
}

async function toggleReview(id, approved) {
  const data = await apiCall('admin_approve_review',{id,approved},'POST');
  if(data.success) { toast('Review updated.','success'); loadAdminReviews(); }
}

// Init
document.addEventListener('DOMContentLoaded', ()=>{
  checkAuth();
  loadDashboard();
  document.querySelectorAll('[data-close-modal]').forEach(btn=>btn.addEventListener('click',()=>closeModal(btn.dataset.closeModal)));
  document.querySelectorAll('.modal-overlay').forEach(o=>o.addEventListener('click',e=>{if(e.target===o)closeModal(o.id)}));
  document.querySelectorAll('.logout-btn').forEach(btn=>btn.addEventListener('click',async()=>{
    await apiCall('logout',{},'POST');
    window.location='/nexus-gear/index.php';
  }));
});
</script>
</body></html>