/* ══════════════════════════════════════════════
   Hassan Traders — Profile Page JS
   ══════════════════════════════════════════════ */

document.addEventListener("DOMContentLoaded", () => {
  // ── Navbar scroll shadow ────────────────────────────────────────
  const nav = document.getElementById("mainNav");
  if (nav) {
    window.addEventListener("scroll", () => {
      nav.classList.toggle("scrolled", window.scrollY > 20);
    });
  }

  // ── Cart icon count handled by navbar.js ───────────────────────
  if (typeof updateCartIcon === 'function') updateCartIcon();

  // ── Expand / collapse order items ──────────────────────────────
  document.querySelectorAll(".btn-toggle-items").forEach((btn) => {
    const body = btn.closest(".order-block").querySelector(".order-items-body");
    const icon = btn.querySelector(".toggle-icon");
    const label = btn.querySelector(".toggle-label");

    btn.addEventListener("click", () => {
      const isOpen = body.classList.toggle("open");
      if (icon)
        icon.style.transform = isOpen ? "rotate(180deg)" : "rotate(0deg)";
      if (label) label.textContent = isOpen ? "Hide Items" : "Show Items";
    });
  });

  // ── Order search + status filter ───────────────────────────────
  const searchInput = document.getElementById("orderSearch");
  const statusFilter = document.getElementById("statusFilter");
  const orderBlocks = document.querySelectorAll(".order-block");
  const noResultsEl = document.getElementById("filterNoResults");
  const visibleCount = document.getElementById("visibleCount");

  function applyFilters() {
    const q = (searchInput?.value || "").toLowerCase().trim();
    const st = (statusFilter?.value || "").toLowerCase();
    let shown = 0;

    orderBlocks.forEach((block) => {
      const text = block.innerText.toLowerCase();
      const statusEl = block.querySelector(".st-badge");
      const statusText = (statusEl?.innerText || "").toLowerCase().trim();

      const matchQ = !q || text.includes(q);
      const matchSt = !st || statusText.includes(st);
      const visible = matchQ && matchSt;

      block.style.display = visible ? "" : "none";
      if (visible) shown++;
    });

    if (noResultsEl) noResultsEl.style.display = shown === 0 ? "block" : "none";
    if (visibleCount)
      visibleCount.textContent = `${shown} order${shown !== 1 ? "s" : ""}`;
  }

  searchInput?.addEventListener("input", applyFilters);
  statusFilter?.addEventListener("change", applyFilters);

  // Init count
  if (visibleCount)
    visibleCount.textContent = `${orderBlocks.length} order${orderBlocks.length !== 1 ? "s" : ""}`;

  // ── Copy order ID on click ──────────────────────────────────────
  document.querySelectorAll(".order-ref").forEach((ref) => {
    ref.style.cursor = "pointer";
    ref.title = "Click to copy";
    ref.addEventListener("click", () => {
      const id = ref.textContent.replace(/[^0-9]/g, "").trim();
      navigator.clipboard?.writeText(id).then(() => {
        const orig = ref.textContent;
        ref.textContent = "✓ Copied!";
        setTimeout(() => (ref.textContent = orig), 1500);
      });
    });
  });

  // ── Smooth scroll to orders section ────────────────────────────
  document.querySelectorAll('a[href="#orders"]').forEach((a) => {
    a.addEventListener("click", (e) => {
      e.preventDefault();
      document.getElementById("orders")?.scrollIntoView({ behavior: "smooth" });
    });
  });

  // ── Animate order blocks on scroll (IntersectionObserver) ──────
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = "1";
          entry.target.style.transform = "translateY(0)";
          io.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.08 },
  );

  orderBlocks.forEach((block, i) => {
    block.style.opacity = "0";
    block.style.transform = "translateY(14px)";
    block.style.transition = `opacity 0.36s ${i * 0.06}s ease, transform 0.36s ${i * 0.06}s ease`;
    io.observe(block);
  });

  // ── Toast notifications ─────────────────────────────────────────
  window.showToast = function (msg, type = "success") {
    let container = document.getElementById("toastContainer");
    if (!container) {
      container = document.createElement("div");
      container.id = "toastContainer";
      container.style.cssText =
        "position:fixed;bottom:28px;right:28px;z-index:9999;display:flex;flex-direction:column;gap:10px;";
      document.body.appendChild(container);
    }
    const toast = document.createElement("div");
    toast.style.cssText = `
      background:#1a1e27;color:#fff;padding:13px 20px;border-radius:12px;
      font-size:13.5px;font-weight:600;display:flex;align-items:center;gap:10px;
      box-shadow:0 8px 28px rgba(0,0,0,0.2);
      opacity:0;transform:translateY(8px);
      transition:opacity .25s,transform .25s;
      font-family:'Plus Jakarta Sans',system-ui,sans-serif;
    `;
    const ico = type === "success" ? "✓" : "!";
    const bg = type === "success" ? "#16a34a" : "#c0392b";
    toast.innerHTML = `
      <span style="width:22px;height:22px;border-radius:50%;background:${bg};display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0;">${ico}</span>
      ${msg}
    `;
    container.appendChild(toast);
    requestAnimationFrame(() => {
      toast.style.opacity = "1";
      toast.style.transform = "translateY(0)";
    });
    setTimeout(() => {
      toast.style.opacity = "0";
      toast.style.transform = "translateY(8px)";
      setTimeout(() => toast.remove(), 280);
    }, 3200);
  };
});