// --- public/assets/js/main.js ---

// Helper to get API URL (matches validation.js)
function getApiUrl(endpoint) {
  const base = window.ORVIGO && window.ORVIGO.API_BASE ? window.ORVIGO.API_BASE : window.location.origin;
  return base + '/api/' + endpoint;
}

document.addEventListener('DOMContentLoaded', function(){
  // Quick booking redirect
  const qForm = document.getElementById('quick-booking-form');
  if(qForm){
    qForm.addEventListener('submit', function(e){
      e.preventDefault();
      const svc = document.getElementById('quick-service').value;
      // Map to page
      const map = {
        ac:'service-ac.php',
        refrigerator:'service-refrigerator.php',
        washing_machine:'service-washing-machine.php',
        tv:'service-tv.php',
        geyser:'service-geyser.php',
        microwave:'service-microwave.php',
        water_purifier:'service-water-purifier.php',
        mobile_tablet:'service-mobile-tablet.php'
      };
      const target = map[svc] || 'services.php';
      // pass area/pincode as query param
      const area = encodeURIComponent(document.getElementById('quick-area').value || '');
      window.location = target + (area ? '?area=' + area : '');
    });
  }

  // Track booking form
  const trackForm = document.getElementById('track-booking-form');
  if(trackForm){
    trackForm.addEventListener('submit', function(e){
      e.preventDefault();
      const id = document.getElementById('booking_id').value.trim();
      const phone = document.getElementById('phone').value.trim();
      fetch(getApiUrl('get-booking.php') + '?booking_id=' + encodeURIComponent(id) + '&phone=' + encodeURIComponent(phone))
        .then(r => r.json()).then(data => {
          const out = document.getElementById('booking-result');
          if(data.success){
            const b = data.booking;
            out.innerHTML = `<div class="card p-3"><h5>Booking ${b.booking_id}</h5><p>Status: ${b.status.status}</p><p>Service: ${b.service.category}</p><p>Date: ${b.schedule.preferred_date} ${b.schedule.time_slot}</p></div>`;
          } else {
            out.innerHTML = `<div class="alert alert-warning">${data.message || 'Booking not found'}</div>`;
          }
        }).catch(err=>console.error(err));
    });
  }
});
