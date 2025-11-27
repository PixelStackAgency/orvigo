--- README.md ---
# Orvigo — Local Development & Deployment

**Orvigo** is a complete, production-ready service booking platform for home repair and maintenance services. This README explains local setup, testing, and deployment.

## Quick Links
- **🚀 Deployment Guide**: See [`DEPLOYMENT_GUIDE.md`](DEPLOYMENT_GUIDE.md) for step-by-step Render + Vercel + Razorpay setup
- **💳 Razorpay Setup**: See [`RAZORPAY_SETUP.md`](RAZORPAY_SETUP.md) for Razorpay configuration and test cards
- **📧 Production Notifiers**: See `src/notifiers/SendGridEmailNotifier.php` and `src/notifiers/TwilioSmsNotifier.php`

## Prerequisites (Local Development)
- PHP 8+ installed and on PATH
- Git configured (for pushing to GitHub)
- Internet access for CDN assets (Bootstrap, AOS, Razorpay script)

## Quick Start (Local Server)
```cmd
cd "C:\All In 1\Wajid Bhaw Project\Orvigo"
php -S localhost:8000 -t public
```
Open http://localhost:8000 in your browser.

## Lint PHP Files
```cmd
php -l src/config.php
php -l src/helpers.php
php -l src/BookingStore.php
php -l src/PaymentGateway.php
php -l api/book-service.php
php -l api/create-order.php
php -l api/confirm-payment.php
php -l api/get-booking.php
```
Or use GitHub Actions CI (`.github/workflows/php-lint.yml`) which runs on every push.

## Local Testing: Simulate Booking + Payment
1. Open http://localhost:8000 and create a booking via any service page
2. Select "Pay online now"
3. If Razorpay keys are NOT set in environment variables, the system will *simulate* order creation
4. Manually confirm payment by calling:
```cmd
curl -X POST "http://localhost:8000/api/confirm-payment.php" -H "Content-Type: application/json" -d "{\"booking_id\": \"ORVIGO-...\", \"razorpay_payment_id\": \"sim_pay_123\", \"razorpay_order_id\": \"sim_order_abc\", \"razorpay_signature\": \"sim\"}"
```
This updates `storage/bookings.json` and marks the booking as confirmed.

## Testing with Real Razorpay (Production)
See [`RAZORPAY_SETUP.md`](RAZORPAY_SETUP.md) for:
- Test API keys and test card numbers
- Webhook configuration and payload examples
- Switching between test and live modes

## Admin Credentials
- Default plaintext password in `src/config.php`: `admin123` (change immediately in production!)
- Generate a bcrypt hash for production using:
```cmd
php gen_hash.php "your-secure-password"
```
- Set the hash via environment variable `ORVIGO_ADMIN_PASSWORD_HASH` (recommended for Render/production)

## Email & SMS Notifications
Production-ready implementations available:
- **`src/notifiers/SendGridEmailNotifier.php`** — SendGrid integration for booking confirmations
- **`src/notifiers/TwilioSmsNotifier.php`** — Twilio integration for SMS and WhatsApp notifications

To enable:
1. Obtain API credentials (SendGrid API key or Twilio account)
2. Set environment variables in your deployment (see `DEPLOYMENT_GUIDE.md` Part 4)
3. Replace placeholder notifiers in `src/config.php`

## Security Features
- **CSRF Protection**: All forms include CSRF tokens
- **Honeypot**: Bot-detection field (`website`) in booking and contact forms
- **Rate Limiting**: IP-based rate limiting persisted in `logs/rate_limits.json`
- **Input Sanitization**: All user inputs validated and sanitized
- **Password Hashing**: Admin credentials hashed with bcrypt

## Deployment to Production
**For live deployment on Render with optional Vercel frontend:**

1. **[Render Backend Setup](DEPLOYMENT_GUIDE.md#part-1-deploy-backend-on-render)** 
   - Create Render Web Service connected to GitHub
   - Configure environment secrets (admin hash, Razorpay keys, email/SMS credentials)
   - Deploy with included Dockerfile

2. **[Razorpay Configuration](RAZORPAY_SETUP.md)**
   - Get test API keys from Razorpay Dashboard
   - Configure webhook URL in Razorpay settings
   - Use provided test card numbers for payment testing

3. **[Email/SMS Setup](DEPLOYMENT_GUIDE.md#part-4-configure-email--sms-notifications)** (Optional)
   - Choose SendGrid for email or Twilio for SMS/WhatsApp
   - Set provider credentials in Render environment

4. **[Testing & Validation](DEPLOYMENT_GUIDE.md#part-5-testing--validation)**
   - Test end-to-end booking → payment → notification flow
   - Monitor logs in Render dashboard

**See [`DEPLOYMENT_GUIDE.md`](DEPLOYMENT_GUIDE.md) for complete step-by-step instructions.**

## Project Structure
```
Orvigo/
├── public/                     # Frontend (HTML, CSS, JS)
│   ├── index.php              # Home page
│   ├── services.php           # Service listing
│   ├── service-*.php          # Individual service pages
│   ├── contact.php            # Contact form
│   ├── admin-login.php        # Admin login
│   ├── admin-dashboard.php    # Admin dashboard
│   └── assets/
│       ├── css/               # Styling
│       └── js/                # Client-side validation & API calls
├── api/                        # REST API endpoints
│   ├── book-service.php       # Create booking
│   ├── create-order.php       # Razorpay order creation
│   ├── confirm-payment.php    # Payment confirmation
│   ├── payment-webhook.php    # Razorpay webhook receiver
│   ├── get-booking.php        # Fetch booking details
│   ├── admin-update-booking.php  # Admin booking updates
│   └── contact-submit.php     # Contact form handler
├── src/                        # Backend classes & utilities
│   ├── config.php             # Configuration & env loading
│   ├── helpers.php            # CSRF, sanitization, validators, rate limiting
│   ├── BookingStore.php       # JSON CRUD with file locking
│   ├── PaymentGateway.php     # Razorpay integration
│   ├── SmsNotifier.php        # SMS placeholder (implement with Twilio)
│   ├── EmailNotifier.php      # Email placeholder (implement with SendGrid)
│   └── notifiers/
│       ├── SendGridEmailNotifier.php    # Production SendGrid implementation
│       └── TwilioSmsNotifier.php        # Production Twilio implementation
├── storage/                    # JSON data files (persistent)
│   └── bookings.json          # All bookings with payment status
├── logs/                       # Log files
│   ├── notifications.log      # Booking confirmations, errors
│   └── rate_limits.json       # IP rate limiting data
├── tools/                      # Utilities
│   └── auto_fix.php           # Auto-formatting for PHP files
├── DEPLOYMENT_GUIDE.md        # ⭐ Step-by-step Render/Vercel/Razorpay setup
├── RAZORPAY_SETUP.md          # ⭐ Razorpay configuration guide
├── Dockerfile                 # Docker image for production
├── render.yaml                # Render service configuration
├── .github/workflows/php-lint.yml  # CI/CD GitHub Actions linting
├── .gitignore                 # Git exclusions
└── .env.example               # Environment variable template
```
