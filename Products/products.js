// ── Navbar scroll shadow
window.addEventListener("scroll", () => {
  document
    .getElementById("mainNav")
    .classList.toggle("scrolled", window.scrollY > 20);
});

// ── Tab switching
const pills = document.querySelectorAll(".cat-pill");
const panes = document.querySelectorAll(".tab-pane");

pills.forEach((pill) => {
  pill.addEventListener("click", () => {
    pills.forEach((p) => p.classList.remove("active"));
    panes.forEach((p) => p.classList.remove("show"));
    pill.classList.add("active");
    const target = document.getElementById(pill.dataset.target);
    if (target) target.classList.add("show");
    document.getElementById("productSearch").value = "";
    document
      .querySelectorAll(".product-card-wrap")
      .forEach((c) => (c.style.display = ""));
    document.getElementById("noResults").style.display = "none";
    updateCount();
  });
});

// ── Search / filter
function filterCards() {
  const q = document.getElementById("productSearch").value.toLowerCase().trim();
  const activePane = document.querySelector(".tab-pane.show");
  if (!activePane) return;
  const cards = activePane.querySelectorAll(".product-card-wrap");
  let visible = 0;
  cards.forEach((card) => {
    const match = card.innerText.toLowerCase().includes(q);
    card.style.display = match ? "" : "none";
    if (match) visible++;
  });
  document.getElementById("noResults").style.display =
    visible === 0 && q ? "block" : "none";
  document.getElementById("resultCount").textContent = q
    ? `${visible} result${visible !== 1 ? "s" : ""} found`
    : "";
}

function updateCount() {
  document.getElementById("resultCount").textContent = "";
}

document.getElementById("productSearch").addEventListener("keyup", filterCards);
document
  .getElementById("productSearch")
  .addEventListener("search", filterCards);

// ── Cart icon count
function updateCartIcon() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const el = document.getElementById("cart-count");
  if (el) el.innerText = cart.length;
}
document.addEventListener("DOMContentLoaded", updateCartIcon);

// ── Add to cart & redirect
function addToCartAndGo(id, name, price, image) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const product = { id, name, price: parseFloat(price), image, quantity: 1 };
  const index = cart.findIndex((item) => item.id === id);
  if (index > -1) {
    cart[index].quantity += 1;
  } else {
    cart.push(product);
  }
  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartIcon();
  window.location.href = "../Add to Cart and CheckOut/Cart.php";
}

// ══════════════════════════════════════════
//  QUICK VIEW MODAL
// ══════════════════════════════════════════

const qvOverlay = document.getElementById("qvOverlay");
const qvContent = document.getElementById("qvContent");

// Open modal and load product
function openQuickView(id, name, price, image) {
  qvOverlay.classList.add("open");
  document.body.style.overflow = "hidden";

  // Show skeleton loader
  qvContent.innerHTML = `
    <div class="qv-skeleton">
      <div class="qv-skel-box" style="height:300px;border-radius:16px;"></div>
    </div>`;

  // Fetch product details
  fetch(`product-quickview.php?id=${id}`)
    .then((r) => r.json())
    .then((p) => {
      if (!p.success) {
        qvContent.innerHTML = renderQvError();
        return;
      }
      qvContent.innerHTML = renderQvContent(p);
    })
    .catch(() => {
      qvContent.innerHTML = renderQvError();
    });
}

function closeQuickView() {
  qvOverlay.classList.remove("open");
  document.body.style.overflow = "";
}

// Close on overlay background click
qvOverlay.addEventListener("click", (e) => {
  if (e.target === qvOverlay) closeQuickView();
});

// Close on Escape
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") closeQuickView();
});

function renderQvContent(p) {
  const imgSrc = `../Admin-Panel/uploads/${p.image}`;
  const fallback = `../Images/no-image.png`;
  const hasOrig = p.orignalprice > 0 && p.orignalprice !== p.price;

  let stockHtml;
  if (p.stock === 0) {
    stockHtml = `<span class="qv-stock-badge qv-stock-out"><i class="bi bi-x-circle-fill"></i> Out of Stock</span>`;
  } else if (p.stock < 20) {
    stockHtml = `<span class="qv-stock-badge qv-stock-low"><i class="bi bi-exclamation-circle-fill"></i> Low Stock (${p.stock} left)</span>`;
  } else {
    stockHtml = `<span class="qv-stock-badge qv-stock-ok"><i class="bi bi-check-circle-fill"></i> In Stock</span>`;
  }

  const descHtml = p.description
    ? `<p style="font-size:13.5px;color:#6B6B6B;line-height:1.7;margin-bottom:20px;">${p.description}</p>`
    : "";

  const jsName = p.name.replace(/'/g, "\\'");
  const cartBtn =
    p.stock > 0
      ? `<button class="qv-btn-primary" onclick="addToCartAndGo(${p.id}, '${jsName}', ${p.price}, '${p.image}')">
         <i class="bi bi-cart-plus-fill"></i> Add to Cart
       </button>`
      : `<button class="qv-btn-primary" disabled style="opacity:0.5;cursor:not-allowed;">
         <i class="bi bi-x-circle"></i> Out of Stock
       </button>`;

  return `
    <div class="qv-inner">
      <div class="qv-img-side">
        <img src="${imgSrc}" alt="${p.name}" onerror="this.src='${fallback}'">
        ${p.category ? `<span class="qv-img-badge">${p.category}</span>` : ""}
      </div>
      <div class="qv-info-side">
        ${p.brand ? `<div class="qv-brand">${p.brand}</div>` : ""}
        <div class="qv-name">${p.name}</div>

        <div class="qv-price-row">
          <div class="qv-price">Rs. ${Number(p.price).toLocaleString("en-PK")}</div>
          
        </div>

        ${descHtml}

        <hr class="qv-divider">

        <div class="qv-meta">
          ${
            p.category
              ? `
          <div class="qv-meta-row">
            <span class="qv-meta-label">Category</span>
            <span class="qv-meta-value">${p.category}</span>
          </div>`
              : ""
          }
          ${
            p.brand
              ? `
          <div class="qv-meta-row">
            <span class="qv-meta-label">Brand</span>
            <span class="qv-meta-value">${p.brand}</span>
          </div>`
              : ""
          }
          <div class="qv-meta-row">
            <span class="qv-meta-label">Stock</span>
            ${stockHtml}
          </div>
        </div>

        <hr class="qv-divider">

        <div class="qv-actions">
          ${cartBtn}
          
        </div>
      </div>
    </div>`;
}

function renderQvError() {
  return `
    <div style="padding:60px;text-align:center;color:#6B6B6B;">
      <i class="bi bi-exclamation-circle" style="font-size:2.5rem;display:block;margin-bottom:14px;opacity:0.4;"></i>
      <p>Could not load product details.</p>
    </div>`;
}
