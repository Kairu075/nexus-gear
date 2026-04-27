<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Profile — Nexus Gear</title>
<link rel="icon" href="/nexus-gear/images/logo.png">
<link rel="stylesheet" href="/nexus-gear/css/main.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
.profile-grid{display:grid;grid-template-columns:280px 1fr;gap:32px;align-items:start}
.profile-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:32px;text-align:center;position:sticky;top:90px}
.profile-avatar-wrap{position:relative;width:120px;height:120px;margin:0 auto 20px}
.profile-avatar{width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid var(--red)}
.avatar-upload-btn{position:absolute;bottom:0;right:0;width:34px;height:34px;background:var(--red);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid var(--bg-page)}
.avatar-upload-btn svg{color:#000}
.content-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:28px;margin-bottom:20px}
.content-card-title{font-family:'Syne',sans-serif;font-size:11px;letter-spacing:2px;color:var(--red);margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--border)}
.order-row{display:flex;align-items:center;gap:16px;padding:16px;background:var(--bg-card2);border-radius:var(--radius);border:1px solid var(--border);margin-bottom:10px;cursor:pointer;transition:var(--transition)}
.order-row:hover{border-color:var(--border-med)}
</style>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<div style="height:64px"></div>

<div style="background:linear-gradient(180deg,rgba(0,212,255,0.05) 0%,transparent 100%);border-bottom:1px solid var(--border);padding:32px 0">
  <div class="container">
    <div class="breadcrumb" style="margin-bottom:10px"><a href="/nexus-gear/index.php">Home</a><span class="breadcrumb-sep">/</span><span>My Profile</span></div>
    <h1 style="font-size:1.8rem">My Account</h1>
  </div>
</div>

<div class="container" style="padding:40px 0 80px">
  <div id="profileContent"><div style="text-align:center;padding:80px"><div class="spinner"></div></div></div>
</div>

<!-- Order Detail Modal -->
<div class="modal-overlay" id="orderDetailModal">
  <div class="modal modal-xl">
    <div class="modal-header">
      <span class="modal-title">ORDER DETAILS</span>
      <button class="modal-close" data-close-modal="orderDetailModal">✕</button>
    </div>
    <div class="modal-body" id="orderDetailBody"></div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer-common.php'; ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="/nexus-gear/js/app.js"></script>
<script>
let profileMap = null;
let profileMarker = null;
let orders = [];

function renderProfile() {
  const u = window.state.user;
  if (!u) { window.location = '/nexus-gear/index.php'; return; }

  document.getElementById('profileContent').innerHTML = `
    <div class="profile-grid">
      <!-- Sidebar -->
      <div>
        <div class="profile-card">
          <div class="profile-avatar-wrap">
            <img src="${u.avatar && u.avatar!=='default.png' ? UPLOAD+'avatars/'+u.avatar : '/nexus-gear/images/default-avatar.png'}" class="profile-avatar" id="profileAvatar" onerror="this.src='/nexus-gear/images/default-avatar.png'">
            <label for="avatarInput" class="avatar-upload-btn" title="Change photo">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            </label>
            <input type="file" id="avatarInput" accept="image/*" class="hidden" onchange="uploadAvatar(this)">
          </div>
          <h2 style="font-size:20px;margin-bottom:4px">${u.name}</h2>
          <div style="color:var(--text-dim);font-size:13px;margin-bottom:20px">${u.email}</div>
          <div style="background:var(--red-dim);border:1px solid var(--border-med);border-radius:8px;padding:12px">
            <div style="font-size:11px;color:var(--text-dim);margin-bottom:4px">MEMBER SINCE</div>
            <div style="font-size:14px;font-weight:600;color:var(--red)">${new Date().toLocaleDateString('en-PH',{month:'long',year:'numeric'})}</div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      <div>
        <!-- Profile Form -->
        <div class="content-card">
          <div class="content-card-title">PERSONAL INFORMATION</div>
          <form id="profileForm" onsubmit="saveProfile(event)">
            <div class="form-row">
              <div class="form-group"><label class="form-label">Full Name</label><input type="text" name="name" class="form-input" value="${u.name||''}" required></div>
              <div class="form-group"><label class="form-label">Phone Number</label><input type="tel" name="phone" class="form-input" value="${u.phone||''}" placeholder="09xxxxxxxxx"></div>
            </div>
            <div class="form-group"><label class="form-label">Street Address</label><input type="text" name="address_line" class="form-input" value="${u.address_line||''}" placeholder="House No., Street, Barangay"></div>
            <div class="form-row">
              <div class="form-group"><label class="form-label">City / Municipality</label><input type="text" name="city" class="form-input" value="${u.city||''}"></div>
              <div class="form-group"><label class="form-label">Province</label><input type="text" name="province" class="form-input" value="${u.province||''}"></div>
            </div>
            <div class="form-row">
              <div class="form-group"><label class="form-label">ZIP Code</label><input type="text" name="zip_code" class="form-input" value="${u.zip_code||''}"></div>
              <div></div>
            </div>
            <input type="hidden" name="lat" id="profileLat" value="${u.lat||''}">
            <input type="hidden" name="lng" id="profileLng" value="${u.lng||''}">

            <!-- Map -->
            <div class="form-group">
              <label class="form-label">Pin Your Location (Optional)</label>
              <div id="map" style="border-radius:10px;border:1px solid var(--border)"></div>
              <div class="form-hint">Click on the map to pin your delivery location</div>
            </div>

            <button type="submit" class="btn btn-primary">Save Profile</button>
          </form>
        </div>

        <!-- Change Password -->
        <div class="content-card">
          <div class="content-card-title">CHANGE PASSWORD</div>
          <form id="passwordForm" onsubmit="changePassword(event)">
            <div class="form-row">
              <div class="form-group"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-input" required></div>
              <div></div>
            </div>
            <div class="form-row">
              <div class="form-group"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-input" required minlength="6"></div>
              <div class="form-group"><label class="form-label">Confirm New Password</label><input type="password" name="confirm_new" class="form-input" required></div>
            </div>
            <button type="submit" class="btn btn-outline">Update Password</button>
          </form>
        </div>

        <!-- Orders -->
        <div class="content-card">
          <div class="content-card-title">RECENT ORDERS</div>
          <div id="ordersList"><div class="spinner" style="margin:20px auto"></div></div>
        </div>
      </div>
    </div>`;

  initMap();
  loadOrders();
}

function initMap() {
  const u = window.state.user;
  const lat = parseFloat(u.lat) || 14.5995;
  const lng = parseFloat(u.lng) || 120.9842;

  profileMap = L.map('map').setView([lat, lng], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap'}).addTo(profileMap);

  const icon = L.divIcon({
    html: `<div style="width:28px;height:28px;background:var(--red);border-radius:50%;border:3px solid white;box-shadow:0 0 10px rgba(0,212,255,0.5)"></div>`,
    iconSize:[28,28], iconAnchor:[14,14], className:''
  });

  if (u.lat && u.lng) {
    profileMarker = L.marker([lat, lng], {icon, draggable:true}).addTo(profileMap);
    profileMarker.on('dragend', e=>{
      const {lat,lng} = e.target.getLatLng();
      document.getElementById('profileLat').value = lat;
      document.getElementById('profileLng').value = lng;
    });
  }

  profileMap.on('click', e=>{
    const {lat,lng} = e.latlng;
    document.getElementById('profileLat').value = lat;
    document.getElementById('profileLng').value = lng;
    if (profileMarker) profileMap.removeLayer(profileMarker);
    profileMarker = L.marker([lat,lng],{icon,draggable:true}).addTo(profileMap);
    profileMarker.on('dragend', ev=>{
      const {lat,lng} = ev.target.getLatLng();
      document.getElementById('profileLat').value = lat;
      document.getElementById('profileLng').value = lng;
    });
  });

  // Fix Leaflet map rendering after tab show
  setTimeout(()=>profileMap.invalidateSize(),200);
}

async function loadOrders() {
  const data = await fetch(`${API}?action=get_orders`,{credentials:'include'}).then(r=>r.json());
  const el = document.getElementById('ordersList');
  if (!data.success || !data.orders.length) {
    el.innerHTML = `<div style="text-align:center;padding:32px;color:var(--text-dim)">No orders yet. <a href="/nexus-gear/shop.php" style="color:var(--red)">Start shopping!</a></div>`;
    return;
  }
  el.innerHTML = data.orders.map(o=>`
    <div class="order-row" onclick="viewOrder(${o.id})">
      <div style="flex:1">
        <div style="font-family:'Syne',sans-serif;font-size:13px;color:var(--red);margin-bottom:4px">${o.order_number}</div>
        <div style="font-size:12px;color:var(--text-dim)">${new Date(o.created_at).toLocaleDateString('en-PH',{year:'numeric',month:'short',day:'numeric'})} · ${o.item_count} item(s)</div>
      </div>
      <div style="text-align:right">
        <div style="font-family:'Syne',sans-serif;font-weight:700;color:var(--red)">${formatPrice(o.total)}</div>
        <span class="status-badge status-${o.order_status}" style="margin-top:6px;display:inline-block">${o.order_status.toUpperCase()}</span>
      </div>
    </div>`).join('');
}

async function viewOrder(id) {
  const data = await fetch(`${API}?action=get_order&id=${id}`,{credentials:'include'}).then(r=>r.json());
  if (!data.success) return;
  const o = data.order;
  document.getElementById('orderDetailBody').innerHTML = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px">
      <div style="background:var(--bg-card2);border-radius:var(--radius);padding:16px">
        <div style="font-size:11px;color:var(--text-dim);margin-bottom:8px">ORDER INFO</div>
        <div style="font-family:'Syne',sans-serif;font-size:16px;color:var(--red);margin-bottom:4px">${o.order_number}</div>
        <div style="font-size:12px;color:var(--text-dim)">${new Date(o.created_at).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'})}</div>
        <span class="status-badge status-${o.order_status}" style="margin-top:10px;display:inline-block">${o.order_status.toUpperCase()}</span>
      </div>
      <div style="background:var(--bg-card2);border-radius:var(--radius);padding:16px">
        <div style="font-size:11px;color:var(--text-dim);margin-bottom:8px">DELIVERY TO</div>
        <div style="font-size:14px;font-weight:600">${o.shipping_name}</div>
        <div style="font-size:12px;color:var(--text-secondary);margin-top:4px">${o.shipping_address}, ${o.shipping_city}, ${o.shipping_province}</div>
        <div style="font-size:12px;color:var(--text-dim)">${o.shipping_phone}</div>
      </div>
    </div>
    <div style="margin-bottom:20px">
      ${o.items.map(item=>{
        const img = item.image ? UPLOAD+'products/'+item.image : '/nexus-gear/images/no-image.png';
        return `<div style="display:flex;align-items:center;gap:14px;padding:14px;background:var(--bg-card2);border-radius:var(--radius);border:1px solid var(--border);margin-bottom:8px">
          <img src="${img}" style="width:56px;height:56px;object-fit:contain;border-radius:8px;background:var(--bg-page)" onerror="this.src='/nexus-gear/images/no-image.png'">
          <div style="flex:1"><div style="font-size:14px;font-weight:500">${item.product_name}</div><div style="font-size:12px;color:var(--text-dim)">Qty: ${item.quantity} × ${formatPrice(item.price)}</div></div>
          <div style="font-family:'Syne',sans-serif;color:var(--red)">${formatPrice(item.subtotal)}</div>
        </div>`;
      }).join('')}
    </div>
    <div style="background:var(--bg-card2);border-radius:var(--radius);padding:16px;border:1px solid var(--border)">
      <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;color:var(--text-secondary)"><span>Subtotal</span><span>${formatPrice(o.subtotal)}</span></div>
      ${o.discount_amount>0?`<div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;color:var(--green)"><span>Discount</span><span>-${formatPrice(o.discount_amount)}</span></div>`:''}
      <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;color:var(--text-secondary)"><span>Payment</span><span>${o.payment_method.replace('_',' ').toUpperCase()}</span></div>
      <div style="display:flex;justify-content:space-between;padding:14px 0 0;border-top:1px solid var(--border);margin-top:8px"><span style="font-weight:700;font-size:15px">Total</span><span style="font-family:'Syne',sans-serif;font-size:20px;color:var(--red)">${formatPrice(o.total)}</span></div>
    </div>
    ${o.notes?`<div style="margin-top:16px;padding:14px;background:var(--bg-card2);border-radius:var(--radius);border:1px solid var(--border)"><div style="font-size:11px;color:var(--text-dim);margin-bottom:6px">ORDER NOTES</div><div style="font-size:13px;color:var(--text-secondary)">${o.notes}</div></div>`:''}
  `;
  openModal('orderDetailModal');
}

async function saveProfile(e) {
  e.preventDefault();
  const btn = e.target.querySelector('button[type=submit]');
  btn.disabled=true; btn.textContent='Saving...';
  const fd = new FormData(e.target);
  const data = await apiCall('update_profile', fd, 'POST');
  btn.disabled=false; btn.textContent='Save Profile';
  if(data.success) { toast('Profile updated!','success'); await checkAuth(); }
  else toast(data.message,'error');
}

async function changePassword(e) {
  e.preventDefault();
  const f = e.target;
  if(f.new_password.value !== f.confirm_new.value) { toast('Passwords do not match.','error'); return; }
  const btn = f.querySelector('button[type=submit]');
  btn.disabled=true; btn.textContent='Updating...';
  const data = await apiCall('change_password',{current_password:f.current_password.value,new_password:f.new_password.value},'POST');
  btn.disabled=false; btn.textContent='Update Password';
  if(data.success) { toast('Password changed!','success'); f.reset(); }
  else toast(data.message,'error');
}

async function uploadAvatar(input) {
  const file = input.files[0];
  if(!file) return;
  const fd = new FormData();
  fd.append('avatar', file);
  const data = await apiCall('upload_avatar', fd, 'POST');
  if(data.success) {
    document.getElementById('profileAvatar').src = data.avatar_url;
    toast('Profile photo updated!','success');
  } else toast(data.message,'error');
}

document.addEventListener('DOMContentLoaded', async ()=>{
  await checkAuth();
  if (!window.state.user) { openModal('authModal'); return; }
  renderProfile();
});
</script>
</body></html>
