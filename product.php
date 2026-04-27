<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Product — Nexus Gear</title>
<link rel="icon" href="/nexus-gear/images/logo.png">
<link rel="stylesheet" href="/nexus-gear/css/main.css">
<style>
.spec-table{width:100%;border-collapse:collapse}
.spec-table tr:nth-child(even) td{background:rgba(0,212,255,0.03)}
.spec-table td{padding:10px 14px;font-size:13px;border-bottom:1px solid var(--border)}
.spec-table td:first-child{color:var(--text-dim);width:40%;font-weight:500}
.review-item{padding:20px;background:var(--bg-card2);border-radius:var(--radius);border:1px solid var(--border);margin-bottom:14px}
.reviewer-avatar{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid var(--border)}
.qty-selector{display:flex;align-items:center;gap:0;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;width:fit-content}
.qty-selector button{width:40px;height:44px;background:var(--bg-card2);border:none;color:var(--text-primary);font-size:18px;cursor:pointer;transition:var(--transition)}
.qty-selector button:hover{background:var(--red-dim);color:var(--red)}
.qty-selector span{width:56px;text-align:center;font-family:'Syne',sans-serif;font-weight:700;font-size:16px}
</style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>
<div style="height:64px"></div>

<div class="container" style="padding:40px 0 80px" id="productContent">
  <div style="text-align:center;padding:80px"><div class="spinner"></div></div>
</div>

<!-- Related Products -->
<div style="background:var(--bg-card);border-top:1px solid var(--border);padding:60px 0" id="relatedSection" class="hidden">
  <div class="container">
    <div class="section-header flex justify-between items-center">
      <div>
        <div class="section-eyebrow">YOU MAY ALSO LIKE</div>
        <h2 class="section-title">Related <span class="accent">Products</span></h2>
      </div>
    </div>
    <div class="product-grid" id="relatedProducts"></div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer-common.php'; ?>
<script src="/nexus-gear/js/app.js"></script>
<script>
const slug = new URLSearchParams(window.location.search).get('slug');
let product = null;
let selectedQty = 1;

async function loadProduct() {
  if (!slug) { window.location = '/nexus-gear/shop.php'; return; }
  const data = await fetch(`${API}?action=get_product&slug=${encodeURIComponent(slug)}`, {credentials:'include'}).then(r=>r.json());
  if (!data.success) { document.getElementById('productContent').innerHTML = '<div style="text-align:center;padding:80px"><h2>Product not found</h2><a href="/nexus-gear/shop.php" class="btn btn-primary" style="margin-top:20px">Back to Shop</a></div>'; return; }
  product = data.product;
  document.title = `${product.name} — Nexus Gear`;
  renderProduct();
  renderRelated();
}

function renderProduct() {
  const p = product;
  const imgs = p.images || [];
  const primaryImg = imgs.find(i=>i.is_primary)?.image_path || imgs[0]?.image_path;
  const outOfStock = parseInt(p.stock) === 0;
  const disc = p.original_price > p.price ? Math.round((p.original_price - p.price) / p.original_price * 100) : 0;

  let specsHtml = '';
  if (p.specifications) {
    try {
      const specs = typeof p.specifications === 'string' ? JSON.parse(p.specifications) : p.specifications;
      specsHtml = Object.entries(specs).map(([k,v])=>`<tr><td>${k}</td><td>${v}</td></tr>`).join('');
    } catch(e) { specsHtml = `<tr><td colspan="2">${p.specifications}</td></tr>`; }
  }

  const ratingDist = [5,4,3,2,1].map(r=>{
    const count = p.reviews.filter(rv=>rv.rating===r).length;
    const pct = p.reviews.length ? Math.round(count/p.reviews.length*100) : 0;
    return {r,count,pct};
  });

  document.getElementById('productContent').innerHTML = `
    <div class="breadcrumb" style="margin-bottom:24px">
      <a href="/nexus-gear/index.php">Home</a>
      <span class="breadcrumb-sep">/</span>
      <a href="/nexus-gear/shop.php">Shop</a>
      <span class="breadcrumb-sep">/</span>
      <a href="/nexus-gear/shop.php?category=${p.category_slug}">${p.category_name||'Products'}</a>
      <span class="breadcrumb-sep">/</span>
      <span>${p.name}</span>
    </div>

    <div class="product-detail-grid">
      <!-- Gallery -->
      <div>
        <div class="product-gallery-main" id="mainGallery">
          <img src="${primaryImg ? UPLOAD+'products/'+primaryImg : '/nexus-gear/images/no-image.png'}" alt="${p.name}" id="mainProductImg" onerror="this.src='/nexus-gear/images/no-image.png'">
        </div>
        ${imgs.length > 1 ? `<div class="product-thumbnails">${imgs.map(img=>`
          <img src="${UPLOAD}products/${img.image_path}" class="product-thumb ${img.is_primary?'active':''}"
            onclick="switchImage('${UPLOAD}products/${img.image_path}',this)"
            onerror="this.style.display='none'">`).join('')}</div>` : ''}
      </div>

      <!-- Info -->
      <div>
        <!-- Brand + Category chips -->
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
          ${p.category_name?`<a href="/nexus-gear/shop.php?category=${p.category_slug}" style="display:inline-flex;align-items:center;padding:4px 12px;background:var(--red-dim);border:1px solid rgba(232,25,44,.20);border-radius:50px;font-size:12px;font-weight:600;color:var(--red);text-decoration:none">${p.category_name}</a>`:''}
          ${p.brand_name?`<a href="/nexus-gear/shop.php?brand=${encodeURIComponent(p.brand_name.toLowerCase())}" style="display:inline-flex;align-items:center;padding:4px 12px;background:var(--bg-card2);border:1px solid var(--border-med);border-radius:50px;font-size:12px;font-weight:600;color:var(--text-secondary);text-decoration:none">${p.brand_name}</a>`:''}
        </div>

        <h1 style="font-size:1.9rem;margin-bottom:12px;color:var(--text-primary)">${p.name}</h1>

        <!-- Rating row -->
        <div class="product-rating" style="margin-bottom:18px">
          ${[1,2,3,4,5].map(i=>`<span class="star ${i<=Math.round(p.rating_avg)?'filled':'empty'}">★</span>`).join('')}
          <span style="font-size:15px;font-weight:700;color:var(--amber);margin-left:6px">${parseFloat(p.rating_avg||0).toFixed(1)}</span>
          <span class="rating-count">(${p.rating_count||0} reviews)</span>
          <span style="color:var(--text-dim);margin-left:10px;font-size:12px">${p.total_sold||0} sold</span>
        </div>

        <!-- Price block -->
        <div style="padding:18px;background:var(--bg-card2);border-radius:var(--radius-lg);border:1px solid var(--border);margin-bottom:20px">
          <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
            <span style="font-family:'Syne',sans-serif;font-size:34px;font-weight:800;color:var(--text-primary);line-height:1">${formatPrice(p.price)}</span>
            ${parseFloat(p.original_price) > parseFloat(p.price) ? `
              <span style="font-size:18px;color:var(--text-dim);text-decoration:line-through;line-height:1">${formatPrice(p.original_price)}</span>
              <span style="background:var(--red);color:#fff;padding:5px 10px;border-radius:6px;font-size:13px;font-weight:700">${disc}% OFF</span>
            ` : ''}
          </div>
          ${parseFloat(p.original_price) > parseFloat(p.price) ? `
            <div style="margin-top:8px;font-size:13px;color:var(--green);font-weight:600">
              You save ${formatPrice(p.original_price - p.price)}
            </div>` : ''}
        </div>

        <!-- Short desc -->
        ${p.short_description ? `<p style="color:var(--text-secondary);font-size:14px;line-height:1.8;margin-bottom:20px">${p.short_description}</p>` : ''}

        <!-- Stock status -->
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:22px;padding:10px 14px;background:var(--bg-card2);border-radius:var(--radius);border:1px solid var(--border)">
          <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:${outOfStock?'var(--red)':parseInt(p.stock)<=10?'var(--amber)':'var(--green)'}"></div>
          <span style="font-size:13px;font-weight:600;color:var(--text-primary)">${outOfStock ? 'Out of Stock' : parseInt(p.stock)<=10 ? `Only ${p.stock} left!` : `In Stock`}</span>
          ${!outOfStock ? `<span style="font-size:12px;color:var(--text-dim)">(${p.stock} available)</span>` : ''}
        </div>

        ${!outOfStock ? `
        <!-- Qty selector -->
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:14px;flex-wrap:wrap">
          <div class="qty-selector">
            <button onclick="changeQty(-1)">−</button>
            <span id="qtyDisplay">1</span>
            <button onclick="changeQty(1)">+</button>
          </div>
          <span style="font-size:12px;color:var(--text-dim)">Max ${p.stock} per order</span>
        </div>

        <!-- CTA buttons -->
        <div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap">
          <button class="btn btn-primary btn-lg" style="flex:1;justify-content:center;min-width:160px" onclick="addToCart(${p.id}, selectedQty)">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            Add to Cart
          </button>
          <button id="buyNowBtn" class="btn btn-dark btn-lg" style="flex:1;justify-content:center;min-width:160px" onclick="openBuyNow(${p.id})">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="13 17 18 12 13 7"/><path d="M6 12h12"/></svg>
            Buy Now
          </button>
        </div>` : ''}

        <!-- Wishlist -->
        <button class="btn btn-outline" style="width:100%;justify-content:center;margin-bottom:0" onclick="toggleWishlist(${p.id},this)">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          Save to Wishlist
        </button>
      </div>
    </div>

    <!-- Tabs Section — Description / Specifications / Reviews -->
    <div style="margin-top:60px">
      <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('desc',this)">Description</button>
        <button class="tab-btn" onclick="switchTab('specs',this)">Specifications</button>
        <button class="tab-btn" onclick="switchTab('reviews',this)">Reviews (${p.reviews.length})</button>
      </div>

      <!-- Description -->
      <div id="tab-desc" class="tab-panel">
        <div style="color:var(--text-secondary);font-size:15px;line-height:1.9;max-width:820px">${p.description||'No description available.'}</div>
      </div>

      <!-- Specifications -->
      <div id="tab-specs" class="tab-panel hidden">
        ${specsHtml
          ? `<div style="max-width:700px"><table class="spec-table">${specsHtml}</table></div>`
          : `<div style="padding:40px 0;text-align:center;color:var(--text-dim)">
               <div style="font-size:48px;margin-bottom:14px">📋</div>
               <p>No specifications have been added for this product yet.</p>
             </div>`
        }
      </div>

      <!-- Reviews -->
      <div id="tab-reviews" class="tab-panel hidden">
        <div style="display:grid;grid-template-columns:280px 1fr;gap:40px;margin-bottom:32px">
          <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:32px;text-align:center">
            <div style="font-family:'Syne',sans-serif;font-size:64px;font-weight:900;color:var(--red);line-height:1">${parseFloat(p.rating_avg||0).toFixed(1)}</div>
            <div style="margin:12px 0">${[1,2,3,4,5].map(i=>`<span style="font-size:24px;color:${i<=Math.round(p.rating_avg)?'#fbbf24':'var(--text-dim)'}">★</span>`).join('')}</div>
            <div style="color:var(--text-dim);font-size:13px">Based on ${p.rating_count||0} reviews</div>
          </div>
          <div style="display:flex;flex-direction:column;justify-content:center;gap:10px">
            ${ratingDist.map(({r,count,pct})=>`
              <div style="display:flex;align-items:center;gap:12px">
                <span style="font-size:13px;color:var(--text-dim);width:20px;text-align:right">${r}</span>
                <span style="color:#fbbf24;font-size:14px">★</span>
                <div class="progress-bar" style="flex:1"><div class="progress-fill" style="width:${pct}%"></div></div>
                <span style="font-size:12px;color:var(--text-dim);width:30px">${count}</span>
              </div>`).join('')}
          </div>
        </div>

        <!-- Add Review -->
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;margin-bottom:24px" id="reviewFormSection">
          <h3 style="font-size:16px;margin-bottom:20px">Write a Review</h3>
          <form id="reviewForm" onsubmit="submitReview(event)">
            <div class="form-group">
              <label class="form-label">Your Rating</label>
              <div class="star-input" id="starInput">
                ${[5,4,3,2,1].map(r=>`<input type="radio" name="rating" id="star${r}" value="${r}"><label for="star${r}">★</label>`).join('')}
              </div>
            </div>
            <div class="form-group"><label class="form-label">Review Title</label><input type="text" name="title" class="form-input" placeholder="Summarize your experience"></div>
            <div class="form-group"><label class="form-label">Your Review</label><textarea name="body" class="form-textarea" placeholder="Share details about your purchase and experience..."></textarea></div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
          </form>
        </div>

        <!-- Reviews list -->
        <div id="reviewsList">
          ${p.reviews.length ? p.reviews.map(r=>`
            <div class="review-item">
              <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                <img src="${r.user_avatar && r.user_avatar!=='default.png' ? UPLOAD+'avatars/'+r.user_avatar : '/nexus-gear/images/default-avatar.png'}" class="reviewer-avatar" onerror="this.src='/nexus-gear/images/default-avatar.png'">
                <div>
                  <div style="font-weight:600;font-size:14px">${r.user_name}</div>
                  <div style="display:flex;align-items:center;gap:8px">
                    <div>${[1,2,3,4,5].map(i=>`<span style="color:${i<=r.rating?'#fbbf24':'var(--text-dim)'}">★</span>`).join('')}</div>
                    <span style="font-size:11px;color:var(--text-dim)">${new Date(r.created_at).toLocaleDateString('en-PH',{year:'numeric',month:'short',day:'numeric'})}</span>
                  </div>
                </div>
              </div>
              ${r.title?`<div style="font-weight:600;margin-bottom:6px">${r.title}</div>`:''}
              <p style="font-size:13px;color:var(--text-secondary);line-height:1.7">${r.body||''}</p>
            </div>`).join('') : '<div style="text-align:center;padding:40px;color:var(--text-dim)">No reviews yet. Be the first to review!</div>'}
        </div>
      </div>
    </div>
  `;
}

function renderRelated() {
  if (!product.related?.length) return;
  document.getElementById('relatedSection').classList.remove('hidden');
  document.getElementById('relatedProducts').innerHTML = product.related.map(p=>renderProductCard(p)).join('');
}

function switchImage(src, thumb) {
  document.getElementById('mainProductImg').src = src;
  document.querySelectorAll('.product-thumb').forEach(t=>t.classList.remove('active'));
  thumb.classList.add('active');
}

function changeQty(delta) {
  selectedQty = Math.max(1, Math.min(parseInt(product.stock)||1, selectedQty + delta));
  document.getElementById('qtyDisplay').textContent = selectedQty;
}

function switchTab(tab, btn) {
  document.querySelectorAll('.tab-panel').forEach(p=>p.classList.add('hidden'));
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById('tab-'+tab).classList.remove('hidden');
  btn.classList.add('active');
}

// ── BUY NOW (FIXED) ────────────────────────────────────────────────────────
// Adds the current quantity to cart then redirects straight to checkout.
// The original code called openBuyNow(id, name, price, img) but that function
// was never defined anywhere, causing "unknown function" / silent failure.
async function openBuyNow(productId) {
  if (!window.state?.user) {
    toast('Please sign in to continue.', 'warning');
    openModal('authModal');
    return;
  }

  const btn = document.getElementById('buyNowBtn');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg> Adding…`;
  }

  try {
    const data = await apiCall('add_to_cart', { product_id: productId, quantity: selectedQty }, 'POST');

    if (data.success) {
      window.location.href = '/nexus-gear/checkout.php';
    } else {
      toast(data.message || 'Could not add to cart. Please try again.', 'error');
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="13 17 18 12 13 7"/><path d="M6 12h12"/></svg> Buy Now`;
      }
    }
  } catch (e) {
    toast('Something went wrong. Please try again.', 'error');
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="13 17 18 12 13 7"/><path d="M6 12h12"/></svg> Buy Now`;
    }
  }
}

async function submitReview(e) {
  e.preventDefault();
  if (!window.state.user) { openModal('authModal'); return; }
  const form = e.target;
  const rating = form.querySelector('input[name=rating]:checked')?.value;
  if (!rating) { toast('Please select a rating.','warning'); return; }
  const btn = form.querySelector('button[type=submit]');
  btn.disabled=true; btn.textContent='Submitting...';
  const data = await apiCall('add_review',{product_id:product.id,rating,title:form.title.value,body:form.body.value},'POST');
  btn.disabled=false; btn.textContent='Submit Review';
  if (data.success) { toast('Review submitted!','success'); setTimeout(()=>window.location.reload(),1000); }
  else toast(data.message,'error');
}

document.addEventListener('DOMContentLoaded', loadProduct);
</script>
</body></html>