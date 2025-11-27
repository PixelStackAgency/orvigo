// Frontend Configuration
// This file is loaded by all pages to set the API base URL

(function() {
  // Default to localhost for development
  // Override by setting window.ORVIGO.API_BASE before this script loads
  
  if (!window.ORVIGO) {
    window.ORVIGO = {};
  }
  
  // Set API base from environment variable or local storage or default
  if (!window.ORVIGO.API_BASE) {
    // Try to get from URL parameters (useful for testing)
    const urlParams = new URLSearchParams(window.location.search);
    const apiFromUrl = urlParams.get('api_base');
    
    if (apiFromUrl) {
      window.ORVIGO.API_BASE = apiFromUrl;
      // Store in local storage for future requests
      localStorage.setItem('ORVIGO_API_BASE', apiFromUrl);
    } else if (localStorage.getItem('ORVIGO_API_BASE')) {
      // Use previously stored value
      window.ORVIGO.API_BASE = localStorage.getItem('ORVIGO_API_BASE');
    } else if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
      // Development: assume backend on same localhost
      window.ORVIGO.API_BASE = `http://localhost:8000`;
    } else {
      // Production: must be explicitly set or will fail
      // For Vercel + Render, set in Vercel environment or HTML
      window.ORVIGO.API_BASE = window.location.origin; // fallback
    }
  }
  
  // Razorpay key (will be set via meta tag or environment)
  if (!window.ORVIGO.RAZORPAY_KEY_ID) {
    const metaRzp = document.querySelector('meta[name="razorpay-key"]');
    if (metaRzp) {
      window.ORVIGO.RAZORPAY_KEY_ID = metaRzp.getAttribute('content');
    }
  }
  
  console.log('[Orvigo Config] API Base:', window.ORVIGO.API_BASE);
  console.log('[Orvigo Config] Razorpay Key:', window.ORVIGO.RAZORPAY_KEY_ID ? '✓ Set' : '✗ Not set');
})();
