<?php
// --- public/admin/login.php ---
require_once __DIR__ . '/../../src/helpers.php';
session_start();
if (!empty($_POST['password'])){
  $submitted = $_POST['password'];
  $ok = false;
  // Prefer hashed verification
  if (defined('ORVIGO_ADMIN_PASSWORD_HASH') && !empty(ORVIGO_ADMIN_PASSWORD_HASH)){
    if (password_verify($submitted, ORVIGO_ADMIN_PASSWORD_HASH)) $ok = true;
  }
  // Fallback: plaintext match (legacy)
  if (!$ok && defined('ORVIGO_ADMIN_PASSWORD') && $submitted === ORVIGO_ADMIN_PASSWORD){
    $ok = true;
  }
  if ($ok){
    $_SESSION['orvigo_admin'] = true;
    header('Location: index.php'); exit;
  } else {
    $error = 'Invalid password';
  }
}
include __DIR__ . '/../_header.php';
?>
<section class="container mt-5">
  <h1>Admin Login</h1>
  <?php if(!empty($error)): ?><div class="alert alert-danger"><?php echo h($error); ?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3"><input type="password" name="password" class="form-control" placeholder="Admin password"></div>
    <button class="btn btn-primary">Login</button>
  </form>
</section>
<?php include __DIR__ . '/../_footer.php'; ?>
