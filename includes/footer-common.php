<?php // includes/footer-common.php ?>
<!-- Cart Sidebar -->
<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-sidebar" id="cartSidebar">
  <div class="cart-header">
    <span class="cart-title">YOUR CART</span>
    <button class="cart-close" id="cartClose">✕</button>
  </div>
  <div class="cart-items" id="cartItems"><div class="cart-empty"><p>Loading...</p></div></div>
  <div class="cart-footer">
    <div class="cart-total-row">
      <span class="cart-total-label">Total</span>
      <span class="cart-total-value" id="cartTotal">₱0.00</span>
    </div>
    <a href="/nexus-gear/checkout.php" class="btn btn-primary" style="width:100%;justify-content:center" id="cartCheckoutBtn">Proceed to Checkout</a>
    <button class="btn btn-ghost" style="width:100%;justify-content:center;margin-top:8px" id="continueShoppingBtn">Continue Shopping</button>
  </div>
</div>

<!-- Auth Modal -->
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
      <div id="loginPanel" class="auth-panel">
        <form id="loginForm">
          <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-input" placeholder="your@email.com" required></div>
          <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-input" placeholder="••••••••" required></div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">Sign In</button>
        </form>
      </div>
      <div id="registerPanel" class="auth-panel hidden">
        <form id="registerForm">
          <div class="form-group"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-input" placeholder="Juan dela Cruz" required></div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-input" placeholder="09xxxxxxxxx"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Confirm</label><input type="password" name="confirm_password" class="form-input" required></div>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">Create Account</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Confirm Modal -->
<div class="modal-overlay" id="confirmModal">
  <div class="modal" style="max-width:380px">
    <div class="modal-header">
      <span class="modal-title confirm-title">Confirm</span>
      <button class="modal-close" data-close-modal="confirmModal">✕</button>
    </div>
    <div class="modal-body"><p class="confirm-message" style="color:var(--text-secondary);font-size:14px"></p></div>
    <div class="modal-footer">
      <button class="btn btn-ghost" data-close-modal="confirmModal">Cancel</button>
      <button class="btn btn-danger confirm-yes">Confirm</button>
    </div>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
document.getElementById('continueShoppingBtn')?.addEventListener('click',()=>{
  document.getElementById('cartOverlay').classList.remove('open');
  document.getElementById('cartSidebar').classList.remove('open');
  document.body.style.overflow='';
});
</script>
