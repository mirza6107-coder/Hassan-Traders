/* ══════════════════════════════════════════════
   HASSAN TRADERS — LOGIN PAGE SCRIPTS
   login.js
══════════════════════════════════════════════ */

/* ──────────────────────────────────────────
   TOGGLE BETWEEN SIGN IN / SIGN UP
────────────────────────────────────────── */
function switchToSignUp() {
  document.getElementById("formSignIn").style.display = "none";
  document.getElementById("formSignUp").style.display = "block";
  document.getElementById("panelSignIn").style.display = "none";
  document.getElementById("panelSignUp").style.display = "block";

  // Animate in
  document.getElementById("formSignUp").style.animation =
    "fade-up 0.4s ease both";
  document.getElementById("panelSignUp").style.animation =
    "fade-up 0.4s ease both";

  // Clear errors
  clearErrors();
}

function switchToSignIn() {
  document.getElementById("formSignUp").style.display = "none";
  document.getElementById("formSignIn").style.display = "block";
  document.getElementById("panelSignUp").style.display = "none";
  document.getElementById("panelSignIn").style.display = "block";

  // Animate in
  document.getElementById("formSignIn").style.animation =
    "fade-up 0.4s ease both";
  document.getElementById("panelSignIn").style.animation =
    "fade-up 0.4s ease both";

  clearErrors();
}

// Panel toggle buttons
document
  .getElementById("registerToggleBtn")
  .addEventListener("click", switchToSignUp);
document
  .getElementById("loginToggleBtn")
  .addEventListener("click", switchToSignIn);

/* ──────────────────────────────────────────
   CLEAR ALL ERROR MESSAGES
────────────────────────────────────────── */
function clearErrors() {
  document.querySelectorAll(".lg-error").forEach(function (el) {
    el.textContent = "";
  });
}

/* ──────────────────────────────────────────
   SHOW / HIDE PASSWORD
────────────────────────────────────────── */
function togglePass(inputId, btn) {
  const input = document.getElementById(inputId);
  const icon = btn.querySelector("i");

  if (input.type === "password") {
    input.type = "text";
    icon.className = "bi bi-eye-slash";
  } else {
    input.type = "password";
    icon.className = "bi bi-eye";
  }
}

/* ──────────────────────────────────────────
   PASSWORD STRENGTH METER
────────────────────────────────────────── */
const signupPasswordEl = document.getElementById("signupPassword");
if (signupPasswordEl) {
  signupPasswordEl.addEventListener("input", function () {
    const val = this.value;
    const fill = document.getElementById("strengthFill");
    const label = document.getElementById("strengthLabel");
    if (!fill || !label) return;

    let strength = 0;
    if (val.length >= 6) strength++;
    if (val.length >= 10) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const levels = [
      { w: "0%", bg: "transparent", text: "", color: "" },
      { w: "25%", bg: "#ef4444", text: "Weak", color: "#ef4444" },
      { w: "50%", bg: "#f97316", text: "Fair", color: "#f97316" },
      { w: "75%", bg: "#eab308", text: "Good", color: "#eab308" },
      { w: "90%", bg: "#22c55e", text: "Strong", color: "#22c55e" },
      { w: "100%", bg: "#16a34a", text: "Very Strong", color: "#16a34a" },
    ];

    const lvl = levels[Math.min(strength, 5)];
    fill.style.width = val.length === 0 ? "0%" : lvl.w;
    fill.style.background = lvl.bg;
    label.textContent = val.length === 0 ? "" : lvl.text;
    label.style.color = lvl.color;
  });
}

/* ──────────────────────────────────────────
   SIGN UP FORM VALIDATION
────────────────────────────────────────── */
function handleSignup(e) {
  e.preventDefault();
  clearErrors();
  let valid = true;

  const name = document.getElementById("signupName").value.trim();
  const email = document.getElementById("signupEmail").value.trim();
  const password = document.getElementById("signupPassword").value;

  if (name.length < 3) {
    document.getElementById("signupNameError").textContent =
      "Enter at least 3 characters";
    shakeField("signupName");
    valid = false;
  }
  if (!email.includes("@") || !email.includes(".")) {
    document.getElementById("signupEmailError").textContent =
      "Enter a valid email address";
    shakeField("signupEmail");
    valid = false;
  }
  if (password.length < 6) {
    document.getElementById("signupPasswordError").textContent =
      "Password must be at least 6 characters";
    shakeField("signupPassword");
    valid = false;
  }

  if (valid) {
    e.target.submit();
  }
}

/* ──────────────────────────────────────────
   SIGN IN FORM VALIDATION
────────────────────────────────────────── */
function handleSignin(e) {
  e.preventDefault();
  clearErrors();
  let valid = true;

  const email = document.getElementById("loginEmail").value.trim();
  const password = document.getElementById("loginPassword").value;

  if (!email.includes("@") || !email.includes(".")) {
    document.getElementById("loginEmailError").textContent =
      "Enter a valid email address";
    shakeField("loginEmail");
    valid = false;
  }
  if (password.length < 1) {
    document.getElementById("loginPasswordError").textContent =
      "Password is required";
    shakeField("loginPassword");
    valid = false;
  }

  if (valid) {
    // SAVE EMAIL IF CHECKED
    if (rememberCheckbox.checked) {
      localStorage.setItem(
        "hassan_traders_email",
        loginEmailInput.value.trim(),
      );
    } else {
      localStorage.removeItem("hassan_traders_email");
    }

    e.target.submit();
  }
}

/* ──────────────────────────────────────────
   SHAKE ANIMATION on invalid field
────────────────────────────────────────── */
function shakeField(inputId) {
  const el = document.getElementById(inputId);
  if (!el) return;
  el.style.animation = "none";
  // Force reflow
  void el.offsetWidth;
  el.style.animation = "shake 0.4s ease";
  el.addEventListener(
    "animationend",
    function () {
      el.style.animation = "";
    },
    { once: true },
  );
}

/* ──────────────────────────────────────────
   ATTACH FORM HANDLERS
────────────────────────────────────────── */
const signupForm = document.getElementById("signupForm");
const signinForm = document.getElementById("signinForm");
if (signupForm) signupForm.onsubmit = handleSignup;
if (signinForm) signinForm.onsubmit = handleSignin;

/* ──────────────────────────────────────────
   INJECT SHAKE KEYFRAME into document
────────────────────────────────────────── */
(function injectShakeStyle() {
  const style = document.createElement("style");
  style.textContent = `
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20%       { transform: translateX(-6px); }
      40%       { transform: translateX(6px); }
      60%       { transform: translateX(-4px); }
      80%       { transform: translateX(4px); }
    }
    @keyframes fade-up {
      from { opacity: 0; transform: translateY(16px); }
      to   { opacity: 1; transform: translateY(0); }
    }
  `;
  document.head.appendChild(style);
})();
/* ──────────────────────────────────────────
   REMEMBER ME FUNCTIONALITY
────────────────────────────────────────── */
const rememberCheckbox = document.getElementById("rememberMe");
const loginEmailInput = document.getElementById("loginEmail");

// 1. On page load, check if an email was previously saved
window.addEventListener("DOMContentLoaded", () => {
  const savedEmail = localStorage.getItem("hassan_traders_email");
  if (savedEmail && loginEmailInput) {
    loginEmailInput.value = savedEmail;
    rememberCheckbox.checked = true;
  }
});
