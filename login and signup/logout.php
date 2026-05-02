<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head><title>Logging out...</title></head>
<body>
<script>
  // Wipe the cart so the next user starts clean
  localStorage.removeItem('cart');
  window.location.href = '../login and signup/login.php';
</script>
</body>
</html>