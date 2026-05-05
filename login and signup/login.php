<?php
session_start();

$authError  = $_SESSION['auth_error'] ?? '';
unset($_SESSION['auth_error']);

$openSignup = isset($_GET['tab']) && $_GET['tab'] === 'signup';
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hassan Traders - Login & Sign Up</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="../NavBar/navbar.css" />
  <link rel="stylesheet" href="login.css" />
</head>

<body>

  <!-- ══ AUTH PAGE ══ -->
  <div class="lg-page">
    <div class="lg-bg-grid"></div>
    <div class="lg-bg-blob-1"></div>
    <div class="lg-bg-blob-2"></div>

    <div class="lg-wrapper">
      <div class="lg-box" id="authBox">

        <!-- Left Panel -->
        <div class="lg-panel" id="lgPanel">
          <!-- Sign In view -->
          <div class="lg-panel-content" id="panelSignIn">
            <div class="lg-panel-brand">
              <div class="lg-brand-dot"></div>
              <span>Hassan Traders</span>
            </div>
            <h2 class="lg-panel-title">Welcome<br /><em>Back!</em></h2>
            <p class="lg-panel-desc">
              Don't have an account yet? Sign up and get access to all our products.
            </p>
            <button class="lg-panel-btn" id="registerToggleBtn">
              Create Account <i class="bi bi-arrow-right ms-1"></i>
            </button>
            <div class="lg-panel-features">
              <div class="lg-feature"><i class="bi bi-shield-check"></i> Secure Shopping</div>
              <div class="lg-feature"><i class="bi bi-truck"></i> Fast Delivery</div>
              <div class="lg-feature"><i class="bi bi-star-fill"></i> Premium Products</div>
            </div>
          </div>

          <!-- Sign Up view -->
          <div class="lg-panel-content" id="panelSignUp" style="display: none;">
            <div class="lg-panel-brand">
              <div class="lg-brand-dot"></div>
              <span>Hassan Traders</span>
            </div>
            <h2 class="lg-panel-title">Hello,<br /><em>Friend!</em></h2>
            <p class="lg-panel-desc">
              Already have an account? Sign in and continue where you left off.
            </p>
            <button class="lg-panel-btn" id="loginToggleBtn">
              Sign In <i class="bi bi-arrow-right ms-1"></i>
            </button>
            <div class="lg-panel-features">
              <div class="lg-feature"><i class="bi bi-box-seam"></i> 200+ Products</div>
              <div class="lg-feature"><i class="bi bi-people-fill"></i> 5000+ Customers</div>
              <div class="lg-feature"><i class="bi bi-award-fill"></i> Trusted Since 2016</div>
            </div>
          </div>
        </div>

        <!-- Right Forms -->
        <div class="lg-forms">

          <!-- SIGN IN FORM -->
          <div class="lg-form-wrap" id="formSignIn">
            <div class="lg-form-head">
              <h3 class="lg-form-title">Sign <em>In</em></h3>
              <p class="lg-form-sub">Welcome back — enter your credentials below</p>
            </div>

            <!-- Error Message Area -->
            <div id="loginErrorBox" class="alert alert-danger d-none" style="border-radius:10px; font-size:0.95rem;"></div>

            <div class="lg-social-row">
              <a href="#" class="lg-social-btn"><i class="fa-brands fa-google"></i> Google</a>
              <a href="#" class="lg-social-btn"><i class="fa-brands fa-facebook-f"></i> Facebook</a>
            </div>

            <div class="lg-divider"><span>or sign in with email</span></div>

            <form id="signinForm" action="signin.php" method="POST" novalidate>
              <div class="lg-field">
                <label class="lg-label" for="loginEmail">Email Address</label>
                <div class="lg-input-wrap">
                  <i class="bi bi-envelope lg-input-icon"></i>
                  <input type="email" id="loginEmail" name="emailaddress" class="lg-input" placeholder="you@example.com" required />
                </div>
              </div>

              <div class="lg-field">
                <div class="lg-label-row">
                  <label class="lg-label" for="loginPassword">Password</label>
                </div>
                <div class="lg-input-wrap">
                  <i class="bi bi-lock lg-input-icon"></i>
                  <input type="password" id="loginPassword" name="password" class="lg-input" placeholder="Enter your password" required />
                  <button type="button" class="lg-eye-btn" onclick="togglePass('loginPassword', this)">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <div class="lg-remember d-flex justify-content-between align-items-center">
                <label class="lg-check-label">
                  <input type="checkbox" class="lg-check" id="rememberMe" name="remember" />
                  Remember me
                </label>
                <a href="#" class="lg-forgot">Forgot password?</a>
              </div>

              <button type="submit" class="lg-submit-btn">
                <i class="bi bi-lock-fill me-2"></i> Sign In
              </button>
            </form>

            <p class="lg-switch-text">
              Don't have an account?
              <button class="lg-switch-btn" onclick="switchToSignUp()">Create one</button>
            </p>
          </div>

          <!-- SIGN UP FORM (unchanged) -->
          <div class="lg-form-wrap" id="formSignUp" style="display: none;">
            <div class="lg-form-head">
              <h3 class="lg-form-title">Create <em>Account</em></h3>
              <p class="lg-form-sub">
                Join Hassan Traders — it takes less than a minute
              </p>
            </div>

            <div class="lg-social-row">
              <a href="#" class="lg-social-btn"><i class="fa-brands fa-google"></i> Google</a>
              <a href="#" class="lg-social-btn"><i class="fa-brands fa-facebook-f"></i> Facebook</a>
            </div>

            <div class="lg-divider"><span>or register with email</span></div>

            <form
              id="signupForm"
              action="signup.php"
              method="POST"
              novalidate>
              <div class="lg-field">
                <label class="lg-label" for="signupName">Full Name</label>
                <div class="lg-input-wrap">
                  <i class="bi bi-person lg-input-icon"></i>
                  <input
                    type="text"
                    id="signupName"
                    name="fullname"
                    class="lg-input"
                    placeholder="Muhammad Hassan"
                    required />
                </div>
                <div class="lg-error" id="signupNameError"></div>
              </div>

              <div class="lg-field">
                <label class="lg-label" for="signupEmail">Email Address</label>
                <div class="lg-input-wrap">
                  <i class="bi bi-envelope lg-input-icon"></i>
                  <input
                    type="email"
                    id="signupEmail"
                    name="emailaddress"
                    class="lg-input"
                    placeholder="you@example.com"
                    required />
                </div>
                <div class="lg-error" id="signupEmailError"></div>
              </div>

              <div class="lg-field">
                <label class="lg-label" for="signupPassword">Password</label>
                <div class="lg-input-wrap">
                  <i class="bi bi-lock lg-input-icon"></i>
                  <input
                    type="password"
                    id="signupPassword"
                    name="password"
                    class="lg-input"
                    placeholder="Min. 6 characters"
                    required />
                  <button
                    type="button"
                    class="lg-eye-btn"
                    onclick="togglePass('signupPassword', this)">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
                <div class="lg-error" id="signupPasswordError"></div>
                <!-- Password strength bar -->
                <div class="lg-strength-bar" id="strengthBar">
                  <div class="lg-strength-fill" id="strengthFill"></div>
                </div>
                <div class="lg-strength-label" id="strengthLabel"></div>
              </div>

              <button type="submit" class="lg-submit-btn">
                <i class="bi bi-person-plus-fill me-2"></i> Create Account
              </button>
            </form>

            <p class="lg-switch-text">
              Already have an account?
              <button class="lg-switch-btn" onclick="switchToSignIn()">
                Sign in
              </button>
            </p>
          </div>
        </div>
        <!-- /lg-forms -->
      </div>
      <!-- /lg-box -->
    </div>
  </div>

  <script src="login.js"></script>

  <?php if (!empty($_SESSION['cart_on_login'])): ?>
    <script>
      localStorage.setItem('cart', <?= $_SESSION['cart_on_login'] ?>);
      <?php unset($_SESSION['cart_on_login']); ?>
    </script>
  <?php endif; ?>

  <!-- Improved Error Display -->
  <?php if ($authError): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const errorBox = document.getElementById('loginErrorBox');
        errorBox.textContent = <?= json_encode($authError) ?>;
        errorBox.classList.remove('d-none');

        <?php if ($openSignup): ?>
          switchToSignUp();
          // Optional: Show error on signup side too if needed
        <?php endif; ?>
      });
    </script>
  <?php endif; ?>

  <?php if ($openSignup): ?>
    <script>
      document.addEventListener('DOMContentLoaded', switchToSignUp);
    </script>
  <?php endif; ?>

</body>

</html>