<?php
/**
 * cart-init.php
 * Include this file in EVERY page that has a navbar (inside the <head> or just before </body>)
 * It injects the user's DB cart into localStorage right after login.
 * After the first page load it clears itself so it only runs once per login.
 */
if (!empty($_SESSION['cart_on_login'])):
    $cartJson = $_SESSION['cart_on_login'];
    unset($_SESSION['cart_on_login']); // clear so it only runs once
?>
<script>
  // User just logged in — replace localStorage cart with their saved DB cart
  try {
    const dbCart = <?= $cartJson ?>;
    localStorage.setItem('cart', JSON.stringify(dbCart));
    // Update badge immediately
    if (typeof updateCartIcon === 'function') updateCartIcon();
  } catch(e) {
    console.warn('[Cart] Failed to restore cart from DB:', e);
  }
</script>
<?php endif; ?>
