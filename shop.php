<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Shop — Nexus Gear</title>
<link rel="icon" href="/nexus-gear/images/logo.png">
<link rel="stylesheet" href="/nexus-gear/css/main.css">
<style>
/* ── Shop page layout ── */
.shop-layout { display:grid; grid-template-columns:260px 1fr; gap:28px; align-items:start; }

/* Filter sidebar */
.filter-sidebar {
  background:var(--bg-white); border:1px solid var(--border);
  border-radius:var(--radius-lg); padding:22px; position:sticky; top:80px;
  box-shadow:var(--shadow-sm);
}
.filter-section { margin-bottom:24px; padding-bottom:24px; border-bottom:1px solid var(--border); }
.filter-section:last-child { border-bottom:none; margin-bottom:0; padding-bottom:0; }
.filter-title { font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:var(--red); margin-bottom:12px; }
.filter-option { display:flex; align-items:center; gap:9px; padding:5px 0; cursor:pointer; }
.filter-option input[type=checkbox], .filter-option input[type=radio] { width:15px; height:15px; accent-color:var(--red); cursor:pointer; }
.filter-option label { font-size:13px; color:var(--text-secondary); cursor:pointer; flex:1; }
.filter-option label:hover { color:var(--text-primary); }
.filter-count { font-size:11px; color:var(--text-dim); }
.price-range { display:flex; gap:8px; align-items:center; }
.price-input { width:100%; padding:8px 10px; background:var(--bg-card2); border:1.5px solid var(--border-med); border-radius:8px; color:var(--text-primary); font-size:13px; outline:none; }
.price-input:focus { border-color:var(--red); }
.shop-toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; padding:14px 18px; background:var(--bg-white); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); }
.sort-select { background:var(--bg-card2); border:1px solid var(--border-med); border-radius:8px; color:var(--text-primary); padding:7px 12px; font-size:13px; outline:none; cursor:pointer; }
.results-count { font-size:13px; color:var(--text-secondary); }
.results-count span { color:var(--text-primary); font-weight:700; }
.pagination { display:flex; align-items:center; justify-content:center; gap:6px; margin-top:40px; }
.page-btn { width:36px; height:36px; border-radius:8px; background:var(--bg-white); border:1px solid var(--border); color:var(--text-secondary); cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; transition:var(--transition); }
.page-btn:hover, .page-btn.active { background:var(--red); border-color:var(--red); color:#fff; }
.active-filters { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:16px; }
.filter-tag { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; background:var(--red-dim); border:1px solid rgba(232,25,44,.20); border-radius:5px; font-size:12px; font-weight:700; color:var(--red); }
.filter-tag button { background:none; border:none; color:var(--red); cursor:pointer; font-size:13px; line-height:1; }

/* ── Shop product grid — always proper grid (never flex scroll) ── */
#shopProducts {
  display:grid !important;
  grid-template-columns:repeat(3, 1fr) !important;
  gap:18px !important;
  overflow:visible !important;
  flex-wrap:unset !important;
}
#shopProducts .product-card {
  flex:unset !important;
  width:auto !important;
  min-width:0 !important;
}

/* ── Mobile filter toggle ── */
.filter-toggle-btn {
  display:none;
  width:100%; padding:12px 16px;
  background:var(--bg-white); border:1px solid var(--border);
  border-radius:var(--radius-lg); cursor:pointer;
  font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:14px;
  color:var(--text-primary); text-align:left;
  margin-bottom:16px; box-shadow:var(--shadow-sm);
  align-items:center; justify-content:space-between;
}
.filter-toggle-btn svg { color:var(--red); }

@media (max-width:900px) {
  .shop-layout { grid-template-columns:1fr; }

  .filter-toggle-btn { display:flex; }

  .filter-sidebar {
    position:static;
    display:none; /* hidden by default on mobile */
    margin-bottom:16px;
  }
  .filter-sidebar.mobile-open { display:block; }

  #shopProducts {
    grid-template-columns:repeat(2, 1fr) !important;
    gap:12px !important;
  }
}

@media (max-width:480px) {
  #shopProducts {
    grid-template-columns:repeat(2, 1fr) !important;
    gap:10px !important;
  }
  #shopProducts .product-card .product-name { font-size:13px; }
  #shopProducts .product-card .price-current { font-size:14px; }
  #shopProducts .product-card .btn-add-cart,
  #shopProducts .product-card .btn-buy-now { font-size:10px; padding:6px 4px; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<!-- Page Header -->
<div style="height:64px"></div>
<div style="background:linear-gradient(180deg,rgba(0,212,255,0.05) 0%,transparent 100%);border-bottom:1px solid var(--border);padding:40px 0">
  <div class="container">
    <div class="breadcrumb" style="margin-bottom:12px">
      <a href="/nexus-gear/index.php">Home</a>
      <span class="breadcrumb-sep">/</span>
      <span id="breadcrumbCurrent">All Products</span>
    </div>
    <h1 style="font-size:2rem" id="pageTitle">All Products</h1>
  </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:80px">

  <!-- Mobile filter toggle -->
  <button type="button" class="filter-toggle-btn" id="filterToggleBtn" onclick="toggleMobileFilters()">
    <span>
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:8px"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
      Filters
    </span>
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" id="filterArrow"><polyline points="6 9 12 15 18 9"/></svg>
  </button>

  <div class="shop-layout">
    <!-- Filters -->
    <aside class="filter-sidebar" id="filterSidebar">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <span style="font-family:'Syne',sans-serif;font-size:12px;letter-spacing:1px">FILTERS</span>
        <button onclick="clearAllFilters()" class="btn btn-ghost btn-sm" style="font-size:11px">Clear All</button>
      </div>

      <!-- Categories -->
      <div class="filter-section">
        <div class="filter-title">CATEGORY</div>
        <div id="categoryFilters">
          <div class="skeleton" style="height:160px;border-radius:8px"></div>
        </div>
      </div>

      <!-- Brands -->
      <div class="filter-section">
        <div class="filter-title">BRAND</div>
        <div id="brandFilters">
          <div class="skeleton" style="height:180px;border-radius:8px"></div>
        </div>
      </div>

      <!-- Price -->
      <div class="filter-section">
        <div class="filter-title">PRICE RANGE</div>
        <div class="price-range">
          <input type="number" class="price-input" id="priceMin" placeholder="Min" min="0">
          <span style="color:var(--text-dim)">—</span>
          <input type="number" class="price-input" id="priceMax" placeholder="Max" min="0">
        </div>
        <button onclick="applyPriceFilter()" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;margin-top:12px">Apply</button>
      </div>

      <!-- Availability -->
      <div class="filter-section">
        <div class="filter-title">AVAILABILITY</div>
        <div class="filter-option">
          <input type="checkbox" id="inStock" onchange="loadProducts()">
          <label for="inStock">In Stock Only</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="onSale" onchange="loadProducts()">
          <label for="onSale">On Sale</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="featuredOnly" onchange="loadProducts()">
          <label for="featuredOnly">Featured</label>
        </div>
      </div>

      <!-- Rating -->
      <div class="filter-section">
        <div class="filter-title">MIN RATING</div>
        <?php for($r=5;$r>=1;$r--): ?>
        <div class="filter-option">
          <input type="radio" name="minRating" id="rating<?=$r?>" value="<?=$r?>" onchange="loadProducts()">
          <label for="rating<?=$r?>">
            <?php for($i=1;$i<=5;$i++): ?><span style="color:<?=$i<=$r?'#fbbf24':'var(--text-dim)'?>">★</span><?php endfor; ?>
            &amp; up
          </label>
        </div>
        <?php endfor; ?>
      </div>
    </aside>

    <!-- Products -->
    <div>
      <!-- Active filter tags -->
      <div class="active-filters" id="activeFilters"></div>

      <!-- Toolbar -->
      <div class="shop-toolbar">
        <div class="results-count"><span id="resultCount">0</span> products found</div>
        <div style="display:flex;align-items:center;gap:12px">
          <label style="font-size:12px;color:var(--text-dim)">Sort:</label>
          <select class="sort-select" id="sortSelect" onchange="loadProducts()">
            <option value="newest">Newest First</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
            <option value="popular">Most Popular</option>
            <option value="rating">Top Rated</option>
          </select>
        </div>
      </div>

      <div class="product-grid" id="shopProducts">
        <?php for($i=0;$i<8;$i++): ?>
        <div class="skeleton" style="height:380px;border-radius:16px"></div>
        <?php endfor; ?>
      </div>

      <div class="pagination" id="pagination"></div>
    </div>
  </div><!-- end shop-layout -->
</div><!-- end container -->

<?php include __DIR__ . '/includes/footer-common.php'; ?>

<script src="/nexus-gear/js/app.js"></script>
<script>
const urlParams = new URLSearchParams(window.location.search);
let currentPage = 1;
let selectedCategories = urlParams.get('category') ? [urlParams.get('category')] : [];
let selectedBrands = urlParams.get('brand') ? [urlParams.get('brand')] : [];
let priceMin = '', priceMax = '';

// Load filter options
async function loadFilterOptions() {
  const [catData, brandData] = await Promise.all([
    fetch(`${API}?action=get_categories`).then(r=>r.json()),
    fetch(`${API}?action=get_brands`).then(r=>r.json())
  ]);

  if(catData.success) {
    document.getElementById('categoryFilters').innerHTML = catData.categories.map(c=>`
      <div class="filter-option">
        <input type="checkbox" id="cat_${c.slug}" value="${c.slug}" ${selectedCategories.includes(c.slug)?'checked':''} onchange="toggleCategory('${c.slug}')">
        <label for="cat_${c.slug}">${c.name}</label>
        <span class="filter-count">${c.product_count}</span>
      </div>`).join('');
  }

  if(brandData.success) {
    document.getElementById('brandFilters').innerHTML = brandData.brands.map(b=>`
      <div class="filter-option">
        <input type="checkbox" id="brand_${b.slug}" value="${b.slug}" ${selectedBrands.includes(b.slug)?'checked':''} onchange="toggleBrand('${b.slug}')">
        <label for="brand_${b.slug}">${b.name}</label>
        <span class="filter-count">${b.product_count}</span>
      </div>`).join('');
  }
}

function toggleCategory(slug) {
  const idx = selectedCategories.indexOf(slug);
  if(idx>-1) selectedCategories.splice(idx,1); else selectedCategories.push(slug);
  currentPage=1; loadProducts(); updateActiveFilters();
}

function toggleBrand(slug) {
  const idx = selectedBrands.indexOf(slug);
  if(idx>-1) selectedBrands.splice(idx,1); else selectedBrands.push(slug);
  currentPage=1; loadProducts(); updateActiveFilters();
}

function applyPriceFilter() {
  priceMin = document.getElementById('priceMin').value;
  priceMax = document.getElementById('priceMax').value;
  currentPage=1; loadProducts(); updateActiveFilters();
}

function clearAllFilters() {
  selectedCategories=[]; selectedBrands=[];
  priceMin=''; priceMax='';
  document.getElementById('priceMin').value='';
  document.getElementById('priceMax').value='';
  document.querySelectorAll('.filter-sidebar input[type=checkbox]').forEach(i=>i.checked=false);
  document.querySelectorAll('.filter-sidebar input[type=radio]').forEach(i=>i.checked=false);
  currentPage=1; loadProducts(); updateActiveFilters();
}

function updateActiveFilters() {
  const tags = [];
  selectedCategories.forEach(c=>tags.push(`<div class="filter-tag">${c} <button onclick="toggleCategory('${c}')">×</button></div>`));
  selectedBrands.forEach(b=>tags.push(`<div class="filter-tag">${b} <button onclick="toggleBrand('${b}')">×</button></div>`));
  if(priceMin||priceMax) tags.push(`<div class="filter-tag">₱${priceMin||0}–${priceMax||'∞'} <button onclick="priceMin='';priceMax='';loadProducts();updateActiveFilters()">×</button></div>`);
  document.getElementById('activeFilters').innerHTML = tags.join('');
}

async function loadProducts() {
  const container = document.getElementById('shopProducts');
  container.innerHTML = Array(8).fill('<div class="skeleton" style="height:380px;border-radius:16px"></div>').join('');

  const sort = document.getElementById('sortSelect').value;
  const inStock = document.getElementById('inStock')?.checked;
  const onSale = document.getElementById('onSale')?.checked;
  const featured = document.getElementById('featuredOnly')?.checked;
  const minRating = document.querySelector('input[name=minRating]:checked')?.value || '';
  const q = urlParams.get('q') || '';

  let url = `${API}?action=get_products&page=${currentPage}&limit=12&sort=${sort}`;
  if(selectedCategories.length) url += `&category=${selectedCategories[0]}`;
  if(selectedBrands.length) url += `&brand=${selectedBrands[0]}`;
  if(priceMin) url += `&price_min=${priceMin}`;
  if(priceMax) url += `&price_max=${priceMax}`;
  if(inStock) url += '&in_stock=1';
  if(onSale) url += '&sale=1';
  if(featured || urlParams.get('featured')) url += '&is_featured=1';
  if(minRating) url += `&min_rating=${minRating}`;
  if(q) url += `&q=${encodeURIComponent(q)}`;

  const data = await fetch(url,{credentials:'include'}).then(r=>r.json());
  if(data.success) {
    document.getElementById('resultCount').textContent = data.total;
    if(!data.products.length) {
      container.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:80px 20px;color:var(--text-dim)"><div style="font-size:48px;margin-bottom:16px">◎</div><h3 style="margin-bottom:8px">No products found</h3><p>Try adjusting your filters or search terms.</p></div>`;
    } else {
      container.innerHTML = data.products.map(p=>renderProductCard(p)).join('');
    }
    renderPagination(data.pages);
  }
}

function renderPagination(pages) {
  const el = document.getElementById('pagination');
  if(pages<=1){el.innerHTML='';return;}
  let html = '';
  if(currentPage>1) html+=`<button class="page-btn" onclick="goPage(${currentPage-1})">‹</button>`;
  for(let i=1;i<=pages;i++){
    if(i===1||i===pages||Math.abs(i-currentPage)<=2)
      html+=`<button class="page-btn ${i===currentPage?'active':''}" onclick="goPage(${i})">${i}</button>`;
    else if(Math.abs(i-currentPage)===3) html+='<span style="color:var(--text-dim);padding:0 4px">…</span>';
  }
  if(currentPage<pages) html+=`<button class="page-btn" onclick="goPage(${currentPage+1})">›</button>`;
  el.innerHTML=html;
}

function goPage(p){currentPage=p;loadProducts();window.scrollTo({top:300,behavior:'smooth'});}

// Update page title
const q = urlParams.get('q');
const cat = selectedCategories[0];
if(q){ document.getElementById('pageTitle').textContent=`Search: "${q}"`; document.getElementById('breadcrumbCurrent').textContent='Search'; }
else if(cat){ document.getElementById('pageTitle').textContent=cat.replace(/-/g,' ').replace(/\b\w/g,l=>l.toUpperCase()); document.getElementById('breadcrumbCurrent').textContent=cat.replace(/-/g,' ').replace(/\b\w/g,l=>l.toUpperCase()); }

document.addEventListener('DOMContentLoaded',()=>{
  loadFilterOptions();
  loadProducts();
  updateActiveFilters();
});

function toggleMobileFilters() {
  const sidebar = document.getElementById('filterSidebar');
  const arrow   = document.getElementById('filterArrow');
  const isOpen  = sidebar.classList.toggle('mobile-open');
  if (arrow) arrow.style.transform = isOpen ? 'rotate(180deg)' : '';
}
</script>

</body></html>
