<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Checkout — Nexus Gear</title>
<link rel="icon" href="/nexus-gear/images/logo.png">
<link rel="stylesheet" href="/nexus-gear/css/main.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<div style="height:64px"></div>

<div style="background:linear-gradient(180deg,rgba(0,212,255,0.05) 0%,transparent 100%);border-bottom:1px solid var(--border);padding:32px 0">
  <div class="container">
    <div class="breadcrumb" style="margin-bottom:10px"><a href="/nexus-gear/index.php">Home</a><span class="breadcrumb-sep">/</span><span>Checkout</span></div>
    <h1 style="font-size:1.8rem">Checkout</h1>
  </div>
</div>

<div class="container" style="padding:40px 0 80px">
  <div id="checkoutContent">
    <div style="text-align:center;padding:80px"><div class="spinner"></div><p style="margin-top:16px;color:var(--text-dim)">Loading your cart...</p></div>
  </div>
</div>

<!-- Order Success Modal -->
<div class="modal-overlay" id="orderSuccessModal">
  <div class="modal" style="max-width:440px;text-align:center">
    <div class="modal-body" style="padding:48px 32px">
      <div style="width:80px;height:80px;background:rgba(0,255,136,0.1);border:2px solid var(--green);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:36px;color:var(--green)">✓</div>
      <h2 style="margin-bottom:8px">Order Placed!</h2>
      <p style="color:var(--text-secondary);margin-bottom:16px">Your order has been successfully placed. We'll process it shortly.</p>
      <div style="background:var(--bg-card2);border:1px solid var(--border);border-radius:var(--radius);padding:16px;margin-bottom:24px">
        <div style="font-size:11px;color:var(--text-dim);margin-bottom:6px">ORDER NUMBER</div>
        <div class="order-number-display" style="font-family:'Syne',sans-serif;font-size:18px;color:var(--red);font-weight:700"></div>
      </div>
      <div style="display:flex;gap:12px;justify-content:center">
        <a href="/nexus-gear/orders.php" class="btn btn-primary">View Orders</a>
        <a href="/nexus-gear/shop.php" class="btn btn-outline">Continue Shopping</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer-common.php'; ?>
<script src="/nexus-gear/js/app.js"></script>
<script>
async function renderCheckout() {
  if (!window.state.user) {
    document.getElementById('checkoutContent').innerHTML = `
      <div style="text-align:center;padding:80px">
        <h2 style="margin-bottom:16px">Please sign in to checkout</h2>
        <button class="btn btn-primary btn-lg" data-open-auth="login" onclick="openModal('authModal')">Sign In</button>
      </div>`;
    return;
  }

  const cartData = await fetch(`${API}?action=get_cart`,{credentials:'include'}).then(r=>r.json());
  if (!cartData.items?.length) {
    document.getElementById('checkoutContent').innerHTML = `
      <div style="text-align:center;padding:80px">
        <h2 style="margin-bottom:16px">Your cart is empty</h2>
        <a href="/nexus-gear/shop.php" class="btn btn-primary btn-lg">Start Shopping</a>
      </div>`;
    return;
  }
  window.state.cart = cartData;

  document.getElementById('checkoutContent').innerHTML = `
    <div class="checkout-grid">
      <div>
        <!-- Shipping -->
        <form id="checkoutForm">
          <div class="checkout-section">
            <div class="checkout-section-title">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              Delivery Information
            </div>
            <div class="form-row">
              <div class="form-group"><label class="form-label">Full Name</label><input type="text" name="shipping_name" class="form-input" required></div>
              <div class="form-group"><label class="form-label">Phone Number</label><input type="tel" name="shipping_phone" class="form-input" placeholder="09xxxxxxxxx" required></div>
            </div>
            <div class="form-group"><label class="form-label">Street Address</label><input type="text" name="shipping_address" class="form-input" placeholder="House No., Street, Barangay" required></div>
            <div class="form-row">
              <div class="form-group"><label class="form-label">City / Municipality</label><input type="text" name="shipping_city" class="form-input" required></div>
              <div class="form-group"><label class="form-label">Province</label><input type="text" name="shipping_province" class="form-input" required></div>
            </div>
            <div class="form-row">
              <div class="form-group"><label class="form-label">ZIP Code</label><input type="text" name="shipping_zip" class="form-input"></div>
              <div></div>
            </div>
          </div>

          <!-- Payment -->
          <div class="checkout-section">
            <div class="checkout-section-title">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
              Payment Method
            </div>
            <div class="payment-options">
              <div class="payment-option selected" data-method="cod">
                <input type="radio" name="payment_method" value="cod" checked>
                <div style="width:36px;height:36px;background:rgba(0,255,136,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px">💵</div>
                <div class="payment-option-text">
                  <div class="payment-option-name">Cash on Delivery</div>
                  <div class="payment-option-desc">Pay when your order arrives</div>
                </div>
                <div class="payment-radio-dot"></div>
              </div>
              <div class="payment-option" data-method="gcash">
                <input type="radio" name="payment_method" value="gcash">
                <div style="width:36px;height:36px;background:rgba(0,100,255,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px">📱</div>
                <div class="payment-option-text">
                  <div class="payment-option-name">GCash</div>
                  <div class="payment-option-desc">Pay via GCash mobile wallet</div>
                </div>
                <div class="payment-radio-dot"></div>
              </div>
              <div class="payment-option" data-method="credit_card">
                <input type="radio" name="payment_method" value="credit_card">
                <div style="width:36px;height:36px;background:rgba(255,107,43,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px">💳</div>
                <div class="payment-option-text">
                  <div class="payment-option-name">Credit / Debit Card</div>
                  <div class="payment-option-desc">Visa, Mastercard, JCB</div>
                </div>
                <div class="payment-radio-dot"></div>
              </div>
            </div>

            <!-- GCash extra -->
            <div id="extra_gcash" class="payment-extra hidden" style="margin-top:16px;background:var(--bg-card2);border-radius:var(--radius);padding:16px;border:1px solid var(--border)">
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label">GCash Reference Number</label>
                <input type="text" name="gcash_ref" class="form-input" placeholder="Enter GCash reference number">
              </div>
            </div>

            <!-- Card extra -->
            <div id="extra_credit_card" class="payment-extra hidden" style="margin-top:16px;background:var(--bg-card2);border-radius:var(--radius);padding:16px;border:1px solid var(--border)">
              <div class="form-row">
                <div class="form-group"><label class="form-label">Card Number</label><input type="text" class="form-input" placeholder="•••• •••• •••• ••••" maxlength="19" id="cardNum" oninput="formatCard(this)"></div>
                <div class="form-group"><label class="form-label">Last 4 Digits</label><input type="text" name="card_last4" class="form-input" placeholder="Auto" readonly id="cardLast4"></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label class="form-label">Expiry</label><input type="text" class="form-input" placeholder="MM / YY"></div>
                <div class="form-group"><label class="form-label">CVV</label><input type="text" class="form-input" placeholder="•••" maxlength="4"></div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div class="checkout-section">
            <div class="checkout-section-title">
              <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              Order Notes (Optional)
            </div>
            <textarea name="notes" class="form-textarea" placeholder="Special instructions for your order, delivery preferences, etc."></textarea>
          </div>

          <button type="submit" class="btn btn-primary btn-lg place-order-btn" style="width:100%;justify-content:center;font-size:18px">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Place Order
          </button>
        </form>
      </div>

      <!-- Order Summary -->
      <div>
        <div class="order-summary">
          <div class="order-summary-title">ORDER SUMMARY</div>

          <!-- Items -->
          <div style="margin-bottom:16px;max-height:300px;overflow-y:auto">
            ${cartData.items.map(item=>{
              const img = item.image ? `${UPLOAD}products/${item.image}` : '/nexus-gear/images/no-image.png';
              return `<div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)">
                <img src="${img}" style="width:56px;height:56px;border-radius:8px;object-fit:contain;background:var(--bg-card2)" onerror="this.src='/nexus-gear/images/no-image.png'">
                <div style="flex:1;min-width:0">
                  <div style="font-size:13px;font-weight:500;line-height:1.3">${item.name}</div>
                  <div style="font-size:12px;color:var(--text-dim);margin-top:4px">Qty: ${item.quantity}</div>
                </div>
                <div style="font-family:'Syne',sans-serif;font-size:13px;color:var(--red);white-space:nowrap">${formatPrice(item.price * item.quantity)}</div>
              </div>`;
            }).join('')}
          </div>

          <!-- Discount Code -->
          <div style="display:flex;gap:8px;margin-bottom:16px">
            <input type="text" id="discountCode" class="form-input" placeholder="Promo code" style="flex:1">
            <button id="applyDiscountBtn" class="btn btn-outline btn-sm">Apply</button>
          </div>
          <div id="discountRow" class="discount-badge hidden">
            <span>Discount Applied!</span>
            <span id="discountSaved" style="color:var(--green)"></span>
          </div>

          <!-- Totals -->
          <div class="summary-row"><span style="color:var(--text-secondary)">Subtotal</span><span id="summarySubtotal"></span></div>
          <div class="summary-row"><span style="color:var(--text-secondary)">Shipping</span><span style="color:var(--green)">FREE</span></div>
          <div class="summary-row" id="discountAmountRow" style="display:none"><span style="color:var(--green)">Discount</span><span id="summaryDiscount" style="color:var(--green)"></span></div>
          <div class="summary-total">
            <span class="summary-total-label">Total</span>
            <span class="summary-total-value" id="summaryTotal"></span>
          </div>
        </div>
      </div>
    </div>`;

  // Pre-fill user info
  const u = window.state.user;
  if (u) {
    const f = document.getElementById('checkoutForm');
    if(f.shipping_name) f.shipping_name.value = u.name||'';
    if(f.shipping_phone) f.shipping_phone.value = u.phone||'';
    if(f.shipping_address) f.shipping_address.value = u.address_line||'';
    if(f.shipping_city) f.shipping_city.value = u.city||'';
    if(f.shipping_province) f.shipping_province.value = u.province||'';
    if(f.shipping_zip) f.shipping_zip.value = u.zip_code||'';
  }

  initCheckout();
}

function formatCard(input) {
  let v = input.value.replace(/\D/g,'').substring(0,16);
  input.value = v.replace(/(.{4})/g,'$1 ').trim();
  document.getElementById('cardLast4').value = v.slice(-4);
}

document.addEventListener('DOMContentLoaded', async () => {
  await checkAuth();
  renderCheckout();
});
</script>
</body></html>
