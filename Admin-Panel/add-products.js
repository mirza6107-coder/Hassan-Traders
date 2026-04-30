/* ══════════════════════════════════════════════
   HASSAN TRADERS — ADD PRODUCT PAGE SCRIPTS
   add-products.js
══════════════════════════════════════════════ */

/* ──────────────────────────────────────────
   IMAGE PREVIEW
────────────────────────────────────────── */
function previewImage(event) {
  const file = event.target.files[0];
  if (!file) return;

  const reader    = new FileReader();
  const preview   = document.getElementById('imagePreview');
  const zone      = document.getElementById('uploadZone');
  const previewWrap = document.getElementById('imagePreviewWrap');
  const previewThumb = document.getElementById('previewThumb');

  reader.onload = function () {
    if (reader.readyState === 2) {
      preview.src = reader.result;
      zone.style.display        = 'none';
      previewWrap.style.display = 'block';

      // Update live preview thumbnail
      if (previewThumb) {
        previewThumb.innerHTML = `<img src="${reader.result}" alt="Preview" style="width:100%;height:100%;object-fit:cover;" />`;
      }

      updateProgress();
      showToast('✅ Image uploaded successfully!', 'success');
    }
  };

  reader.readAsDataURL(file);
}

function removeImage() {
  document.getElementById('productImage').value = '';
  document.getElementById('imagePreview').src   = '';
  document.getElementById('imagePreviewWrap').style.display = 'none';
  document.getElementById('uploadZone').style.display       = 'block';
  document.getElementById('previewThumb').innerHTML         = '<i class="bi bi-image"></i>';
  updateProgress();
}

/* ──────────────────────────────────────────
   DRAG & DROP
────────────────────────────────────────── */
function handleDragOver(e) {
  e.preventDefault();
  document.getElementById('uploadZone').classList.add('dragover');
}

function handleDragLeave(e) {
  document.getElementById('uploadZone').classList.remove('dragover');
}

function handleDrop(e) {
  e.preventDefault();
  document.getElementById('uploadZone').classList.remove('dragover');

  const file = e.dataTransfer.files[0];
  if (!file || !file.type.startsWith('image/')) {
    showToast('❌ Please drop an image file.', 'error');
    return;
  }

  // Inject the file into the input
  const dt = new DataTransfer();
  dt.items.add(file);
  const input = document.getElementById('productImage');
  input.files = dt.files;

  previewImage({ target: input });
}

/* ──────────────────────────────────────────
   LIVE PREVIEW UPDATES
────────────────────────────────────────── */
function updatePreviewPrice() {
  const val = document.getElementById('basePrice').value;
  const el  = document.getElementById('prev-price');
  if (!el) return;
  el.textContent = val ? 'Rs. ' + parseInt(val).toLocaleString() : '—';
}

// Status badge color
const statusEl = document.getElementById('productStatus');
if (statusEl) {
  statusEl.addEventListener('change', function () {
    const badge = document.getElementById('prev-status');
    if (!badge) return;
    const map = {
      published: { text: 'Published', bg: 'rgba(39,174,96,0.1)',  color: '#27ae60' },
      draft:     { text: 'Draft',     bg: 'rgba(243,156,18,0.1)', color: '#e67e22' },
      inactive:  { text: 'Inactive',  bg: 'rgba(108,117,125,0.1)',color: '#6c757d' },
    };
    const s = map[this.value] || map.published;
    badge.textContent       = s.text;
    badge.style.background  = s.bg;
    badge.style.color       = s.color;
  });
}

/* ──────────────────────────────────────────
   FORM COMPLETION PROGRESS
────────────────────────────────────────── */
const progressFields = [
  { id: 'productName',   pfId: 'pf-name',          label: 'Product Name' },
  { id: 'category',      pfId: 'pf-category',       label: 'Category' },
  { id: 'basePrice',     pfId: 'pf-price',          label: 'Sale Price' },
  { id: 'orignalPrice',  pfId: 'pf-orignalprice',   label: 'Original Price' },
];

function updateProgress() {
  let done = 0;

  progressFields.forEach(function (f) {
    const el  = document.getElementById(f.id);
    const pf  = document.getElementById(f.pfId);
    const val = el ? el.value.trim() : '';
    const filled = val !== '' && val !== '0' && val !== 'Select Category' && el.value !== '';

    if (filled) {
      done++;
      if (pf) { pf.className = 'pf-item done'; pf.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${f.label}`; }
    } else {
      if (pf) { pf.className = 'pf-item'; pf.innerHTML = `<i class="bi bi-circle"></i> ${f.label}`; }
    }
  });

  // Check image separately
  const imgFilled  = document.getElementById('productImage').files.length > 0;
  const pfImg      = document.getElementById('pf-image');
  if (imgFilled) {
    done++;
    if (pfImg) { pfImg.className = 'pf-item done'; pfImg.innerHTML = `<i class="bi bi-check-circle-fill"></i> Product Image`; }
  } else {
    if (pfImg) { pfImg.className = 'pf-item'; pfImg.innerHTML = `<i class="bi bi-circle"></i> Product Image`; }
  }

  const total   = progressFields.length + 1; // +1 for image
  const pct     = Math.round((done / total) * 100);

  const fill    = document.getElementById('progressFill');
  const pctEl   = document.getElementById('progressPct');
  if (fill)  fill.style.width   = pct + '%';
  if (pctEl) pctEl.textContent  = pct + '%';
}

// Attach change listeners for progress
['productName','category','basePrice','orignalPrice','stockQuantity'].forEach(function (id) {
  const el = document.getElementById(id);
  if (el) el.addEventListener('input', updateProgress);
});
const catEl = document.getElementById('category');
if (catEl) catEl.addEventListener('change', updateProgress);

// Run on load
updateProgress();

/* ──────────────────────────────────────────
   TOPBAR DATE
────────────────────────────────────────── */
const dateEl = document.getElementById('topbarDate');
if (dateEl) {
  const d = new Date();
  dateEl.textContent = d.toLocaleDateString('en-PK', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
  dateEl.style.display = 'block';
}

/* ──────────────────────────────────────────
   TOAST NOTIFICATION
────────────────────────────────────────── */
function showToast(message, type) {
  const toast = document.getElementById('toast');
  if (!toast) return;

  toast.textContent  = message;
  toast.className    = 'toast-notify show' + (type ? ' ' + type : '');

  setTimeout(function () {
    toast.classList.remove('show');
  }, 3500);
}

/* ──────────────────────────────────────────
   FORM SUBMIT — validation + loading state
────────────────────────────────────────── */
const form = document.getElementById('addProductForm');
if (form) {
  form.addEventListener('submit', function (e) {
    const name  = document.getElementById('productName').value.trim();
    const cat   = document.getElementById('category').value;
    const price = document.getElementById('basePrice').value;
    const orig  = document.getElementById('orignalPrice').value;

    let hasError = false;

    if (!name) {
      document.getElementById('nameError').textContent = 'Product name is required';
      document.getElementById('productName').focus();
      hasError = true;
    } else {
      document.getElementById('nameError').textContent = '';
    }

    if (!cat) {
      showToast('❌ Please select a category.', 'error');
      hasError = true;
    }

    if (!price || parseFloat(price) <= 0) {
      showToast('❌ Please enter a valid sale price.', 'error');
      hasError = true;
    }

    if (!orig || parseFloat(orig) <= 0) {
      showToast('❌ Please enter a valid original price.', 'error');
      hasError = true;
    }

    if (hasError) {
      e.preventDefault();
      return;
    }

    // Show loading state
    const btn = document.getElementById('submitBtn');
    if (btn) {
      btn.disabled     = true;
      btn.innerHTML    = '<span class="spinner-border spinner-border-sm me-2"></span> Saving…';
    }
  });
}
