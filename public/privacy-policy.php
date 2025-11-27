<?php
// --- public/privacy-policy.php ---
require_once __DIR__ . '/../src/helpers.php';
include __DIR__ . '/_header.php';
?>

<section class="container mt-5" data-aos="fade-up">
  <h1>Privacy Policy</h1>
  <p>This Privacy Policy describes how <?php echo h(ORVIGO_NAME); ?> collects and uses personal information.</p>
  <h3>Information We Collect</h3>
  <ul>
    <li>Contact details (name, phone, email, address)</li>
    <li>Booking and service details</li>
  </ul>
  <h3>How We Use Your Information</h3>
  <ul>
    <li>To confirm and fulfil service bookings</li>
    <li>To send booking confirmations and status updates</li>
  </ul>
  <p>We do not sell your information. Update contact: <?php echo h(ORVIGO_ADMIN_EMAIL); ?>.</p>
</section>

<?php include __DIR__ . '/_footer.php'; ?>
