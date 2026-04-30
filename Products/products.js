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
    // reset search
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

//updateCartIcon
function updateCartIcon() {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];

  // CHANGE: Use .length to count unique items only
  const uniqueItemsCount = cart.length;

  const cartCountElement = document.getElementById("cart-count");
  if (cartCountElement) {
    cartCountElement.innerText = uniqueItemsCount;
  }
}

// Run on page load
document.addEventListener("DOMContentLoaded", updateCartIcon);

// ── FIX: Single, correct definition of addToCartAndGo with full cart logic
function addToCartAndGo(id, name, price, image) {
  // 1. Get current cart from localStorage
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  // 2. Create product object
  const product = {
    id: id,
    name: name,
    price: parseFloat(price),
    image: image,
    quantity: 1,
  };

  // 3. If already in cart, increase quantity; otherwise add it
  const index = cart.findIndex((item) => item.id === id);
  if (index > -1) {
    cart[index].quantity += 1;
  } else {
    cart.push(product);
  }

  // 4. Save to localStorage
  localStorage.setItem("cart", JSON.stringify(cart));

  // 5. Update navbar badge immediately
  updateCartIcon();

  // 6. Redirect to Cart page
  // NOTE: Make sure this path matches your actual folder name exactly (spaces included)
  window.location.href = "../Add to Cart and CheckOut/Cart.php";
}
