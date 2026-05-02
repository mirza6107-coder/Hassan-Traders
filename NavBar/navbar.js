// ══════════════════════════════════════════════════════════════
//  CART UTILITIES  —  available on every page
// ══════════════════════════════════════════════════════════════

// Absolute paths work from ANY folder (Home/, Products/, NavBar/, etc.)
const SAVE_CART_URL = '/Add to Cart and CheckOut/save-cart.php';
const CART_PAGE_URL = '/Add to Cart and CheckOut/Cart.php';

// ── Read cart from localStorage safely ───────────────────────
function getCart() {
  try { return JSON.parse(localStorage.getItem('cart')) || []; }
  catch { return []; }
}

// ── Update the badge count in the navbar ─────────────────────
function updateCartIcon() {
  const cart  = getCart();
  const total = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
  const el    = document.getElementById('cart-count');
  if (el) {
    el.innerText     = total;
    el.style.display = total > 0 ? '' : 'none';
  }
}

// ── Send any action to save-cart.php ─────────────────────────
function _cartRequest(payload) {
  return fetch(SAVE_CART_URL, {
    method:      'POST',
    credentials: 'same-origin',
    headers:     { 'Content-Type': 'application/json' },
    body:        JSON.stringify(payload),
  })
  .then(r => r.json())
  .then(res => { console.log('[Cart]', res); return res; })
  .catch(err => console.warn('[Cart] request failed:', err));
}

// ── Add one product and redirect to cart ─────────────────────
function addToCartAndGo(id, name, price, image) {
  let cart    = getCart();
  const index = cart.findIndex(item => item.id === id);
  if (index > -1) {
    cart[index].quantity += 1;
  } else {
    cart.push({ id, name, price: parseFloat(price), image, quantity: 1 });
  }
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartIcon();

  // Save to DB then redirect
  _cartRequest({ action: 'add', id, name, price: parseFloat(price), image, quantity: 1 })
    .finally(() => { window.location.href = CART_PAGE_URL; });
}

// ── Add one product WITHOUT redirecting ───────────────────────
function addToCart(id, name, price, image) {
  let cart    = getCart();
  const index = cart.findIndex(item => item.id === id);
  if (index > -1) {
    cart[index].quantity += 1;
  } else {
    cart.push({ id, name, price: parseFloat(price), image, quantity: 1 });
  }
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartIcon();
  _cartRequest({ action: 'add', id, name, price: parseFloat(price), image, quantity: 1 });
}

// ── Sync full cart to DB (call after quantity change or remove)
function syncCartToDB() {
  const items = getCart();
  _cartRequest({ action: 'sync', items });
}

// ── Remove one item from localStorage + DB ───────────────────
function removeFromCart(id) {
  let cart = getCart().filter(item => item.id !== id);
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartIcon();
  _cartRequest({ action: 'remove', id });
}

// Keep badge in sync across multiple open tabs
window.addEventListener('storage', function (e) {
  if (e.key === 'cart') updateCartIcon();
});


// ══════════════════════════════════════════════════════════════
//  NAVBAR BEHAVIOUR
// ══════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {

  // ── 1. Refresh badge on every page load ──────────────────────
  updateCartIcon();

  // ── 2. Scroll shadow ─────────────────────────────────────────
  const navbar = document.querySelector('.premium-navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 80);
    });
  }

  // ── 3. Force-initialize Bootstrap dropdowns ──────────────────
  if (typeof bootstrap !== 'undefined') {
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (el) {
      new bootstrap.Dropdown(el, { popperConfig: { strategy: 'fixed' } });
    });
  }

  // ── 4. Close dropdown on item click (mobile fix) ─────────────
  document.querySelectorAll('.navbar .dropdown-menu .dropdown-item').forEach(function (item) {
    item.addEventListener('click', function () {
      const toggle = item.closest('.dropdown').querySelector('[data-bs-toggle="dropdown"]');
      if (toggle && typeof bootstrap !== 'undefined') {
        const instance = bootstrap.Dropdown.getInstance(toggle);
        if (instance) instance.hide();
      }
    });
  });

});