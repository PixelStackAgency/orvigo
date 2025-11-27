<?php
// --- public/service-template.php ---
// Template used by individual service pages. Expects $serviceKey and $serviceTitle defined.
require_once __DIR__ . '/../src/helpers.php';
include __DIR__ . '/_header.php';

// Define service-specific options (simplified)
$serviceOptions = [
    'ac' => [
        'options' => ['AC Service (Split/Window)', 'AC Repair - No Cooling', 'AC Gas Refill', 'Installation/Uninstallation'],
        'image' => 'ac.svg'
    ],
    'refrigerator' => [
        'options' => ['Full Service', 'Cooling Issue', 'Water Leakage', 'Compressor Repair'],
        'image' => 'fridge.svg'
    ],
    'washing_machine' => [
        'options' => ['Full Service', 'Motor Repair', 'Installation/Uninstallation'],
        'image' => 'washing.svg'
    ],
    'tv' => [
        'options' => ['Display Issue', 'No Sound', 'Power Issue'],
        'image' => 'tv.svg'
    ],
    'geyser' => [
        'options' => ['No Heating', 'Leakage', 'Installation'],
        'image' => 'geyser.svg'
    ],
    'microwave' => [
        'options' => ['Not Heating', 'Sparking', 'Turntable Issue'],
        'image' => 'microwave.svg'
    ],
    'water_purifier' => [
        'options' => ['Filter Change', 'Low Flow', 'No Water'],
        'image' => 'waterpurifier.svg'
    ],
    'mobile_tablet' => [
        'options' => ['Screen Replacement', 'Battery Issue', 'Charging Problem', 'Other'],
        'image' => 'mobile.svg'
    ],
];

$imageMap = [
  'ac' => 'https://images.unsplash.com/photo-1581579182294-0b9f6b1b5b62?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s=1',
  'refrigerator' => 'https://images.unsplash.com/photo-1582719478172-4f6a9a7a9f1f?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s=2',
  'washing_machine' => 'https://images.unsplash.com/photo-1592928307326-0e4f7a2b8fb5?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s=3',
  'tv' => 'https://images.unsplash.com/photo-1503602642458-232111445657?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s=4',
  'geyser' => 'https://images.unsplash.com/photo-1600651226270-2d6a2f2e2d7b?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s=5',
  'microwave' => 'https://images.unsplash.com/photo-1601050690592-4b5b1836f9f3?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s=6',
  'water_purifier' => 'https://images.unsplash.com/photo-1598511722647-7b777c2e2c2a?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s=7',
  'mobile_tablet' => 'https://images.unsplash.com/photo-1510557880182-3d4d3d7f6b8e?q=80&w=1200&auto=format&fit=crop&ixlib=rb-4.0.3&s=8'
];

$metaImage = $imageMap[$serviceKey] ?? ('assets/img/' . ($serviceOptions[$serviceKey]['image'] ?? 'hero-1.svg'));
?>

<section class="container mt-5">
  <div class="row g-4">
    <div class="col-lg-8" data-aos="fade-up">
      <div class="d-flex align-items-center gap-3">
        <img src="<?php echo $metaImage; ?>" alt="<?php echo h($serviceTitle); ?>" width="120" loading="lazy">
        <div>
          <h1 class="h3"><?php echo h($serviceTitle . ' in ' . ORVIGO_DEFAULT_CITY); ?></h1>
          <p class="text-muted small"><?php echo h('We provide expert ' . $serviceTitle . ' technicians at your doorstep in ' . ORVIGO_DEFAULT_CITY . '.'); ?></p>
          <div class="mt-2">
            <span class="badge bg-light text-dark me-1">Verified technicians</span>
            <span class="badge bg-light text-dark me-1">Doorstep</span>
            <span class="badge bg-light text-dark">Same-day slots</span>
          </div>
        </div>
      </div>

      <hr>

      <h5>Service Options</h5>
      <form id="service-booking-form">
        <input type="hidden" name="csrf_token" value="<?php echo h(csrf_token()); ?>">
        <input type="hidden" name="service_category" value="<?php echo h($serviceKey); ?>">
        <!-- Honeypot field to trap bots; leave empty -->
        <div style="position:absolute;left:-9999px;top:auto;"> 
          <label>Leave empty</label>
          <input type="text" name="website" value="">
        </div>
        <div class="mb-3">
          <?php foreach ($serviceOptions[$serviceKey]['options'] as $i => $opt): ?>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="service_options[]" value="<?php echo h($opt); ?>" id="opt-<?php echo $i; ?>">
              <label class="form-check-label" for="opt-<?php echo $i; ?>"><?php echo h($opt); ?></label>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="mb-3">
          <label class="form-label">Preferred Date</label>
          <input type="date" name="preferred_date" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Preferred Time Slot</label>
          <select name="time_slot" class="form-select" required>
            <option value="10:00-12:00">10 AM - 12 PM</option>
            <option value="12:00-14:00">12 PM - 2 PM</option>
            <option value="14:00-16:00">2 PM - 4 PM</option>
            <option value="16:00-18:00">4 PM - 6 PM</option>
          </select>
        </div>

        <h5>Customer Details</h5>
        <div class="row g-2">
          <div class="col-md-6 mb-2">
            <input name="name" class="form-control" placeholder="Full name" required>
          </div>
          <div class="col-md-6 mb-2">
            <input name="phone" class="form-control" placeholder="Mobile number" required>
          </div>
          <div class="col-12 mb-2">
            <input name="address_line1" class="form-control" placeholder="Address line 1" required>
          </div>
          <div class="col-md-6 mb-2">
            <input name="area" class="form-control" placeholder="Area / Landmark" required>
          </div>
          <div class="col-md-3 mb-2">
            <input name="city" class="form-control" value="<?php echo h(ORVIGO_DEFAULT_CITY); ?>" required>
          </div>
          <div class="col-md-3 mb-2">
            <input name="pincode" class="form-control" placeholder="Pincode" required>
          </div>
        </div>

        <div class="form-check my-2">
          <input class="form-check-input" type="radio" name="payment_mode" value="cash_after_service" id="pay-cash" checked>
          <label class="form-check-label" for="pay-cash">Pay after service (Cash/Card/UPI to technician)</label>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="radio" name="payment_mode" value="online" id="pay-online">
          <label class="form-check-label" for="pay-online">Pay online now (Razorpay)</label>
        </div>

        <div class="mb-3">
          <small class="text-muted">Pricing is confirmed after diagnosis. Transparent pricing & no surprise charges.</small>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Confirm Booking</button>
          <a class="btn btn-outline-secondary" href="services.php">Back to Services</a>
        </div>
      </form>
    </div>

    <div class="col-lg-4" data-aos="fade-up">
      <div class="sticky-top pt-5">
        <div class="card shadow-sm">
          <div class="card-body">
            <h6 class="card-title">Booking Summary</h6>
            <p class="small text-muted">Selected options will appear here. Estimated visit charge and final amount will be confirmed after diagnosis.</p>
            <ul class="list-unstyled small" id="booking-summary"></ul>
            <div class="mt-3">
              <strong>Payment:</strong> <span id="selected-payment">Pay after service</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/_footer.php'; ?>
