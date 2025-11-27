<?php
// --- public/index.php ---
require_once __DIR__ . '/../src/helpers.php';
include __DIR__ . '/_header.php';
?>

<!-- HERO -->
<section class="bg-light pt-5 pb-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6" data-aos="fade-up">
        <h1 class="display-6 fw-bold">Fast, trusted home-appliance & electronics repair at your doorstep.</h1>
        <p class="lead text-muted">We bring trained technicians to your home for same-day, affordable service across <?php echo h(ORVIGO_DEFAULT_CITY); ?>.</p>
        <a href="services.php#book" class="btn btn-primary btn-lg me-2">Book a Service</a>
        <a href="contact.php" class="btn btn-outline-secondary btn-lg">Contact Us</a>
      </div>
      <div class="col-lg-6 text-center" data-aos="fade-left">
        <img src="assets/img/hero-1.svg" alt="Technician servicing appliance" class="img-fluid" loading="lazy">
      </div>
    </div>

    <!-- Quick booking widget -->
    <div class="card mt-4 shadow-sm" data-aos="fade-up">
      <div class="card-body">
        <form id="quick-booking-form" class="row g-2 align-items-end">
          <div class="col-md-5">
            <label class="form-label">Select Appliance</label>
            <select id="quick-service" class="form-select">
              <option value="ac">AC Service & Repair</option>
              <option value="refrigerator">Refrigerator Repair</option>
              <option value="washing_machine">Washing Machine Repair</option>
              <option value="tv">Television Repair</option>
              <option value="geyser">Geyser / Water Heater Repair</option>
              <option value="microwave">Microwave Oven Repair</option>
              <option value="water_purifier">Water Purifier Service</option>
              <option value="mobile_tablet">Mobile & Tablet Repair</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Pincode / Area</label>
            <input id="quick-area" class="form-control" placeholder="560001 or 'HSR Layout'">
          </div>
          <div class="col-md-3">
            <button class="btn btn-primary w-100" id="quick-book-btn">Next</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Services grid -->
<section class="container mt-5">
  <h2 class="mb-4" data-aos="fade-up">Appliance Service & Repair</h2>
  <div class="row g-3">
    <?php
    $services = [
      ['key'=>'ac','title'=>'AC Service & Repair','img_url'=>'https://images.unsplash.com/photo-1581579182294-0b9f6b1b5b62?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=1','desc'=>'Split/window AC servicing, gas refills, installation.'],
      ['key'=>'refrigerator','title'=>'Refrigerator Repair','img_url'=>'https://images.unsplash.com/photo-1582719478172-4f6a9a7a9f1f?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=2','desc'=>'Cooling issues, water leakage, compressor troubles.'],
      ['key'=>'washing_machine','title'=>'Washing Machine Repair','img_url'=>'https://images.unsplash.com/photo-1592928307326-0e4f7a2b8fb5?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=3','desc'=>'Full diagnosis, repair & maintenance.'],
      ['key'=>'tv','title'=>'Television Repair','img_url'=>'https://images.unsplash.com/photo-1503602642458-232111445657?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=4','desc'=>'Display, sound and power issues.'],
      ['key'=>'geyser','title'=>'Geyser / Water Heater','img_url'=>'https://images.unsplash.com/photo-1600651226270-2d6a2f2e2d7b?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=5','desc'=>'Heating problems, leakage & installation.'],
      ['key'=>'microwave','title'=>'Microwave Oven Repair','img_url'=>'https://images.unsplash.com/photo-1601050690592-4b5b1836f9f3?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=6','desc'=>'Not heating, sparking, turntable issues.'],
      ['key'=>'water_purifier','title'=>'Water Purifier Service','img_url'=>'https://images.unsplash.com/photo-1598511722647-7b777c2e2c2a?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=7','desc'=>'Filter change, low flow, leakage.' ],
      ['key'=>'mobile_tablet','title'=>'Mobile & Tablet Repair','img_url'=>'https://images.unsplash.com/photo-1510557880182-3d4d3d7f6b8e?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=8','desc'=>'Screen, battery, charging and more.'],
    ];

    foreach ($services as $s):
    ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3" data-aos="fade-up">
        <div class="card h-100 shadow-sm">
          <img src="<?php echo h($s['img_url'] ?? 'assets/img/' . ($s['img'] ?? 'hero-1.svg')); ?>" class="card-img-top" alt="<?php echo h($s['title']); ?>" loading="lazy">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?php echo h($s['title']); ?></h5>
            <p class="text-muted small mb-3"><?php echo h($s['desc']); ?></p>
            <div class="mt-auto d-flex gap-2">
              <a href="service-<?php echo $s['key']; ?>.php" class="btn btn-outline-primary btn-sm">View Details</a>
              <a href="service-<?php echo $s['key']; ?>.php" class="btn btn-primary btn-sm">Book Now</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Why Orvigo / How it Works / Testimonials (short) -->
<section class="container mt-5" data-aos="fade-up">
  <div class="row">
    <div class="col-md-8">
      <h3>Why Orvigo?</h3>
      <div class="row g-3 mt-3">
        <div class="col-sm-6">
          <div class="p-3 border rounded"> <strong>Verified Techs</strong><p class="small text-muted">Trained, background-checked technicians.</p></div>
        </div>
        <div class="col-sm-6">
          <div class="p-3 border rounded"> <strong>Doorstep Convenience</strong><p class="small text-muted">We come to your home — no service centre visits.</p></div>
        </div>
        <div class="col-sm-6">
          <div class="p-3 border rounded"> <strong>Transparent Process</strong><p class="small text-muted">Pricing confirmed after diagnosis; no surprises.</p></div>
        </div>
        <div class="col-sm-6">
          <div class="p-3 border rounded"> <strong>Service Guarantee</strong><p class="small text-muted">Customer satisfaction & service warranty.</p></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <h5>Quick Stats</h5>
      <ul class="list-unstyled small text-muted">
        <li>⭐ 4.8/5 average rating (placeholder)</li>
        <li>🔧 10,000+ repairs handled (placeholder)</li>
        <li>🚚 Same-day service available in many areas</li>
      </ul>
    </div>
  </div>
</section>

<?php include __DIR__ . '/_footer.php'; ?>
