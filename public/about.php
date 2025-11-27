<?php
// --- public/about.php ---
require_once __DIR__ . '/../src/helpers.php';
include __DIR__ . '/_header.php';
?>

<section class="container mt-5" data-aos="fade-up">
  <h1>About Orvigo</h1>
  <p class="lead text-muted">Orvigo brings trained technicians to your doorstep in <?php echo h(ORVIGO_DEFAULT_CITY); ?> for reliable appliance and electronics repair.</p>

  <h3 class="mt-4">Our Mission</h3>
  <p>To deliver fast, transparent and reliable doorstep repair services using trained technicians and genuine parts.</p>

  <h3 class="mt-4">Why customers choose Orvigo</h3>
  <ul>
    <li>Technician training & verification</li>
    <li>Convenient home visits</li>
    <li>Transparent pricing after diagnosis</li>
    <li>Service guarantee & follow-up</li>
  </ul>
</section>

<?php include __DIR__ . '/_footer.php'; ?>
