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

// ── Cart icon count
function updateCartIcon() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const el = document.getElementById("cart-count");
  if (el) el.innerText = cart.length;
}
document.addEventListener("DOMContentLoaded", updateCartIcon);

// ══════════════════════════════════════════
//  UPDATED ADD TO CART FUNCTIONS
// ══════════════════════════════════════════

function addToCart(id, name, price, image) {
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

  // Show success feedback
  showAddToCartToast(name);
}

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

  // Redirect to cart (used in Quick View)
  window.location.href = "../Add to Cart and CheckOut/Cart.php";
}

// Toast notification when product is added from "Buy" button
function showAddToCartToast(productName) {
  // Remove existing toast if any
  let existing = document.getElementById("cart-toast");
  if (existing) existing.remove();

  const toast = document.createElement("div");
  toast.id = "cart-toast";
  toast.style.cssText = `
    position: fixed;
    bottom: 80px;
    right: 20px;
    background: #1a1a1a;
    color: white;
    padding: 14px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 10000;
    font-family: var(--font);
    font-size: 14px;
    animation: slideIn 0.3s ease;
  `;

  toast.innerHTML = `
    <i class="bi bi-check-circle-fill" style="color:#22c55e;font-size:1.3rem;"></i>
    <div>
      <strong>${productName}</strong><br>
      <small>Added to cart</small>
    </div>
  `;

  document.body.appendChild(toast);

  // Auto hide after 3 seconds
  setTimeout(() => {
    toast.style.transition = "all 0.3s ease";
    toast.style.opacity = "0";
    toast.style.transform = "translateY(20px)";
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Close toast on click
document.addEventListener("click", (e) => {
  if (e.target.closest("#cart-toast")) {
    const toast = document.getElementById("cart-toast");
    if (toast) toast.remove();
  }
});