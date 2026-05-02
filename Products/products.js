// ── Navbar scroll shadow
window.addEventListener("scroll", () => {
  document.getElementById("mainNav")
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
    document.querySelectorAll(".product-card-wrap").forEach((c) => (c.style.display = ""));
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
document.getElementById("productSearch").addEventListener("search", filterCards);

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
    .then(r => r.json())
    .then(p => {
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
  const imgSrc   = `../Admin-Panel/uploads/${p.image}`;
  const fallback = `../Images/no-image.png`;
  const hasOrig  = p.orignalprice > 0 && p.orignalprice !== p.price;
  const jsName   = p.name.replace(/'/g, "\\'");

  // ── Stock badge ──
  let stockBadge;
  if (p.stock === 0) {
    stockBadge = `<span class="qv-stock-badge qv-stock-out"><i class="bi bi-x-circle-fill"></i> Out of Stock</span>`;
  } else if (p.stock < 20) {
    stockBadge = `<span class="qv-stock-badge qv-stock-low"><i class="bi bi-exclamation-circle-fill"></i> Low Stock</span>`;
  } else {
    stockBadge = `<span class="qv-stock-badge qv-stock-ok"><i class="bi bi-check-circle-fill"></i> In Stock</span>`;
  }

  // ── Discount tag ──
  let discountTag = '';
  if (hasOrig && p.orignalprice > p.price) {
    const pct = Math.round(((p.orignalprice - p.price) / p.orignalprice) * 100);
    discountTag = `<span class="qv-discount-tag">${pct}% Off</span>`;
  }

  // ══════════════════════════════════════════════════
  //  DESCRIPTION PARSER
  //  Handles both plain text and structured text with
  //  section headers like "Key Features:", "Ideal For:",
  //  "Technical Specifications:", "Installation Pro-Tip:" etc.
  //  Falls back to clean plain-text rendering if no structure found.
  // ══════════════════════════════════════════════════
  const rawDesc = (p.description || '').trim();

  // Section header patterns to detect
  const SECTION_PATTERNS = [
    { key: 'features',  re: /^key\s*features?\s*:/i },
    { key: 'ideal',     re: /^ideal\s*for\s*:/i },
    { key: 'specs',     re: /^technical\s*specifications?\s*:/i },
    { key: 'protip',    re: /^(pro.?tip|installation\s*pro.?tip)\s*:?/i },
    { key: 'install',   re: /^installation\s*[^:]*\s*:/i },
  ];

  // Split raw description into logical blocks
  // A block starts either at double newline OR at a known section header line
  const allLines  = rawDesc.split(/\r?\n/);
  const blocks    = [];   // [ { header: str|null, lines: str[] } ]
  let current     = { header: null, lines: [] };

  allLines.forEach(line => {
    const trimmed = line.trim();
    // Detect section header (short line ending in colon, or matching known patterns)
    const isSectionHeader =
      SECTION_PATTERNS.some(sp => sp.re.test(trimmed)) ||
      (/^[A-Z][^.!?]{3,60}:$/.test(trimmed) && trimmed.length < 70);

    if (isSectionHeader) {
      if (current.lines.some(l => l.trim())) blocks.push(current);
      current = { header: trimmed.replace(/:$/, ''), lines: [] };
    } else {
      current.lines.push(line);
    }
  });
  if (current.lines.some(l => l.trim()) || current.header) blocks.push(current);

  // ── Helper: turn a list of lines into bullet li items ──
  function parseListLines(lines) {
    // Lines may use bullets: •, -, *, numbers, or just plain text
    const items = [];
    let accumulator = '';

    lines.forEach(line => {
      const t = line.trim();
      if (!t) {
        if (accumulator.trim()) { items.push(accumulator.trim()); accumulator = ''; }
        return;
      }
      if (/^[•\-\*]/.test(t) || /^\d+[\.\)]/.test(t)) {
        if (accumulator.trim()) items.push(accumulator.trim());
        accumulator = t.replace(/^[•\-\*\d\.\)]+\s*/, '');
      } else {
        accumulator += (accumulator ? ' ' : '') + t;
      }
    });
    if (accumulator.trim()) items.push(accumulator.trim());
    return items.filter(Boolean);
  }

  // ── Helper: parse a "Feature Label: description" line ──
  function parseLabeledItem(text) {
    // Matches "Bold Label: rest of text" — bold label is max ~60 chars
    const m = text.match(/^([^:]{2,60}):\s+([\s\S]+)/);
    if (m) return { label: m[1].trim(), body: m[2].trim() };
    return { label: null, body: text };
  }

  // ── Render each block ──
  let introHtml    = '';
  let featuresHtml = '';
  let idealHtml    = '';
  let specsTableHtml = '';  // from description (if any)
  let proTipHtml   = '';
  let extraHtml    = '';

  blocks.forEach(block => {
    const h     = (block.header || '').trim();
    const items = parseListLines(block.lines);
    const hLower = h.toLowerCase();

    if (!h) {
      // Intro paragraph (no header)
      const text = block.lines.join('\n').trim();
      if (text) introHtml += `<p class="qv-intro">${text.replace(/\n/g,'<br>')}</p>`;
      return;
    }

    if (/key\s*feature/i.test(hLower)) {
      if (items.length) {
        featuresHtml = `
          <div class="qv-section-title"><i class="bi bi-stars"></i> Key Features</div>
          <ul class="qv-features">
            ${items.map(item => {
              const { label, body } = parseLabeledItem(item);
              if (label) {
                return `<li>
                  <span class="qv-feat-dot"><i class="bi bi-check2"></i></span>
                  <span><strong class="qv-feat-label">${label}:</strong> ${body}</span>
                </li>`;
              }
              return `<li>
                <span class="qv-feat-dot"><i class="bi bi-check2"></i></span>
                <span>${body}</span>
              </li>`;
            }).join('')}
          </ul>`;
      }

    } else if (/ideal\s*for/i.test(hLower)) {
      if (items.length) {
        idealHtml = `
          <div class="qv-section-title"><i class="bi bi-lightning-charge-fill"></i> Ideal For</div>
          <ul class="qv-ideal">
            ${items.map(item => {
              const { label, body } = parseLabeledItem(item);
              if (label) {
                return `<li>
                  <i class="bi bi-arrow-right-short qv-ideal-icon"></i>
                  <span><strong class="qv-ideal-label">${label}:</strong> ${body}</span>
                </li>`;
              }
              return `<li>
                <i class="bi bi-arrow-right-short qv-ideal-icon"></i>
                <span>${body}</span>
              </li>`;
            }).join('')}
          </ul>`;
      }

    } else if (/technical\s*spec/i.test(hLower)) {
      // Description has its own spec table — parse key: value rows
      if (items.length) {
        specsTableHtml = `
          <div class="qv-section-title"><i class="bi bi-table"></i> Technical Specifications</div>
          <table class="qv-specs-table qv-specs-inline">
            <thead><tr><th>Feature</th><th>Specification</th></tr></thead>
            <tbody>
              ${items.map(item => {
                const { label, body } = parseLabeledItem(item);
                if (label) {
                  return `<tr>
                    <td class="qv-spec-key">${label}</td>
                    <td class="qv-spec-val">${body}</td>
                  </tr>`;
                }
                return `<tr><td colspan="2" class="qv-spec-val">${body}</td></tr>`;
              }).join('')}
            </tbody>
          </table>`;
      }

    } else if (/pro.?tip|installation/i.test(hLower)) {
      const text = block.lines.join(' ').trim();
      if (text) {
        proTipHtml = `
          <div class="qv-protip">
            <div class="qv-protip-title"><i class="bi bi-lightbulb-fill"></i> Pro Tip</div>
            <p>${text}</p>
          </div>`;
      }

    } else {
      // Unknown header — render as a generic section
      const text = block.lines.join('\n').trim();
      if (items.length > 1) {
        extraHtml += `
          <div class="qv-section-title">${h}</div>
          <ul class="qv-features">
            ${items.map(item => {
              const { label, body } = parseLabeledItem(item);
              if (label) return `<li><span class="qv-feat-dot"><i class="bi bi-dot"></i></span><span><strong class="qv-feat-label">${label}:</strong> ${body}</span></li>`;
              return `<li><span class="qv-feat-dot"><i class="bi bi-dot"></i></span><span>${body}</span></li>`;
            }).join('')}
          </ul>`;
      } else if (text) {
        extraHtml += `
          <div class="qv-section-title">${h}</div>
          <p class="qv-intro">${text.replace(/\n/g,'<br>')}</p>`;
      }
    }
  });

  // If absolutely nothing was parsed, just show raw text
  const hasStructure = featuresHtml || idealHtml || specsTableHtml || proTipHtml || extraHtml;
  if (!hasStructure && !introHtml && rawDesc) {
    introHtml = `<p class="qv-intro">${rawDesc.replace(/\n/g,'<br>')}</p>`;
  }

  // ── Specs sidebar (always shown from DB fields) ──
  const specRows = [
    p.category ? ['Category',        p.category] : null,
    p.brand    ? ['Brand',           p.brand]    : null,
                 ['Selling Price',   `Rs. ${Number(p.price).toLocaleString('en-PK')}`],
    hasOrig    ? ['Original Price',  `Rs. ${Number(p.orignalprice).toLocaleString('en-PK')}`] : null,
    hasOrig    ? ['Discount',        discountTag || '—'] : null,
                 ['Availability',    p.stock === 0 ? 'Out of Stock' : p.stock < 20 ? 'Low Stock' : 'In Stock'],
  ].filter(Boolean);

  const sidebarSpecsHtml = `
    <div class="qv-section-title"><i class="bi bi-info-circle-fill"></i> Specifications</div>
    <table class="qv-specs-table">
      <thead><tr><th>Feature</th><th>Specification</th></tr></thead>
      <tbody>
        ${specRows.map(([k, v]) => `
          <tr>
            <td class="qv-spec-key">${k}</td>
            <td class="qv-spec-val">${v}</td>
          </tr>`).join('')}
      </tbody>
    </table>`;

  // ── CTA ──
  const cartBtn = p.stock > 0
    ? `<button class="qv-btn-buy" onclick="addToCartAndGo(${p.id}, '${jsName}', ${p.price}, '${p.image}')">
         <i class="bi bi-cart-plus-fill"></i> Add to Cart
       </button>`
    : `<button class="qv-btn-buy" disabled>
         <i class="bi bi-x-circle"></i> Out of Stock
       </button>`;

  return `
    <!-- ── Dark hero strip ── -->
    <div class="qv-hero">
      <button class="qv-close" onclick="closeQuickView()"><i class="bi bi-x-lg"></i></button>
      <div class="qv-hero-inner">

        <div class="qv-img-box">
          <img src="${imgSrc}" alt="${p.name}" onerror="this.src='${fallback}'">
          ${p.category ? `<span class="qv-img-cat">${p.category}</span>` : ''}
        </div>

        <div class="qv-hero-text">
          <div>
            <div class="qv-headline-row">
              <span class="qv-diamond">◆</span>
              <h2 class="qv-headline">
                ${p.brand ? `<span class="qv-headline-brand">${p.brand} —</span> ` : ''}${p.name}
              </h2>
            </div>
            ${introHtml
              ? `<p class="qv-tagline">${introHtml.replace(/<[^>]+>/g,'').substring(0,260)}${introHtml.replace(/<[^>]+>/g,'').length > 260 ? '…' : ''}</p>`
              : ''}
          </div>
          <div class="qv-price-strip">
            <div class="qv-price-now">Rs. ${Number(p.price).toLocaleString('en-PK')}</div>
            ${hasOrig ? `<div class="qv-price-was">Rs. ${Number(p.orignalprice).toLocaleString('en-PK')}</div>` : ''}
            ${discountTag}
            ${stockBadge}
          </div>
        </div>

      </div>
    </div>

    <!-- ── White body ── -->
    <div class="qv-body">

      <!-- Left: rich description -->
      <div class="qv-desc">
        ${featuresHtml}
        ${specsTableHtml}
        ${idealHtml}
        ${proTipHtml}
        ${extraHtml}
        ${!hasStructure ? introHtml : ''}
      </div>

      <!-- Right: specs table + CTA -->
      <div class="qv-sidebar">
        ${sidebarSpecsHtml}
        <div class="qv-cta">
          ${cartBtn}
          <a href="view-products.php?id=${p.id}" class="qv-btn-detail">
            <i class="bi bi-box-arrow-up-right"></i> Full Detail Page
          </a>
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