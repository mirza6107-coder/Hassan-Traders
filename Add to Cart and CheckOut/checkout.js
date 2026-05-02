/* ══════════════════════════════════════════════
   HASSAN TRADERS — CHECKOUT PAGE SCRIPTS
   checkout.js
══════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {
  renderOrderSummary();
});

/* ──────────────────────────────────────────
   RENDER ORDER SUMMARY from localStorage
────────────────────────────────────────── */
function renderOrderSummary() {
  const container  = document.getElementById('summary-items');
  const subtotalEl = document.getElementById('sum-subtotal');
  const totalEl    = document.getElementById('sum-total');

  const cart = JSON.parse(localStorage.getItem('cart')) || [];

  if (cart.length === 0) {
    if (container) container.innerHTML = '<p class="co-empty-msg">Your cart is empty.</p>';
    return;
  }

  let html = '';
  let grandTotal = 0;

  cart.forEach(function (item) {
    const itemTotal = item.price * item.quantity;
    grandTotal += itemTotal;

    html += `
      <div class="co-summary-item">
        <div class="co-item-info">
          <span class="co-item-name">${item.name}</span>
          <span class="co-item-qty">Qty: ${item.quantity} &times; Rs. ${parseFloat(item.price).toLocaleString()}</span>
        </div>
        <span class="co-item-total">Rs. ${itemTotal.toLocaleString()}</span>
      </div>`;
  });

  if (container)  container.innerHTML  = html;
  if (subtotalEl) subtotalEl.textContent = grandTotal.toLocaleString();
  if (totalEl)    totalEl.textContent    = grandTotal.toLocaleString();
}

/* ──────────────────────────────────────────
   PAYMENT METHOD SELECTION
────────────────────────────────────────── */
// Track selected payment method globally
let selectedPaymentMethod = 'cod';

function selectPayment(card, method) {
  selectedPaymentMethod = method;

  // Remove active from all
  document.querySelectorAll('.co-pay-card').forEach(function (c) {
    c.classList.remove('active');
  });

  // Activate clicked card
  card.classList.add('active');

  // Hide all detail panels
  ['easypaisa', 'jazzcash', 'bank'].forEach(function (m) {
    const el = document.getElementById('pay-detail-' + m);
    if (el) {
      el.style.display = 'none';
      el.style.animation = '';
    }
  });

  // Show relevant panel (not for COD)
  if (method !== 'cod') {
    const panel = document.getElementById('pay-detail-' + method);
    if (panel) {
      panel.style.display = 'block';
      panel.style.animation = 'fade-up 0.3s ease';
    }
  }
}

/* ──────────────────────────────────────────
   PLACE ORDER — Validate, Send to Server & Show Success
────────────────────────────────────────── */
async function handlePlaceOrder() {
  const nameEl    = document.getElementById('userName');
  const phoneEl   = document.getElementById('userPhone');
  const addressEl = document.getElementById('userAddress');
  const emailEl   = document.getElementById('userEmail');
  const cityEl    = document.getElementById('userCity');

  const name    = (nameEl    || {}).value || '';
  const phone   = (phoneEl   || {}).value || '';
  const address = (addressEl || {}).value || '';
  const email   = (emailEl   || {}).value || '';
  const city    = (cityEl    || {}).value || 'Sargodha';

  // ── Validate required fields ──
  if (!name.trim() || !phone.trim() || !address.trim()) {
    ['userName', 'userPhone', 'userAddress'].forEach(function (id) {
      const el = document.getElementById(id);
      if (el && !el.value.trim()) {
        el.style.borderColor = '#dc3545';
        el.style.boxShadow   = '0 0 0 4px rgba(220,53,69,0.12)';
        el.addEventListener('input', function () {
          el.style.borderColor = '';
          el.style.boxShadow   = '';
        }, { once: true });
      }
    });

    const firstEmpty = document.querySelector('.co-input[required]:placeholder-shown') ||
                       document.getElementById('userName');
    if (firstEmpty) firstEmpty.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  // ── Get cart ──
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  if (cart.length === 0) {
    alert('Your cart is empty!');
    return;
  }

  const total = cart.reduce(function (sum, item) {
    return sum + item.price * item.quantity;
  }, 0);

  // ── Build payload ──
  const payload = {
    name:          name.trim(),
    phone:         phone.trim(),
    email:         email.trim(),
    address:       address.trim(),
    city:          city,
    paymentMethod: selectedPaymentMethod,
    total:         total,
    cart:          cart
  };

  // ── Disable button to prevent double-submit ──
  const placeBtn = document.querySelector('.co-place-btn');
  if (placeBtn) {
    placeBtn.disabled = true;
    placeBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Placing Order…';
  }

  try {
    const response = await fetch('place_order.php', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(payload)
    });

    const result = await response.json();

    if (result.success) {
      // ── Clear localStorage cart ──
      localStorage.removeItem('cart');

      // ── Clear DB cart too (place_order.php also does this server-side,
      //    but this is a belt-and-suspenders client-side call) ──
      if (typeof SAVE_CART_URL !== 'undefined') {
        fetch(SAVE_CART_URL, {
          method: 'POST', credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: 'clear' })
        }).catch(() => {});
      }

      // ── Update badge ──
      if (typeof updateCartIcon === 'function') updateCartIcon();

      // ── Show success overlay ──
      const overlay = document.getElementById('successOverlay');
      if (overlay) {
        // Inject the order ID into the success message
        const orderIdEl = document.getElementById('successOrderId');
        if (orderIdEl) orderIdEl.textContent = '#' + result.order_id;

        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }
    } else {
      alert('Order failed: ' + (result.message || 'Unknown error. Please try again.'));
      if (placeBtn) {
        placeBtn.disabled = false;
        placeBtn.innerHTML = '<i class="bi bi-lock-fill me-2"></i> Place Order Now';
      }
    }
  } catch (err) {
    console.error('Order error:', err);
    alert('Network error. Please check your connection and try again.');
    if (placeBtn) {
      placeBtn.disabled = false;
      placeBtn.innerHTML = '<i class="bi bi-lock-fill me-2"></i> Place Order Now';
    }
  }
}