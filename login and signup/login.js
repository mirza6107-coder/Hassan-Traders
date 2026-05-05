/* ══════════════════════════════════════════════
   HASSAN TRADERS — LOGIN & SIGNUP PAGE SCRIPTS
   login.js
══════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    /* ──────────────────────────────────────────
       TOGGLE BETWEEN SIGN IN / SIGN UP
    ────────────────────────────────────────── */
    function switchToSignUp() {
        document.getElementById("formSignIn").style.display = "none";
        document.getElementById("formSignUp").style.display = "block";
        document.getElementById("panelSignIn").style.display = "none";
        document.getElementById("panelSignUp").style.display = "block";

        clearErrors();
    }

    function switchToSignIn() {
        document.getElementById("formSignUp").style.display = "none";
        document.getElementById("formSignIn").style.display = "block";
        document.getElementById("panelSignUp").style.display = "none";
        document.getElementById("panelSignIn").style.display = "block";

        clearErrors();
    }

    // Attach toggle buttons
    const registerToggleBtn = document.getElementById("registerToggleBtn");
    const loginToggleBtn = document.getElementById("loginToggleBtn");

    if (registerToggleBtn) registerToggleBtn.addEventListener("click", switchToSignUp);
    if (loginToggleBtn) loginToggleBtn.addEventListener("click", switchToSignIn);

    /* ──────────────────────────────────────────
       CLEAR ALL ERROR MESSAGES
    ────────────────────────────────────────── */
    function clearErrors() {
        document.querySelectorAll(".lg-error").forEach(el => {
            el.textContent = "";
        });
    }

    /* ──────────────────────────────────────────
       SHOW / HIDE PASSWORD
    ────────────────────────────────────────── */
    window.togglePass = function(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector("i");

        if (input.type === "password") {
            input.type = "text";
            icon.className = "bi bi-eye-slash";
        } else {
            input.type = "password";
            icon.className = "bi bi-eye";
        }
    };

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
                { w: "0%",   bg: "transparent", text: "" },
                { w: "25%",  bg: "#ef4444", text: "Weak" },
                { w: "50%",  bg: "#f97316", text: "Fair" },
                { w: "75%",  bg: "#eab308", text: "Good" },
                { w: "90%",  bg: "#22c55e", text: "Strong" },
                { w: "100%", bg: "#16a34a", text: "Very Strong" }
            ];

            const lvl = levels[Math.min(strength, 5)];
            fill.style.width = val.length === 0 ? "0%" : lvl.w;
            fill.style.background = lvl.bg;
            label.textContent = val.length === 0 ? "" : lvl.text;
            label.style.color = lvl.bg;
        });
    }

    /* ──────────────────────────────────────────
       SHAKE ANIMATION
    ────────────────────────────────────────── */
    function shakeField(inputId) {
        const el = document.getElementById(inputId);
        if (!el) return;
        el.style.animation = "none";
        void el.offsetWidth; // Force reflow
        el.style.animation = "shake 0.4s ease";
        
        el.addEventListener("animationend", () => {
            el.style.animation = "";
        }, { once: true });
    }

    /* ──────────────────────────────────────────
       SIGN IN FORM VALIDATION
    ────────────────────────────────────────── */
    const signinForm = document.getElementById("signinForm");
    if (signinForm) {
        signinForm.addEventListener("submit", function (e) {
            clearErrors();
            let valid = true;

            const email = document.getElementById("loginEmail").value.trim();
            const password = document.getElementById("loginPassword").value;

            if (!email || !email.includes("@") || !email.includes(".")) {
                document.getElementById("loginEmailError").textContent = "Please enter a valid email address";
                shakeField("loginEmail");
                valid = false;
            }

            if (password.length < 1) {
                document.getElementById("loginPasswordError").textContent = "Password is required";
                shakeField("loginPassword");
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    /* ──────────────────────────────────────────
       SIGN UP FORM VALIDATION
    ────────────────────────────────────────── */
    const signupForm = document.getElementById("signupForm");
    if (signupForm) {
        signupForm.addEventListener("submit", function (e) {
            clearErrors();
            let valid = true;

            const name = document.getElementById("signupName").value.trim();
            const email = document.getElementById("signupEmail").value.trim();
            const password = document.getElementById("signupPassword").value;

            if (name.length < 3) {
                document.getElementById("signupNameError").textContent = "Full name must be at least 3 characters";
                shakeField("signupName");
                valid = false;
            }

            if (!email || !email.includes("@") || !email.includes(".")) {
                document.getElementById("signupEmailError").textContent = "Please enter a valid email address";
                shakeField("signupEmail");
                valid = false;
            }

            if (password.length < 6) {
                document.getElementById("signupPasswordError").textContent = "Password must be at least 6 characters";
                shakeField("signupPassword");
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    /* ──────────────────────────────────────────
       REMEMBER ME + AUTOFILL
    ────────────────────────────────────────── */
    const rememberCheckbox = document.getElementById("rememberMe");
    const loginEmailInput = document.getElementById("loginEmail");

    if (rememberCheckbox && loginEmailInput) {
        // Load saved email
        const savedEmail = localStorage.getItem("hassan_traders_email");
        if (savedEmail) {
            loginEmailInput.value = savedEmail;
            rememberCheckbox.checked = true;
        }

        // Save email when checkbox is used
        rememberCheckbox.addEventListener("change", function () {
            if (this.checked && loginEmailInput.value.trim()) {
                localStorage.setItem("hassan_traders_email", loginEmailInput.value.trim());
            } else {
                localStorage.removeItem("hassan_traders_email");
            }
        });
    }

    /* ──────────────────────────────────────────
       INJECT KEYFRAMES
    ────────────────────────────────────────── */
    const style = document.createElement("style");
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%      { transform: translateX(-6px); }
            40%      { transform: translateX(6px); }
            60%      { transform: translateX(-4px); }
            80%      { transform: translateX(4px); }
        }
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);

});