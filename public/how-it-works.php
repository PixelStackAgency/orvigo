<?php
// --- public/how-it-works.php ---
require_once __DIR__ . '/../src/helpers.php';
include __DIR__ . '/_header.php';
?>

<section class="container mt-5" data-aos="fade-up">
  <h1>How Orvigo Works</h1>
  <div class="row mt-4 g-4">
    <div class="col-md-3">
      <div class="p-3 border rounded text-center">
        <h5>1. Choose Service</h5>
        <p class="small text-muted">Select the appliance and preferred slot.</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="p-3 border rounded text-center">
        <h5>2. Confirm</h5>
        <p class="small text-muted">Booking confirmed via SMS/WhatsApp & email.</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="p-3 border rounded text-center">
        <h5>3. Technician Visit</h5>
        <p class="small text-muted">Technician inspects and repairs at your home.</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="p-3 border rounded text-center">
        <h5>4. Payment & Follow-up</h5>
        <p class="small text-muted">Pay online or to the technician. Follow-up and rating.</p>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/_footer.php'; ?>
