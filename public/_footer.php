<?php
// --- public/_footer.php ---
?>

</main>

<footer class="bg-dark text-light mt-5 py-5">
  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <h5>Orvigo</h5>
        <p><?php echo h(ORVIGO_DESCRIPTION); ?></p>
        <p><strong>Serving:</strong> <?php echo h(ORVIGO_DEFAULT_CITY); ?></p>
      </div>
      <div class="col-md-3">
        <h6>Company</h6>
        <ul class="list-unstyled">
          <li><a class="text-light" href="about.php">About</a></li>
          <li><a class="text-light" href="services.php">Services</a></li>
          <li><a class="text-light" href="faq.php">FAQ</a></li>
          <li><a class="text-light" href="contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="col-md-3">
        <h6>Support</h6>
        <ul class="list-unstyled">
          <li><a class="text-light" href="terms-and-conditions.php">T&C</a></li>
          <li><a class="text-light" href="privacy-policy.php">Privacy</a></li>
          <li><a class="text-light" href="my-bookings.php">My Bookings</a></li>
        </ul>
      </div>
      <div class="col-md-2">
        <h6>Contact</h6>
        <p class="mb-1">Phone: <?php echo h(ORVIGO_CONTACT_PHONE); ?></p>
        <p>Email: <a class="text-light" href="mailto:<?php echo h(ORVIGO_ADMIN_EMAIL); ?>"><?php echo h(ORVIGO_ADMIN_EMAIL); ?></a></p>
        <p class="mt-2">
          <a class="text-light me-2" href="#"><i class="fab fa-facebook"></i></a>
          <a class="text-light me-2" href="#"><i class="fab fa-instagram"></i></a>
          <a class="text-light" href="#"><i class="fab fa-whatsapp"></i></a>
        </p>
      </div>
    </div>
    <hr class="border-secondary">
    <div class="d-flex justify-content-between">
      <small>&copy; <?php echo date('Y') . ' ' . h(ORVIGO_NAME); ?>. All rights reserved.</small>
      <small>Currently serving <?php echo h(ORVIGO_DEFAULT_CITY); ?>.</small>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init();</script>
<!-- Razorpay Checkout JS (required for online payments) -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/validation.js"></script>

</body>
</html>
