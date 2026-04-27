// ============================================================
// NEXUS GEAR — Main Application JS · v3
// ============================================================

const API    = '/nexus-gear/php/api.php';
const UPLOAD = '/nexus-gear/uploads/';

// ── State ──
const state = {
  user: null,
  cart: { items: [], count: 0, total: 0 },
  searchTimer: null,
  checkoutDiscount: null,
};

// ── DOM helpers ──
const $  = (s, el = document) => el.querySelector(s);
const $$ = (s, el = document) => [...el.querySelectorAll(s)];

function formatPrice(n) {
  return '₱' + parseFloat(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function discountPct(orig, curr) {
  if (!orig || parseFloat(orig) <= parseFloat(curr)) return 0;
  return Math.round((orig - curr) / orig * 100);
}

function renderStars(rating) {
  let html = '<div class="stars">';
  for (let i = 1; i <= 5; i++)
    html += `<span class="star ${i <= Math.round(rating || 0) ? 'filled' : 'empty'}">★</span>`;
  return html + '</div>';
}

async function apiCall(action, data = {}, method = 'GET') {
  const url = `${API}?action=${action}`;
  const opts = { method, credentials: 'include' };
  if (method === 'POST') {
    opts.body = data instanceof FormData ? data : (() => {
      const fd = new FormData();
      Object.entries(data).forEach(([k, v]) => fd.append(k, v));
      return fd;
    })();
  }
  const res = await fetch(url, opts);
  return res.json();
}

// ── Toast ──
function toast(msg, type = 'info') {
  const icons = { success: '✓', error: '✕', info: 'ℹ', warning: '⚠' };
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const el = document.createElement('div');
  el.className = `toast toast-${type}`;
  el.innerHTML = `<span class="toast-icon">${icons[type]}</span><span class="toast-msg">${msg}</span>`;
  container.appendChild(el);
  setTimeout(() => {
    el.style.cssText = 'opacity:0;transform:translateX(30px);transition:all .3s';
    setTimeout(() => el.remove(), 320);
  }, 3400);
}

// ── Modals ──
function openModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.remove('open'); document.body.style.overflow = ''; }
}

// Namespaced confirm to avoid overwriting window.confirm
window.ngConfirm = function(title, message, onConfirm) {
  const modal = document.getElementById('confirmModal');
  if (!modal) return;
  const titleEl = modal.querySelector('.confirm-title');
  const msgEl   = modal.querySelector('.confirm-message');
  if (titleEl) titleEl.textContent = title;
  if (msgEl)   msgEl.textContent   = message;
  const oldBtn = modal.querySelector('.confirm-yes');
  const newBtn = oldBtn.cloneNode(true);
  oldBtn.parentNode.replaceChild(newBtn, oldBtn);
  newBtn.addEventListener('click', () => { onConfirm(); closeModal('confirmModal'); });
  openModal('confirmModal');
};

// ── Auth ──
async function checkAuth() {
  const data = await fetch(`${API}?action=check_auth`, { credentials: 'include' }).then(r => r.json());
  if (data.logged_in) {
    state.user = data.user;
    updateAuthUI();
    loadCart();
  } else {
    updateAuthUI();
  }
}

function updateAuthUI() {
  const authBtns  = document.getElementById('authBtns');
  const userMenu  = document.getElementById('userMenu');

  if (state.user) {
    authBtns?.classList.add('hidden');
    if (userMenu) {
      userMenu.classList.remove('hidden');
      const avatar = userMenu.querySelector('.user-avatar');
      const uname  = userMenu.querySelector('.user-dropdown-name');
      const uemail = userMenu.querySelector('.user-dropdown-email');
      if (avatar) avatar.src = (state.user.avatar && state.user.avatar !== 'default.png')
        ? UPLOAD + 'avatars/' + state.user.avatar
        : '/nexus-gear/images/default-avatar.png';
      if (uname)  uname.textContent  = state.user.name;
      if (uemail) uemail.textContent = state.user.email;
      const adminLink = userMenu.querySelector('.admin-link');
      if (adminLink) adminLink.classList.toggle('hidden', state.user.role !== 'admin');
    }
  } else {
    authBtns?.classList.remove('hidden');
    userMenu?.classList.add('hidden');
  }
}

// ── Header scroll effect ──
function initHeader() {
  const header = document.getElementById('header');
  if (!header) return;
  const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 10);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

// ── Search ──
function initSearch() {
  const input   = document.getElementById('searchInput');
  const results = document.getElementById('searchResults');
  if (!input || !results) return;

  input.addEventListener('input', () => {
    clearTimeout(state.searchTimer);
    const q = input.value.trim();
    if (!q) { results.classList.add('hidden'); return; }
    state.searchTimer = setTimeout(() => performSearch(q), 280);
  });
  document.addEventListener('click', e => {
    if (!input.contains(e.target) && !results.contains(e.target))
      results.classList.add('hidden');
  });
  input.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
      results.classList.add('hidden');
      window.location.href = `/nexus-gear/shop.php?q=${encodeURIComponent(input.value.trim())}`;
    }
  });
}

async function performSearch(q) {
  const results = document.getElementById('searchResults');
  results.innerHTML = '<div style="padding:20px;text-align:center"><div class="spinner" style="width:22px;height:22px;border-width:2px"></div></div>';
  results.classList.remove('hidden');
  const data = await fetch(`${API}?action=search_products&q=${encodeURIComponent(q)}`, { credentials: 'include' }).then(r => r.json());
  if (!data.products?.length) {
    results.innerHTML = '<div style="padding:20px;text-align:center;font-size:13px;color:var(--text-on-dark-dim)">No products found</div>';
    return;
  }
  results.innerHTML = data.products.slice(0, 6).map(p => {
    const img = p.primary_image ? UPLOAD + 'products/' + p.primary_image : '/nexus-gear/images/no-image.png';
    return `<a href="/nexus-gear/product.php?slug=${p.slug}" class="search-result-item">
      <img src="${img}" alt="${p.name}" onerror="this.src='/nexus-gear/images/no-image.png'">
      <div>
        <div class="search-result-name">${p.name}</div>
        <div class="search-result-price">${formatPrice(p.price)}</div>
      </div>
    </a>`;
  }).join('') + (data.products.length > 6
    ? `<a href="/nexus-gear/shop.php?q=${encodeURIComponent(q)}" style="display:block;text-align:center;padding:10px;font-size:12px;color:var(--red)">See all results →</a>`
    : '');
}

// ── Cart ──
function initCart() {
  const cartBtn     = document.getElementById('cartBtn');
  const cartOverlay = document.getElementById('cartOverlay');
  const cartSidebar = document.getElementById('cartSidebar');
  const cartClose   = document.getElementById('cartClose');

  const openCart = () => {
    if (!state.user) { openModal('authModal'); return; }
    cartOverlay?.classList.add('open');
    cartSidebar?.classList.add('open');
    document.body.style.overflow = 'hidden';
    loadCart();
  };
  const closeCart = () => {
    cartOverlay?.classList.remove('open');
    cartSidebar?.classList.remove('open');
    document.body.style.overflow = '';
  };

  cartBtn?.addEventListener('click', openCart);
  cartOverlay?.addEventListener('click', closeCart);
  cartClose?.addEventListener('click', closeCart);
  document.getElementById('continueShoppingBtn')?.addEventListener('click', closeCart);
}

async function loadCart() {
  const data = await fetch(`${API}?action=get_cart`, { credentials: 'include' }).then(r => r.json());
  if (data.success) { state.cart = data; updateCartUI(); }
}

function updateCartUI() {
  const badge    = document.getElementById('cartBadge');
  const cartItems = document.getElementById('cartItems');
  const cartTotal = document.getElementById('cartTotal');
  const checkoutBtn = document.getElementById('cartCheckoutBtn');

  if (badge) {
    badge.textContent = state.cart.count || 0;
    badge.classList.toggle('hidden', !state.cart.count);
  }
  if (!cartItems) return;

  if (!state.cart.items?.length) {
    cartItems.innerHTML = `<div class="cart-empty">
      <img src="/nexus-gear/images/cart-empty.png" alt="Empty">
      <p style="font-weight:600;margin-bottom:6px;color:var(--text-primary)">Your cart is empty</p>
      <p style="font-size:13px;color:var(--text-secondary)">Add some gear to get started!</p>
    </div>`;
    if (checkoutBtn) checkoutBtn.disabled = true;
    return;
  }
  if (checkoutBtn) checkoutBtn.disabled = false;

  cartItems.innerHTML = state.cart.items.map(item => {
    const img = item.image ? UPLOAD + 'products/' + item.image : '/nexus-gear/images/no-image.png';
    return `<div class="cart-item" data-cart-id="${item.id}">
      <img src="${img}" alt="${item.name}" class="cart-item-img" onerror="this.src='/nexus-gear/images/no-image.png'">
      <div style="flex:1;min-width:0">
        <div class="cart-item-name">${item.name}</div>
        <div class="cart-item-price">${formatPrice(item.price)}</div>
        <div class="cart-item-controls">
          <button class="qty-btn" onclick="updateCartItem(${item.id},${item.quantity - 1})">−</button>
          <span class="qty-display">${item.quantity}</span>
          <button class="qty-btn" onclick="updateCartItem(${item.id},${item.quantity + 1})">+</button>
        </div>
      </div>
      <button class="cart-item-remove" onclick="removeCartItem(${item.id})" title="Remove">✕</button>
    </div>`;
  }).join('');
  if (cartTotal) cartTotal.textContent = formatPrice(state.cart.total);
}

async function addToCart(productId, qty = 1) {
  if (!state.user) { openModal('authModal'); return; }
  const data = await apiCall('add_to_cart', { product_id: productId, quantity: qty }, 'POST');
  if (data.success) {
    state.cart.count = data.cart_count;
    const badge = document.getElementById('cartBadge');
    if (badge) { badge.textContent = data.cart_count; badge.classList.remove('hidden'); }
    toast('Added to cart!', 'success');
    loadCart();
  } else {
    toast(data.message, 'error');
  }
}

async function updateCartItem(cartId, qty) {
  if (qty < 1) { removeCartItem(cartId); return; }
  const data = await apiCall('update_cart', { cart_id: cartId, quantity: qty }, 'POST');
  if (data.success) loadCart();
  else toast(data.message, 'error');
}

async function removeCartItem(cartId) {
  const data = await apiCall('remove_from_cart', { cart_id: cartId }, 'POST');
  if (data.success) { toast('Item removed.', 'info'); loadCart(); }
}

// ── Wishlist ──
async function toggleWishlist(productId, btn) {
  if (!state.user) { openModal('authModal'); return; }
  const data = await apiCall('toggle_wishlist', { product_id: productId }, 'POST');
  if (data.success) {
    const isAdded = data.action === 'added';
    if (btn) btn.classList.toggle('active', isAdded);
    toast(isAdded ? 'Added to wishlist!' : 'Removed from wishlist.', isAdded ? 'success' : 'info');
  }
}

// ── Buy Now modal ──
function openBuyNow(productId, productName, price, imgSrc) {
  if (!state.user) { openModal('authModal'); return; }

  // Build a lightweight buy-now confirmation modal dynamically
  let modal = document.getElementById('buyNowModal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'buyNowModal';
    modal.className = 'modal-overlay';
    document.body.appendChild(modal);
  }

  modal.innerHTML = `
    <div class="modal modal-lg" style="max-width:540px">
      <div class="modal-header">
        <span class="modal-title">Buy Now</span>
        <button class="modal-close" onclick="closeModal('buyNowModal')">✕</button>
      </div>
      <div class="modal-body">
        <!-- Product preview -->
        <div style="display:flex;gap:14px;align-items:center;padding:14px;background:var(--bg-card2);border-radius:10px;margin-bottom:20px;border:1px solid var(--border)">
          <img src="${imgSrc}" style="width:70px;height:70px;object-fit:contain;border-radius:8px;background:var(--bg-white)" onerror="this.src='/nexus-gear/images/no-image.png'">
          <div style="flex:1">
            <div style="font-weight:600;font-size:14px;color:var(--text-primary)">${productName}</div>
            <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:800;color:var(--red);margin-top:4px">${formatPrice(price)}</div>
          </div>
          <div style="display:flex;align-items:center;gap:0;border:1.5px solid var(--border-med);border-radius:8px;overflow:hidden">
            <button onclick="buyNowQtyChange(-1)" style="width:36px;height:36px;background:var(--bg-card2);border:none;font-size:16px;cursor:pointer;color:var(--text-primary)">−</button>
            <span id="buyNowQty" style="width:44px;text-align:center;font-weight:700;font-size:15px;color:var(--text-primary)">1</span>
            <button onclick="buyNowQtyChange(1)" style="width:36px;height:36px;background:var(--bg-card2);border:none;font-size:16px;cursor:pointer;color:var(--text-primary)">+</button>
          </div>
        </div>

        <!-- Shipping address (pre-filled) -->
        <div style="margin-bottom:16px">
          <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--text-secondary);margin-bottom:10px">Delivery Address</div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
            <div>
              <label class="form-label">Full Name</label>
              <input type="text" id="bnName" class="form-input" value="${state.user?.name || ''}" placeholder="Your name">
            </div>
            <div>
              <label class="form-label">Phone</label>
              <input type="tel" id="bnPhone" class="form-input" value="${state.user?.phone || ''}" placeholder="09xxxxxxxxx">
            </div>
          </div>
          <div style="margin-top:10px">
            <label class="form-label">Address</label>
            <input type="text" id="bnAddress" class="form-input" value="${state.user?.address_line || ''}" placeholder="Street, Barangay">
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-top:10px">
            <div>
              <label class="form-label">City</label>
              <input type="text" id="bnCity" class="form-input" value="${state.user?.city || ''}" placeholder="City">
            </div>
            <div>
              <label class="form-label">Province</label>
              <input type="text" id="bnProvince" class="form-input" value="${state.user?.province || ''}" placeholder="Province">
            </div>
            <div>
              <label class="form-label">ZIP</label>
              <input type="text" id="bnZip" class="form-input" value="${state.user?.zip_code || ''}" placeholder="ZIP">
            </div>
          </div>
        </div>

        <!-- Payment method -->
        <div style="margin-bottom:16px">
          <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--text-secondary);margin-bottom:10px">Payment Method</div>
          <div style="display:flex;flex-direction:column;gap:8px" id="bnPaymentOpts">
            ${[
              { m:'cod', label:'Cash on Delivery', desc:'Pay when you receive your order' },
              { m:'gcash', label:'GCash', desc:'Pay via GCash mobile wallet' },
              { m:'credit_card', label:'Credit / Debit Card', desc:'Visa, Mastercard, JCB' },
            ].map((opt, i) => `
            <label style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--bg-card2);border:1.5px solid ${i===0?'var(--red)':'var(--border)'};border-radius:10px;cursor:pointer;transition:all .2s" onclick="bnSelectPayment(this,'${opt.m}')">
              <input type="radio" name="bnPayment" value="${opt.m}" ${i===0?'checked':''} style="accent-color:var(--red)">
              <div>
                <div style="font-weight:700;font-size:14px;color:var(--text-primary)">${opt.label}</div>
                <div style="font-size:12px;color:var(--text-secondary)">${opt.desc}</div>
              </div>
            </label>`).join('')}
          </div>
        </div>

        <!-- Notes -->
        <div>
          <label class="form-label">Notes (Optional)</label>
          <textarea id="bnNotes" class="form-textarea" style="min-height:60px" placeholder="Special instructions..."></textarea>
        </div>

        <!-- Total -->
        <div style="margin-top:16px;padding:14px;background:var(--bg-card2);border-radius:10px;border:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
          <span style="font-weight:600;color:var(--text-secondary)">Total</span>
          <span id="bnTotal" style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;color:var(--text-primary)">${formatPrice(price)}</span>
        </div>
      </div>
      <div class="modal-footer" style="gap:10px">
        <button class="btn btn-ghost" onclick="closeModal('buyNowModal')">Cancel</button>
        <button class="btn btn-primary btn-lg" onclick="placeBuyNowOrder(${productId},${price})" style="flex:1;justify-content:center">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
          Confirm Order
        </button>
      </div>
    </div>`;

  // Attach modal-close events
  modal.addEventListener('click', e => { if (e.target === modal) closeModal('buyNowModal'); });
  openModal('buyNowModal');

  // Store price on window for qty updates
  window._bnPrice  = parseFloat(price);
  window._bnQty    = 1;
}

window.bnSelectPayment = function(label, method) {
  document.querySelectorAll('#bnPaymentOpts label').forEach(l => {
    l.style.borderColor = 'var(--border)';
  });
  label.style.borderColor = 'var(--red)';
};

window.buyNowQtyChange = function(delta) {
  window._bnQty = Math.max(1, (window._bnQty || 1) + delta);
  const qtyEl  = document.getElementById('buyNowQty');
  const totEl  = document.getElementById('bnTotal');
  if (qtyEl) qtyEl.textContent = window._bnQty;
  if (totEl) totEl.textContent = formatPrice(window._bnPrice * window._bnQty);
};

window.placeBuyNowOrder = async function(productId, unitPrice) {
  const qty      = window._bnQty || 1;
  const payment  = document.querySelector('input[name="bnPayment"]:checked')?.value || 'cod';
  const name     = document.getElementById('bnName')?.value.trim();
  const phone    = document.getElementById('bnPhone')?.value.trim();
  const address  = document.getElementById('bnAddress')?.value.trim();
  const city     = document.getElementById('bnCity')?.value.trim();
  const province = document.getElementById('bnProvince')?.value.trim();
  const zip      = document.getElementById('bnZip')?.value.trim();
  const notes    = document.getElementById('bnNotes')?.value.trim();

  if (!name || !phone || !address || !city || !province) {
    toast('Please fill in all required delivery fields.', 'warning'); return;
  }

  const btn = document.querySelector('#buyNowModal .btn-primary');
  if (btn) { btn.disabled = true; btn.textContent = 'Placing Order…'; }

  // Add to cart silently first, then place order, then clear cart
  const addRes = await apiCall('add_to_cart', { product_id: productId, quantity: qty }, 'POST');
  if (!addRes.success) {
    toast(addRes.message || 'Could not add item.', 'error');
    if (btn) { btn.disabled = false; btn.textContent = 'Confirm Order'; }
    return;
  }

  const fd = new FormData();
  fd.append('shipping_name',     name);
  fd.append('shipping_phone',    phone);
  fd.append('shipping_address',  address);
  fd.append('shipping_city',     city);
  fd.append('shipping_province', province);
  fd.append('shipping_zip',      zip);
  fd.append('payment_method',    payment);
  fd.append('notes',             notes);

  const orderRes = await apiCall('place_order', fd, 'POST');
  if (btn) { btn.disabled = false; btn.textContent = 'Confirm Order'; }

  if (orderRes.success) {
    closeModal('buyNowModal');
    openOrderSuccessModal(orderRes.order_number);
    loadCart();
  } else {
    toast(orderRes.message || 'Order failed. Please try again.', 'error');
  }
};

// ── Product Card renderer — redesigned ──
function renderProductCard(p) {
  const img       = p.primary_image ? UPLOAD + 'products/' + p.primary_image : '/nexus-gear/images/no-image.png';
  const disc      = discountPct(p.original_price, p.price);
  const outOfStock = parseInt(p.stock) === 0;

  // Badges (top-left)
  let badges = '';
  if (outOfStock)          badges += '<span class="product-badge badge-outofstock">OUT OF STOCK</span>';
  else if (p.stock <= 10)  badges += '<span class="product-badge badge-lowstock">LOW STOCK</span>';
  if (p.is_featured == 1)       badges += '<span class="product-badge badge-featured">FEATURED</span>';
  else if (p.is_best_seller == 1) badges += '<span class="product-badge badge-bestseller">BEST SELLER</span>';
  if (disc > 0)            badges += `<span class="product-badge badge-sale">-${disc}%</span>`;

  const priceRow = `
    <div class="product-price">
      <span class="price-current">${formatPrice(p.price)}</span>
      ${parseFloat(p.original_price) > parseFloat(p.price)
        ? `<span class="price-original">${formatPrice(p.original_price)}</span>
           <span class="price-discount">-${disc}%</span>`
        : ''}
    </div>`;

  return `
    <div class="product-card" onclick="window.location='/nexus-gear/product.php?slug=${p.slug}'">
      <div class="product-img-wrap">
        <img src="${img}" alt="${p.name}" loading="lazy" onerror="this.src='/nexus-gear/images/no-image.png'">
        <div class="product-badges">${badges}</div>
        <!-- Heart — top right, always visible -->
        <button
          class="product-wishlist-btn"
          onclick="event.stopPropagation();toggleWishlist(${p.id},this)"
          title="Add to Wishlist"
          aria-label="Wishlist">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
        </button>
      </div>
      <div class="product-info">
        <div class="product-brand">${p.brand_name || ''}</div>
        <div class="product-name">${p.name}</div>
        <div class="product-rating">
          ${renderStars(p.rating_avg)}
          <span class="rating-count">(${p.rating_count || 0})</span>
        </div>
        ${priceRow}
      </div>
      <div class="product-add-bar">
        ${outOfStock
          ? `<button class="btn-add-cart" disabled style="opacity:.4;cursor:not-allowed;flex:2">Out of Stock</button>`
          : `<button class="btn-add-cart" onclick="event.stopPropagation();addToCart(${p.id})" title="Add to Cart">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
              Cart
            </button>
            <button class="btn-buy-now" onclick="event.stopPropagation();openBuyNow(${p.id},${JSON.stringify(p.name)},${p.price},'${img}')" title="Buy Now">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="13 17 18 12 13 7"/><path d="M6 12h12"/></svg>
              Buy Now
            </button>`
        }
      </div>
    </div>`;
}

// ── Homepage loaders ──
async function initHomePage() {
  await Promise.all([
    loadFeaturedProducts(),
    loadBestSellers(),
    loadCategories(),
    loadBrands(),
    loadCategoryProducts(),
  ]);
}

async function loadFeaturedProducts() {
  const c = document.getElementById('featuredProducts');
  if (!c) return;
  const data = await fetch(`${API}?action=get_featured`, { credentials: 'include' }).then(r => r.json());
  if (data.success) c.innerHTML = data.products.map(renderProductCard).join('');
}

async function loadBestSellers() {
  const c = document.getElementById('bestSellerProducts');
  if (!c) return;
  const data = await fetch(`${API}?action=get_bestsellers`, { credentials: 'include' }).then(r => r.json());
  if (data.success) c.innerHTML = data.products.map(renderProductCard).join('');
}

async function loadCategories() {
  const c = document.getElementById('categoryGrid');
  if (!c) return;
  const data = await fetch(`${API}?action=get_categories`, { credentials: 'include' }).then(r => r.json());
  if (data.success) {
    c.innerHTML = data.categories.map(cat => `
      <a href="/nexus-gear/shop.php?category=${cat.slug}" class="category-card">
        <div class="category-icon-wrap">
          <img src="/nexus-gear/images/categories/${cat.slug}.png" alt="${cat.name}" onerror="this.style.display='none'">
        </div>
        <div class="category-name">${cat.name}</div>
        <div class="category-count">${cat.product_count} Products</div>
      </a>`).join('');
  }
}

async function loadBrands() {
  const c = document.getElementById('brandGrid');
  if (!c) return;
  const data = await fetch(`${API}?action=get_brands`, { credentials: 'include' }).then(r => r.json());
  if (data.success) {
    c.innerHTML = data.brands.map(b => `
      <a href="/nexus-gear/shop.php?brand=${b.slug}" class="brand-card">
        <img src="/nexus-gear/images/brands/${b.slug}.png" alt="${b.name}" onerror="this.src='/nexus-gear/images/no-image.png'">
        <span class="brand-name">${b.name}</span>
      </a>`).join('');
  }
}

async function loadCategoryProducts() {
  const sections = [
    { id: 'phonesSection',  category: 'smartphones',        limit: 6 },
    { id: 'laptopsSection', category: 'laptops',            limit: 6 },
    { id: 'gamingSection',  category: 'gaming-peripherals', limit: 6 },
  ];
  for (const s of sections) {
    const c = document.getElementById(s.id);
    if (!c) continue;
    const data = await fetch(`${API}?action=get_products&category=${s.category}&limit=${s.limit}`, { credentials: 'include' }).then(r => r.json());
    if (data.success) c.innerHTML = data.products.map(renderProductCard).join('');
  }
}

// ── Checkout ──
async function initCheckout() {
  const form = document.getElementById('checkoutForm');
  if (!form) return;

  if (state.user) {
    if (form.shipping_name)     form.shipping_name.value     = state.user.name || '';
    if (form.shipping_phone)    form.shipping_phone.value    = state.user.phone || '';
    if (form.shipping_address)  form.shipping_address.value  = state.user.address_line || '';
    if (form.shipping_city)     form.shipping_city.value     = state.user.city || '';
    if (form.shipping_province) form.shipping_province.value = state.user.province || '';
    if (form.shipping_zip)      form.shipping_zip.value      = state.user.zip_code || '';
  }

  $$('.payment-option').forEach(opt => {
    opt.addEventListener('click', () => {
      $$('.payment-option').forEach(o => o.classList.remove('selected'));
      opt.classList.add('selected');
      $$('.payment-extra').forEach(p => p.classList.add('hidden'));
      document.getElementById(`extra_${opt.dataset.method}`)?.classList.remove('hidden');
    });
  });

  document.getElementById('applyDiscountBtn')?.addEventListener('click', async () => {
    const code = document.getElementById('discountCode')?.value.trim();
    if (!code) { toast('Enter a discount code.', 'warning'); return; }
    const btn = document.getElementById('applyDiscountBtn');
    btn.disabled = true; btn.textContent = 'Checking…';
    const data = await apiCall('apply_discount', { code, subtotal: state.cart.total }, 'POST');
    btn.disabled = false; btn.textContent = 'Apply';
    if (data.success) {
      state.checkoutDiscount = data.discount;
      updateOrderSummary();
      toast(`Discount applied! Save ${formatPrice(data.discount.amount)}`, 'success');
    } else {
      toast(data.message, 'error');
    }
  });

  updateOrderSummary();

  form.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = form.querySelector('.place-order-btn');
    btn.disabled = true; btn.textContent = 'Placing Order…';
    const selected = $('.payment-option.selected');
    if (!selected) { toast('Select a payment method.', 'warning'); btn.disabled = false; btn.textContent = 'Place Order'; return; }
    const fd = new FormData(form);
    fd.append('payment_method', selected.dataset.method);
    if (state.checkoutDiscount) {
      fd.append('discount_id',     state.checkoutDiscount.id);
      fd.append('discount_amount', state.checkoutDiscount.amount);
    }
    try {
      const data = await apiCall('place_order', fd, 'POST');
      if (data.success) { state.checkoutDiscount = null; openOrderSuccessModal(data.order_number); }
      else toast(data.message, 'error');
    } finally { btn.disabled = false; btn.textContent = 'Place Order'; }
  });
}

function updateOrderSummary() {
  const subtotal    = state.cart.total || 0;
  const discountAmt = state.checkoutDiscount?.amount || 0;
  const total       = Math.max(0, subtotal - discountAmt);
  const el = id => document.getElementById(id);
  if (el('summarySubtotal')) el('summarySubtotal').textContent = formatPrice(subtotal);
  if (el('summaryDiscount')) el('summaryDiscount').textContent = `-${formatPrice(discountAmt)}`;
  el('discountRow')?.classList.toggle('hidden', !state.checkoutDiscount);
  if (el('summaryTotal')) el('summaryTotal').textContent = formatPrice(total);
}

function openOrderSuccessModal(orderNumber) {
  const modal = document.getElementById('orderSuccessModal');
  if (!modal) return;
  const numEl = modal.querySelector('.order-number-display');
  if (numEl) numEl.textContent = orderNumber;
  openModal('orderSuccessModal');
  loadCart();
}

// ── DOMContentLoaded ──
document.addEventListener('DOMContentLoaded', () => {
  // Auth forms
  const loginForm    = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');

  loginForm?.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = loginForm.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Signing in…';
    try {
      const data = await apiCall('login', { email: loginForm.email.value, password: loginForm.password.value }, 'POST');
      if (data.success) {
        state.user = data.user;
        updateAuthUI(); loadCart(); closeModal('authModal');
        toast(`Welcome back, ${data.user.name}!`, 'success');
      } else toast(data.message, 'error');
    } finally { btn.disabled = false; btn.textContent = 'Sign In'; }
  });

  registerForm?.addEventListener('submit', async e => {
    e.preventDefault();
    const pwd = registerForm.password.value;
    const cfw = registerForm.confirm_password?.value;
    if (cfw && pwd !== cfw) { toast('Passwords do not match.', 'error'); return; }
    const btn = registerForm.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Creating Account…';
    try {
      const data = await apiCall('register', {
        name: registerForm.full_name.value, email: registerForm.email.value,
        password: pwd, phone: registerForm.phone?.value || ''
      }, 'POST');
      if (data.success) {
        state.user = data.user;
        updateAuthUI(); loadCart(); closeModal('authModal');
        toast('Welcome to Nexus Gear!', 'success');
      } else toast(data.message, 'error');
    } finally { btn.disabled = false; btn.textContent = 'Create Account'; }
  });

  // Logout
  $$('.logout-btn').forEach(btn => btn.addEventListener('click', async () => {
    await apiCall('logout', {}, 'POST');
    state.user = null; state.cart = { items: [], count: 0, total: 0 };
    updateAuthUI(); updateCartUI();
    toast('Logged out.', 'info');
    if (/profile|admin/.test(window.location.pathname)) window.location.href = '/nexus-gear/index.php';
  }));

  // Auth tabs
  $$('.auth-tab').forEach(tab => tab.addEventListener('click', () => {
    const target = tab.dataset.tab;
    $$('.auth-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    $$('.auth-panel').forEach(p => p.classList.toggle('hidden', p.id !== target + 'Panel'));
  }));

  // Open auth modal triggers
  $$('[data-open-auth]').forEach(btn => btn.addEventListener('click', () => {
    const tab = btn.dataset.openAuth || 'login';
    $$('.auth-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === tab));
    $$('.auth-panel').forEach(p => p.classList.toggle('hidden', p.id !== tab + 'Panel'));
    openModal('authModal');
  }));

  // User dropdown toggle
  const userAvatarBtn = document.getElementById('userAvatarBtn');
  const userDropdown  = document.getElementById('userDropdown');
  if (userAvatarBtn && userDropdown) {
    userAvatarBtn.addEventListener('click', e => { e.stopPropagation(); userDropdown.classList.toggle('open'); });
    document.addEventListener('click', () => userDropdown.classList.remove('open'));
  }

  // Modal close buttons
  $$('[data-close-modal]').forEach(btn => btn.addEventListener('click', () => closeModal(btn.dataset.closeModal)));
  $$('.modal-overlay').forEach(overlay => overlay.addEventListener('click', e => {
    if (e.target === overlay) closeModal(overlay.id);
  }));

  // Initialise everything
  checkAuth();
  initSearch();
  initCart();
  initHeader();
  if (document.getElementById('heroSection')) initHomePage();
});

// ── Globals ──
window.addToCart        = addToCart;
window.toggleWishlist   = toggleWishlist;
window.updateCartItem   = updateCartItem;
window.removeCartItem   = removeCartItem;
window.openModal        = openModal;
window.closeModal       = closeModal;
window.openBuyNow       = openBuyNow;
window.initCheckout     = initCheckout;
window.renderProductCard = renderProductCard;
window.formatPrice      = formatPrice;
window.apiCall          = apiCall;
window.toast            = toast;
window.state            = state;
window.loadCart         = loadCart;
window.checkAuth        = checkAuth;
window.UPLOAD           = UPLOAD;
window.API              = API;
