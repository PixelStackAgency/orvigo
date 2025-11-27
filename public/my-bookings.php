<?php
// --- public/my-bookings.php ---
require_once __DIR__ . '/../src/helpers.php';
include __DIR__ . '/_header.php';
?>

<section class="container mt-5" data-aos="fade-up">
  <h1>Track Your Booking</h1>
  <form id="track-booking-form" class="row g-2">
    <div class="col-md-5">
      <input name="booking_id" id="booking_id" class="form-control" placeholder="Booking ID (e.g. ORVIGO-2025-AC-... )" required>
    </div>
    <div class="col-md-4">
      <input name="phone" id="phone" class="form-control" placeholder="Mobile number" required>
    </div>
    <div class="col-md-3">
      <button class="btn btn-primary w-100" type="submit">Track</button>
    </div>
  </form>

  <div id="booking-result" class="mt-4"></div>
</section>

<?php include __DIR__ . '/_footer.php'; ?>
