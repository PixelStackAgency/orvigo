<?php
// --- public/services.php ---
require_once __DIR__ . '/../src/helpers.php';
include __DIR__ . '/_header.php';
?>

<section class="container mt-5">
  <div class="d-flex justify-content-between align-items-center">
    <h1>All Services</h1>
    <div>
      <a href="#book" class="btn btn-primary">Book Service</a>
    </div>
  </div>

  <div class="mt-4">
    <!-- Simple filter bar -->
    <div class="row g-2 align-items-center">
      <div class="col-md-4">
        <select id="filter-category" class="form-select">
          <option value="all">All categories</option>
          <option value="cooling">Cooling (AC)</option>
          <option value="laundry">Laundry</option>
          <option value="kitchen">Kitchen</option>
          <option value="entertainment">Entertainment</option>
          <option value="mobile">Mobile/Tablet</option>
        </select>
      </div>
    </div>
  </div>

  <div class="row g-3 mt-3" id="services-grid">
    <?php
    // Reuse list from index
    $services = [
      ['key'=>'ac','title'=>'AC Service & Repair','img_url'=>'https://images.unsplash.com/photo-1581579182294-0b9f6b1b5b62?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=1','desc'=>'Split/window AC servicing, gas refill, installation.','tags'=>['Doorstep','Same-day']],
      ['key'=>'refrigerator','title'=>'Refrigerator Repair','img_url'=>'https://images.unsplash.com/photo-1582719478172-4f6a9a7a9f1f?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=2','desc'=>'Cooling issues, water leakage, compressor.','tags'=>['Doorstep','Expert Techs']],
      ['key'=>'washing_machine','title'=>'Washing Machine Repair','img_url'=>'https://images.unsplash.com/photo-1592928307326-0e4f7a2b8fb5?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=3','desc'=>'Full service & repairs.','tags'=>['Doorstep','Installation']],
      ['key'=>'tv','title'=>'Television Repair','img_url'=>'https://images.unsplash.com/photo-1503602642458-232111445657?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=4','desc'=>'Display, sound & power issues.','tags'=>['Doorstep']],
      ['key'=>'geyser','title'=>'Geyser / Water Heater','img_url'=>'https://images.unsplash.com/photo-1600651226270-2d6a2f2e2d7b?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=5','desc'=>'Heating problems & installation.','tags'=>['Doorstep']],
      ['key'=>'microwave','title'=>'Microwave Oven Repair','img_url'=>'https://images.unsplash.com/photo-1601050690592-4b5b1836f9f3?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=6','desc'=>'Not heating, sparking issues.','tags'=>['Doorstep']],
      ['key'=>'water_purifier','title'=>'Water Purifier Service','img_url'=>'https://images.unsplash.com/photo-1598511722647-7b777c2e2c2a?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=7','desc'=>'Filter change & maintenance.','tags'=>['Doorstep']],
      ['key'=>'mobile_tablet','title'=>'Mobile & Tablet Repair','img_url'=>'https://images.unsplash.com/photo-1510557880182-3d4d3d7f6b8e?q=80&w=800&auto=format&fit=crop&ixlib=rb-4.0.3&s=8','desc'=>'Screen, battery & charging repairs.','tags'=>['Doorstep','Pickup']]
    ];

    foreach ($services as $s): ?>
      <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up">
        <div class="card h-100 shadow-sm">
          <div class="row g-0">
            <div class="col-4 d-flex align-items-center justify-content-center p-3">
              <img src="<?php echo h($s['img_url'] ?? 'assets/img/' . ($s['img'] ?? 'hero-1.svg')); ?>" alt="<?php echo h($s['title']); ?>" class="img-fluid" loading="lazy">
            </div>
            <div class="col-8">
              <div class="card-body d-flex flex-column h-100">
                <h5 class="card-title"><?php echo h($s['title']); ?></h5>
                <p class="small text-muted"><?php echo h($s['desc']); ?></p>
                <div class="mb-2">
                  <?php foreach ($s['tags'] as $tag): ?>
                    <span class="badge bg-light text-dark me-1"><?php echo h($tag); ?></span>
                  <?php endforeach; ?>
                </div>
                <div class="mt-auto d-flex gap-2">
                  <a href="service-<?php echo $s['key']; ?>.php" class="btn btn-outline-primary btn-sm">View Details</a>
                  <a href="service-<?php echo $s['key']; ?>.php" class="btn btn-primary btn-sm">Book Now</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include __DIR__ . '/_footer.php'; ?>
