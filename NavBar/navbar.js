document.addEventListener("DOMContentLoaded", function () {
  // ── 1. Scroll effect ─────────────────────────────────────────
  const navbar = document.querySelector(".premium-navbar");
  if (navbar) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > 80) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    });
  }

  // ── 2. Force-initialize all Bootstrap dropdowns ──────────────
  if (typeof bootstrap !== "undefined") {
    document
      .querySelectorAll('[data-bs-toggle="dropdown"]')
      .forEach(function (el) {
        new bootstrap.Dropdown(el, {
          popperConfig: {
            strategy: "fixed", // prevents clipping without breaking alignment
          },
        });
      });
  }

  // ── 3. Close dropdown when clicking a menu item (mobile fix) ─
  document
    .querySelectorAll(".navbar .dropdown-menu .dropdown-item")
    .forEach(function (item) {
      item.addEventListener("click", function () {
        const toggle = item
          .closest(".dropdown")
          .querySelector('[data-bs-toggle="dropdown"]');
        if (toggle && typeof bootstrap !== "undefined") {
          const instance = bootstrap.Dropdown.getInstance(toggle);
          if (instance) instance.hide();
        }
      });
    });
});
// ══════════════════════════════════════════════════════════════
//  CART UTILITIES  —  available on every page
// ══════════════════════════════════════════════════════════════

// save-cart.php lives in 'Add to Cart and CheckOut/' folder
// This path is relative to the PAGE calling it, so we use an
// absolute path from the site root to avoid any folder mismatch.
const SAVE_CART_URL = "../Add to Cart and CheckOut/save-cart.php";
const CART_PAGE_URL = "../Add to Cart and CheckOut/Cart.php";

function getCart() {
  try {
    return JSON.parse(localStorage.getItem("cart")) || [];
  } catch {
    return [];
  }
}

function updateCartIcon() {
  const cart = getCart();
  const total = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
  const el = document.getElementById("cart-count");
  if (el) {
    el.innerText = total;
    el.style.display = total > 0 ? "" : "none";
  }
}

function _syncCartToDB(payload) {
  console.log("[Cart] Saving to DB →", SAVE_CART_URL, payload);
  return fetch(SAVE_CART_URL, {
    method: "POST",
    credentials: "same-origin", // sends the PHP session cookie
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  })
    .then((r) => r.json())
    .then((res) => {
      console.log("[Cart] DB response:", res);
      return res;
    })
    .catch((err) => {
      console.warn("[Cart] DB save failed (cart still in localStorage):", err);
    });
}

// Add to cart then redirect to cart page
function addToCartAndGo(id, name, price, image) {
  // 1. Update localStorage right away
  let cart = getCart();
  const index = cart.findIndex((item) => item.id === id);
  if (index > -1) {
    cart[index].quantity += 1;
  } else {
    cart.push({ id, name, price: parseFloat(price), image, quantity: 1 });
  }
  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartIcon();

  // 2. Save to DB, then redirect
  _syncCartToDB({
    id,
    name,
    price: parseFloat(price),
    image,
    quantity: 1,
  }).finally(() => {
    window.location.href = CART_PAGE_URL;
  });
}

// Add to cart without redirecting (for quick-add / toast flows)
function addToCart(id, name, price, image) {
  let cart = getCart();
  const index = cart.findIndex((item) => item.id === id);
  if (index > -1) {
    cart[index].quantity += 1;
  } else {
    cart.push({ id, name, price: parseFloat(price), image, quantity: 1 });
  }
  localStorage.setItem("cart", JSON.stringify(cart));
  updateCartIcon();
  _syncCartToDB({ id, name, price: parseFloat(price), image, quantity: 1 });
}

// Keep badge in sync when user has multiple tabs open
window.addEventListener("storage", function (e) {
  if (e.key === "cart") updateCartIcon();
});

// ══════════════════════════════════════════════════════════════
//  NAVBAR BEHAVIOUR
// ══════════════════════════════════════════════════════════════
document.addEventListener("DOMContentLoaded", function () {
  // ── 1. Refresh cart badge on every page load ─────────────────
  updateCartIcon();

  // ── 2. Scroll shadow ─────────────────────────────────────────
  const navbar = document.querySelector(".premium-navbar");
  if (navbar) {
    window.addEventListener("scroll", () => {
      navbar.classList.toggle("scrolled", window.scrollY > 80);
    });
  }

  // ── 3. Force-initialize Bootstrap dropdowns ──────────────────
  if (typeof bootstrap !== "undefined") {
    document
      .querySelectorAll('[data-bs-toggle="dropdown"]')
      .forEach(function (el) {
        new bootstrap.Dropdown(el, { popperConfig: { strategy: "fixed" } });
      });
  }

  // ── 4. Close dropdown on item click (mobile fix) ─────────────
  document
    .querySelectorAll(".navbar .dropdown-menu .dropdown-item")
    .forEach(function (item) {
      item.addEventListener("click", function () {
        const toggle = item
          .closest(".dropdown")
          .querySelector('[data-bs-toggle="dropdown"]');
        if (toggle && typeof bootstrap !== "undefined") {
          const instance = bootstrap.Dropdown.getInstance(toggle);
          if (instance) instance.hide();
        }
      });
    });
});
