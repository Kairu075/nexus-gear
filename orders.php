<?php require_once __DIR__ . '/includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>My Orders — Nexus Gear</title>
<link rel="icon" href="/nexus-gear/images/logo.png">
<link rel="stylesheet" href="/nexus-gear/css/main.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>
<div style="height:64px"></div>

<div style="background:linear-gradient(180deg,rgba(0,212,255,0.05) 0%,transparent 100%);border-bottom:1px solid var(--border);padding:32px 0">
  <div class="container">
    <div class="breadcrumb" style="margin-bottom:10px"><a href="/nexus-gear/index.php">Home</a><span class="breadcrumb-sep">/</span><span>My Orders</span></div>
    <h1 style="font-size:1.8rem">My Orders</h1>
  </div>
</div>

<div class="container" style="padding:40px 0 80px">
  <div id="ordersContent"><div style="text-align:center;padding:80px"><div class="spinner"></div></div></div>
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
<script src="/nexus-gear/js/app.js"></script>
<script>
async function loadOrders() {
  if (!window.state.user) {
    document.getElementById('ordersContent').innerHTML = `<div style="text-align:center;padding:80px"><h2 style="margin-bottom:16px">Please sign in to view your orders</h2><button class="btn btn-primary btn-lg" onclick="openModal('authModal')">Sign In</button></div>`;
    return;
  }
  const data = await fetch(`${API}?action=get_orders`,{credentials:'include'}).then(r=>r.json());
  if (!data.success || !data.orders.length) {
    document.getElementById('ordersContent').innerHTML = `<div style="text-align:center;padding:80px"><div style="font-size:64px;margin-bottom:20px;opacity:0.2">📦</div><h2 style="margin-bottom:12px">No orders yet</h2><p style="color:var(--text-dim);margin-bottom:28px">Your order history will appear here once you make a purchase.</p><a href="/nexus-gear/shop.php" class="btn btn-primary btn-lg">Start Shopping</a></div>`;
    return;
  }

  const statusColors = {pending:'var(--amber)',processing:'var(--red)',approved:'var(--green)',shipped:'#a78bfa',delivered:'var(--green)',cancelled:'var(--red)'};

  document.getElementById('ordersContent').innerHTML = `
    <div style="display:flex;flex-direction:column;gap:16px">
      ${data.orders.map(o=>`
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;cursor:pointer;transition:var(--transition)" onclick="viewOrder(${o.id})" onmouseover="this.style.borderColor='var(--border-med)'" onmouseout="this.style.borderColor='var(--border)'">
          <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
              <div style="font-family:'Syne',sans-serif;color:var(--red);font-size:14px;margin-bottom:4px">${o.order_number}</div>
              <div style="font-size:12px;color:var(--text-dim)">${new Date(o.created_at).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'})} · ${o.item_count} item(s) · ${o.payment_method.replace('_',' ').toUpperCase()}</div>
            </div>
            <div style="display:flex;align-items:center;gap:16px">
              <div style="text-align:right">
                <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:var(--red)">${formatPrice(o.total)}</div>
              </div>
              <span class="status-badge status-${o.order_status}">${o.order_status.toUpperCase()}</span>
            </div>
          </div>
          <!-- Progress steps -->
          <div style="margin-top:20px;display:flex;align-items:center;gap:0">
            ${['pending','processing','approved','shipped','delivered'].map((s,i,arr)=>{
              const steps = ['pending','processing','approved','shipped','delivered'];
              const currentIdx = steps.indexOf(o.order_status);
              const stepIdx = steps.indexOf(s);
              const isActive = stepIdx <= currentIdx && o.order_status !== 'cancelled';
              const isCancelled = o.order_status === 'cancelled';
              return `
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:6px">
                  <div style="width:28px;height:28px;border-radius:50%;background:${isCancelled?'rgba(255,51,85,0.1)':isActive?'var(--red)':'var(--bg-card2)'};border:2px solid ${isCancelled?'var(--red)':isActive?'var(--red)':'var(--border)'};display:flex;align-items:center;justify-content:center;font-size:12px;color:${isActive&&!isCancelled?'#000':'var(--text-dim)'};font-weight:700;transition:all 0.3s">
                    ${isCancelled?'✕':isActive?'✓':'·'}
                  </div>
                  <div style="font-size:9px;color:${isActive&&!isCancelled?'var(--red)':'var(--text-dim)'};letter-spacing:0.5px;text-transform:uppercase;text-align:center">${s}</div>
                </div>
                ${i<arr.length-1?`<div style="flex:1;height:2px;background:${stepIdx<currentIdx&&!isCancelled?'var(--red)':'var(--border)'};margin-bottom:22px;transition:background 0.3s"></div>`:''}`;
            }).join('')}
          </div>
        </div>`).join('')}
    </div>`;
}

async function viewOrder(id) {
  const data = await fetch(`${API}?action=get_order&id=${id}`,{credentials:'include'}).then(r=>r.json());
  if (!data.success) return;
  const o = data.order;
  document.getElementById('orderDetailBody').innerHTML = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
      <div style="background:var(--bg-card2);border-radius:var(--radius);padding:16px;border:1px solid var(--border)">
        <div style="font-size:10px;color:var(--text-dim);margin-bottom:6px">ORDER INFO</div>
        <div style="font-family:'Syne',sans-serif;font-size:15px;color:var(--red);margin-bottom:4px">${o.order_number}</div>
        <div style="font-size:12px;color:var(--text-dim)">${new Date(o.created_at).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'})}</div>
        <span class="status-badge status-${o.order_status}" style="margin-top:8px;display:inline-block">${o.order_status.toUpperCase()}</span>
      </div>
      <div style="background:var(--bg-card2);border-radius:var(--radius);padding:16px;border:1px solid var(--border)">
        <div style="font-size:10px;color:var(--text-dim);margin-bottom:6px">DELIVERY ADDRESS</div>
        <div style="font-weight:600;margin-bottom:4px">${o.shipping_name}</div>
        <div style="font-size:12px;color:var(--text-secondary)">${o.shipping_address}</div>
        <div style="font-size:12px;color:var(--text-secondary)">${o.shipping_city}, ${o.shipping_province} ${o.shipping_zip||''}</div>
        <div style="font-size:12px;color:var(--text-dim);margin-top:4px">${o.shipping_phone}</div>
      </div>
    </div>
    <div style="margin-bottom:16px">
      ${o.items.map(item=>{
        const img = item.image ? UPLOAD+'products/'+item.image : '/nexus-gear/images/no-image.png';
        return `<div style="display:flex;align-items:center;gap:14px;padding:14px;background:var(--bg-card2);border-radius:var(--radius);border:1px solid var(--border);margin-bottom:8px">
          <img src="${img}" style="width:64px;height:64px;border-radius:10px;object-fit:contain;background:var(--bg-page);padding:4px" onerror="this.src='/nexus-gear/images/no-image.png'">
          <div style="flex:1">
            <a href="/nexus-gear/product.php?slug=${item.product_id}" style="font-size:14px;font-weight:500;color:var(--text-primary);text-decoration:none">${item.product_name}</a>
            <div style="font-size:12px;color:var(--text-dim);margin-top:4px">${formatPrice(item.price)} × ${item.quantity}</div>
          </div>
          <span style="font-family:'Syne',sans-serif;color:var(--red);font-size:14px">${formatPrice(item.subtotal)}</span>
        </div>`;
      }).join('')}
    </div>
    <div style="background:var(--bg-card2);border-radius:var(--radius);padding:16px;border:1px solid var(--border)">
      <div style="display:flex;justify-content:space-between;padding:5px 0;font-size:13px;color:var(--text-secondary)"><span>Subtotal</span><span>${formatPrice(o.subtotal)}</span></div>
      <div style="display:flex;justify-content:space-between;padding:5px 0;font-size:13px;color:var(--text-secondary)"><span>Shipping</span><span style="color:var(--green)">FREE</span></div>
      ${o.discount_amount>0?`<div style="display:flex;justify-content:space-between;padding:5px 0;font-size:13px;color:var(--green)"><span>Discount</span><span>-${formatPrice(o.discount_amount)}</span></div>`:''}
      <div style="display:flex;justify-content:space-between;padding:12px 0 0;border-top:1px solid var(--border);margin-top:6px"><span style="font-weight:700;font-size:15px">Total</span><span style="font-family:'Syne',sans-serif;font-size:22px;color:var(--red)">${formatPrice(o.total)}</span></div>
    </div>
    ${o.notes?`<div style="margin-top:12px;padding:14px;background:var(--red-dim);border-radius:var(--radius);border:1px solid var(--border-med)"><div style="font-size:10px;color:var(--red);margin-bottom:6px">ORDER NOTES</div><div style="font-size:13px;color:var(--text-secondary)">${o.notes}</div></div>`:''}
  `;
  openModal('orderDetailModal');
}

document.addEventListener('DOMContentLoaded', async ()=>{
  await checkAuth();
  loadOrders();
});
</script>
</body></html>
