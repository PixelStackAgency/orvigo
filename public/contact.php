<?php
// --- public/contact.php ---
require_once __DIR__ . '/../src/helpers.php';
include __DIR__ . '/_header.php';
?>

<section class="container mt-5" data-aos="fade-up">
  <div class="row">
    <div class="col-md-7">
      <h1>Contact Us</h1>
      <form id="contact-form" method="post" action="../api/contact-submit.php">
        <input type="hidden" name="csrf_token" value="<?php echo h(csrf_token()); ?>">
        <div style="position:absolute;left:-9999px;top:auto;"> 
          <label>Leave empty</label>
          <input type="text" name="website" value="">
        </div>
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input name="phone" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input name="email" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Subject</label>
          <input name="subject" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea name="message" rows="5" class="form-control" required></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Send</button>
      </form>
    </div>
    <div class="col-md-5">
      <h5>Contact Info</h5>
      <p>Phone: <?php echo h(ORVIGO_CONTACT_PHONE); ?></p>
      <p>Email: <?php echo h(ORVIGO_ADMIN_EMAIL); ?></p>
      <p>Working hours: Mon - Sat, 9:00 AM - 7:00 PM</p>
      <img src="assets/img/map-bangalore.svg" alt="Bangalore map" class="img-fluid mt-3" loading="lazy">
    </div>
  </div>
</section>

<?php include __DIR__ . '/_footer.php'; ?>
