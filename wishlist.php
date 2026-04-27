<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Wishlist — Nexus Gear</title>
<link rel="icon" href="/nexus-gear/images/logo.png">
<link rel="stylesheet" href="/nexus-gear/css/main.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<div style="height:64px"></div>

<div style="background:linear-gradient(180deg,rgba(0,212,255,0.05) 0%,transparent 100%);border-bottom:1px solid var(--border);padding:32px 0">
  <div class="container">
    <div class="breadcrumb" style="margin-bottom:10px"><a href="/nexus-gear/index.php">Home</a><span class="breadcrumb-sep">/</span><span>Wishlist</span></div>
    <h1 style="font-size:1.8rem">My Wishlist</h1>
  </div>
</div>

<div class="container" style="padding:40px 0 80px">
  <div id="wishlistContent"><div style="text-align:center;padding:80px"><div class="spinner"></div></div></div>
</div>

<?php include __DIR__ . '/includes/footer-common.php'; ?>
<script src="/nexus-gear/js/app.js"></script>
<script>
async function loadWishlist() {
  if (!window.state.user) {
    document.getElementById('wishlistContent').innerHTML = `<div style="text-align:center;padding:80px"><h2 style="margin-bottom:16px">Please sign in to view your wishlist</h2><button class="btn btn-primary btn-lg" onclick="openModal('authModal')">Sign In</button></div>`;
    return;
  }
  const data = await fetch(`${API}?action=get_wishlist`,{credentials:'include'}).then(r=>r.json());
  if (!data.success || !data.items.length) {
    document.getElementById('wishlistContent').innerHTML = `<div style="text-align:center;padding:80px"><div style="font-size:64px;margin-bottom:20px;opacity:0.2">♡</div><h2 style="margin-bottom:12px">Your wishlist is empty</h2><p style="color:var(--text-dim);margin-bottom:28px">Save items you love and come back to them later.</p><a href="/nexus-gear/shop.php" class="btn btn-primary btn-lg">Browse Products</a></div>`;
    return;
  }
  document.getElementById('wishlistContent').innerHTML = `
    <div style="margin-bottom:24px;display:flex;justify-content:space-between;align-items:center">
      <span style="color:var(--text-secondary)">${data.items.length} saved item${data.items.length!==1?'s':''}</span>
      <button class="btn btn-ghost btn-sm" onclick="clearWishlist()">Clear All</button>
    </div>
    <div class="product-grid">${data.items.map(p=>`
      <div class="product-card" onclick="window.location='/nexus-gear/product.php?slug=${p.slug}'">
        <div class="product-img-wrap">
          <img src="${p.image ? UPLOAD+'products/'+p.image : '/nexus-gear/images/no-image.png'}" alt="${p.name}" loading="lazy" onerror="this.src='/nexus-gear/images/no-image.png'">
          <div class="product-actions-overlay">
            <button class="product-action-btn active" onclick="event.stopPropagation();removeFromWishlist(${p.id},this)" title="Remove from Wishlist">
              <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </button>
          </div>
        </div>
        <div class="product-info">
          <div class="product-brand">${p.brand_name||''}</div>
          <div class="product-name">${p.name}</div>
          <div class="product-rating">${[1,2,3,4,5].map(i=>`<span class="star ${i<=Math.round(p.rating_avg)?'filled':'empty'}">★</span>`).join('')}<span class="rating-count">(${p.rating_count||0})</span></div>
          <div class="product-price">
            <span class="price-current">${formatPrice(p.price)}</span>
            ${p.original_price>p.price?`<span class="price-original">${formatPrice(p.original_price)}</span>`:''}
          </div>
        </div>
        <div class="product-add-bar">
          <button class="btn-add-cart" onclick="event.stopPropagation();addToCart(${p.id})" ${parseInt(p.stock)===0?'disabled style="opacity:0.4;cursor:not-allowed"':''}>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            ${parseInt(p.stock)===0?'Out of Stock':'Add to Cart'}
          </button>
        </div>
      </div>`).join('')}</div>`;
}

async function removeFromWishlist(productId, btn) {
  const data = await apiCall('toggle_wishlist',{product_id:productId},'POST');
  if (data.success) { toast('Removed from wishlist.','info'); loadWishlist(); }
}

async function clearWishlist() {
  window.ngConfirm('Clear Wishlist','Remove all items from your wishlist?', async ()=>{
    const data = await fetch(`${API}?action=get_wishlist`,{credentials:'include'}).then(r=>r.json());
    if (data.success) {
      for (const item of data.items) {
        await apiCall('toggle_wishlist',{product_id:item.id},'POST');
      }
      toast('Wishlist cleared.','info');
      loadWishlist();
    }
  });
}

document.addEventListener('DOMContentLoaded', async ()=>{
  await checkAuth();
  loadWishlist();
});
</script>
</body></html>
