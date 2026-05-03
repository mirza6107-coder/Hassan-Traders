/* ══════════════════════════════════════════════
   HASSAN TRADERS — CART PAGE SCRIPTS
   cart.js  (with DB sync)
══════════════════════════════════════════════ */

// SAVE_CART_URL is defined in navbar.js which loads first
// Fallback in case this page loads without navbar.js
// if (typeof SAVE_CART_URL === 'undefined') {
//   var SAVE_CART_URL = '/Add to Cart and CheckOut/save-cart.php';
// }

document.addEventListener('DOMContentLoaded', function () {
  renderCart();
  // navbar.js handles updateCartIcon on load
  if (typeof updateCartIcon === 'function') updateCartIcon();
});

/* ──────────────────────────────────────────
   RENDER CART — Desktop table + Mobile cards
────────────────────────────────────────── */
function renderCart() {
  const desktopBody = document.getElementById('cart-items-container');
  const mobileList  = document.getElementById('cart-items-mobile');
  const subtotalEl  = document.getElementById('subtotal');
  const totalEl     = document.getElementById('grand-total');

  const cart = JSON.parse(localStorage.getItem('cart')) || [];

  if (cart.length === 0) {
    const emptyHTML = `
      <div class="ct-empty">
        <i class="bi bi-cart-x"></i>
        <p>Your cart is empty — add some products first.</p>
        <a href="../Products/Products.php"><i class="bi bi-arrow-left me-1"></i> Go Shopping</a>
      </div>`;
    if (desktopBody) desktopBody.innerHTML = `<tr><td colspan="5">${emptyHTML}</td></tr>`;
    if (mobileList)  mobileList.innerHTML  = emptyHTML;
    if (subtotalEl)  subtotalEl.textContent = '0';
    if (totalEl)     totalEl.textContent    = '0';
    return;
  }

  let desktopHTML = '';
  let mobileHTML  = '';
  let total = 0;

  cart.forEach(function (item, index) {
    const lineTotal = item.price * item.quantity;
    total += lineTotal;

    const imgSrc  = `../Admin-Panel/uploads/${item.image}`;
    const fallback = `onerror="this.src='../Images/no-image.png'"`;

    desktopHTML += `
      <tr id="row-${index}">
        <td>
          <div class="ct-product-cell">
            <div class="ct-product-thumb">
              <img src="${imgSrc}" alt="${item.name}" ${fallback} />
            </div>
            <span class="ct-product-name">${item.name}</span>
          </div>
        </td>
        <td><span class="ct-price">Rs. ${parseFloat(item.price).toLocaleString()}</span></td>
        <td>
          <div class="ct-qty">
            <button class="ct-qty-btn" onclick="changeQty(${index}, -1)">−</button>
            <div class="ct-qty-val">${item.quantity}</div>
            <button class="ct-qty-btn" onclick="changeQty(${index}, 1)">+</button>
          </div>
        </td>
        <td><span class="ct-line-total">Rs. ${lineTotal.toLocaleString()}</span></td>
        <td>
          <button class="ct-remove-btn" onclick="removeItem(${index})" title="Remove">
            <i class="bi bi-x-lg"></i>
          </button>
        </td>
      </tr>`;

    mobileHTML += `
      <div class="ct-mobile-card" id="mob-${index}">
        <div class="ct-mobile-thumb">
          <img src="${imgSrc}" alt="${item.name}" ${fallback} />
        </div>
        <div class="ct-mobile-body">
          <div class="ct-mobile-name">${item.name}</div>
          <div class="ct-mobile-price">Rs. ${parseFloat(item.price).toLocaleString()} each</div>
          <div class="ct-mobile-footer">
            <div class="ct-qty" style="transform:scale(0.92);transform-origin:left">
              <button class="ct-qty-btn" onclick="changeQty(${index}, -1)">−</button>
              <div class="ct-qty-val">${item.quantity}</div>
              <button class="ct-qty-btn" onclick="changeQty(${index}, 1)">+</button>
            </div>
            <span class="ct-mobile-total">Rs. ${lineTotal.toLocaleString()}</span>
          </div>
        </div>
        <button class="ct-mobile-remove" onclick="removeItem(${index})" title="Remove">
          <i class="bi bi-x"></i>
        </button>
      </div>`;
  });

  if (desktopBody) desktopBody.innerHTML = desktopHTML;
  if (mobileList)  mobileList.innerHTML  = mobileHTML;
  if (subtotalEl)  subtotalEl.textContent = total.toLocaleString();
  if (totalEl)     totalEl.textContent    = total.toLocaleString();
}

/* ──────────────────────────────────────────
   HELPER: send action to save-cart.php
────────────────────────────────────────── */
function _dbCart(payload) {
  fetch(SAVE_CART_URL, {
    method:      'POST',
    credentials: 'same-origin',
    headers:     { 'Content-Type': 'application/json' },
    body:        JSON.stringify(payload),
  })
  .then(r => r.json())
  .then(res => console.log('[Cart DB]', res))
  .catch(err => console.warn('[Cart DB] failed:', err));
}

/* ──────────────────────────────────────────
   REMOVE ITEM — updates localStorage + DB
────────────────────────────────────────── */
function removeItem(index) {
  let cart = JSON.parse(localStorage.getItem('cart')) || [];

  // Get the product id BEFORE removing so we can tell the DB
  const removedItem = cart[index];
  const productId   = removedItem ? removedItem.id : null;

  // Animate out
  const row = document.getElementById('row-' + index);
  const mob = document.getElementById('mob-' + index);
  if (row) row.classList.add('removing');
  if (mob) {
    mob.style.opacity    = '0';
    mob.style.transform  = 'translateX(16px)';
    mob.style.transition = 'opacity 0.3s, transform 0.3s';
  }

  setTimeout(function () {
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));

    // ── Sync to DB ──
    if (productId) {
      _dbCart({ action: 'remove', id: productId });
    }

    renderCart();
    if (typeof updateCartIcon === 'function') updateCartIcon();
  }, 300);
}

/* ──────────────────────────────────────────
   CHANGE QUANTITY — updates localStorage + DB
────────────────────────────────────────── */
function changeQty(index, delta) {
  let cart = JSON.parse(localStorage.getItem('cart')) || [];

  cart[index].quantity += delta;

  const productId  = cart[index].id;
  const newQty     = cart[index].quantity;

  if (newQty <= 0) {
    // Remove entirely
    const removedId = cart[index].id;
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    _dbCart({ action: 'remove', id: removedId });
  } else {
    localStorage.setItem('cart', JSON.stringify(cart));
    // ── Sync new quantity to DB ──
    _dbCart({ action: 'update', id: productId, quantity: newQty });
  }

  renderCart();
  if (typeof updateCartIcon === 'function') updateCartIcon();
}