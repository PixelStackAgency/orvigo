// --- public/assets/js/validation.js ---
// Basic front-end helpers

// Helper to get API URL
function getApiUrl(endpoint) {
  const base = window.ORVIGO && window.ORVIGO.API_BASE ? window.ORVIGO.API_BASE : window.location.origin;
  return base + '/api/' + endpoint;
}

document.addEventListener('DOMContentLoaded', function(){
  // Hook service booking forms
  const serviceForm = document.getElementById('service-booking-form');
  if(serviceForm){
    serviceForm.addEventListener('submit', function(e){
      e.preventDefault();
      // simple validations
      const phone = serviceForm.querySelector('[name=phone]').value.trim();
      if(!/^[0-9+\-() ]{10,}$/.test(phone)){
        alert('Please enter a valid phone number');
        return;
      }
      // assemble payload
      const formData = new FormData(serviceForm);
      const payload = {};
      formData.forEach((v,k)=>{
        if(k.endsWith('[]')){ // not applicable
        }
        if(payload[k] !== undefined){
          if(!Array.isArray(payload[k])) payload[k] = [payload[k]];
          payload[k].push(v);
        } else payload[k] = v;
      });

      // Gather checkboxes
      payload.service_options = [];
      serviceForm.querySelectorAll('input[name="service_options[]"]:checked').forEach(ch=>payload.service_options.push(ch.value));

      // Send booking to server first
      fetch(getApiUrl('book-service.php'), {
        method:'POST',
        headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest','Content-Type':'application/json'},
        body: JSON.stringify(payload)
      }).then(r=>r.json()).then(async res=>{
        if(!res.success){ alert('Error: '+(res.message||'Could not create booking')); return; }
        const booking = res.booking;
        // If online payment selected, create order and open Razorpay Checkout
        if(payload.payment_mode === 'online'){
          try{
            const orderResp = await fetch(getApiUrl('create-order.php'), {method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({booking_id: booking.booking_id, amountPaise: 10000})});
            const orderJson = await orderResp.json();
            if(!orderJson.success){ alert('Payment init failed'); return; }
            const order = orderJson.order;
            const options = {
              key: window.ORVIGO && window.ORVIGO.RAZORPAY_KEY_ID ? window.ORVIGO.RAZORPAY_KEY_ID : '',
              amount: order.amount,
              currency: order.currency || 'INR',
              name: 'Orvigo',
              description: booking.service.category || 'Service',
              order_id: order.id,
              handler: function (response){
                // Confirm payment with server
                fetch(getApiUrl('confirm-payment.php'), {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({booking_id: booking.booking_id, razorpay_payment_id: response.razorpay_payment_id, razorpay_order_id: response.razorpay_order_id, razorpay_signature: response.razorpay_signature})}).then(r=>r.json()).then(j=>{
                  if(j.success){ alert('Payment success and booking confirmed.'); window.location='my-bookings.php'; } else { alert('Payment verification failed.'); }
                });
              },
              prefill: {name: booking.customer.name, contact: booking.customer.phone, email: booking.customer.email || ''},
              notes: {booking_id: booking.booking_id}
            };
            // Fallback if Razorpay script not loaded
            if (typeof Razorpay === 'undefined'){
              alert('Payment cannot be processed: payment library missing.');
              return;
            }
            const rzp = new Razorpay(options);
            rzp.open();
          } catch(err){ console.error(err); alert('Payment initiation failed'); }
        } else {
          alert('Booking created: ' + booking.booking_id + '. Confirmation sent via SMS/Email (placeholder).');
          window.location = 'my-bookings.php?';
        }
      }).catch(err=>{console.error(err); alert('Network error');});
    });
  }
});
