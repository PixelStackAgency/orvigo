<?php
// --- public/faq.php ---
require_once __DIR__ . '/../src/helpers.php';
include __DIR__ . '/_header.php';
?>

<section class="container mt-5" data-aos="fade-up">
  <h1>Frequently Asked Questions</h1>
  <div class="accordion mt-4" id="faqAccordion">
    <div class="accordion-item">
      <h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">Which appliances do you service?</button></h2>
      <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion"><div class="accordion-body">We service ACs, Refrigerators, Washing Machines, TVs, Geysers, Microwaves, Water Purifiers and Mobile/Tablet devices in Bangalore.</div></div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">How are prices determined?</button></h2>
      <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">We provide transparent pricing after diagnosis by the technician. No surprise charges.</div></div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">What payment methods are accepted?</button></h2>
      <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Online payments via popular gateways (Razorpay) or cash/card/UPI to the technician after service.</div></div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/_footer.php'; ?>
