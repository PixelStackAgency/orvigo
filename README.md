--- README.md ---
# Orvigo — Local Development & Testing

This README explains how to lint, run, and test the Orvigo prototype locally, including simulating booking + payment flows.

Prerequisites
- PHP 8+ installed and on PATH
- Internet access for CDN assets (Bootstrap, AOS, Razorpay script)

Quick start (local server)
```cmd
cd "C:\All In 1\Wajid Bhaw Project\Orvigo"
php -S localhost:8000 -t public
```
Open http://localhost:8000 in your browser.

Lint PHP files
```cmd
php -l src/config.php
php -l src/helpers.php
php -l api/book-service.php
php -l api/create-order.php
php -l api/confirm-payment.php
php -l api/get-booking.php
```
Or run a shell loop to lint all PHP files.

Simulate booking + payment (recommended for development)
1. Open home page and create a booking via a service page. Select "Pay online now" to exercise the online path.
2. If Razorpay keys are NOT set in `src/config.php` or `ORVIGO_RAZORPAY_*` env vars, the system will *simulate* order creation. `create-order.php` returns a simulated order id.
3. In simulated mode, the frontend attempts to open Razorpay Checkout. If you don't have a valid Razorpay key, you can skip actual checkout and call `api/confirm-payment.php` manually to mark the booking paid:

```cmd
curl -X POST "http://localhost:8000/api/confirm-payment.php" -H "Content-Type: application/json" -d "{\"booking_id\": \"ORVIGO-...\", \"razorpay_payment_id\": \"sim_pay_123\", \"razorpay_order_id\": \"sim_order_abc\", \"razorpay_signature\": \"sim\"}"
```

This will update `storage/bookings.json` setting `payment.status` to `success` and booking `status.status` to `confirmed`.

Testing with real Razorpay
- Set `RAZORPAY_KEY_ID` and `RAZORPAY_KEY_SECRET` in `src/config.php` or as environment variables.
- Set `RAZORPAY_WEBHOOK_SECRET` (from Razorpay Dashboard) and configure webhook URL to point to `api/payment-webhook.php`.
- Use real Razorpay Checkout integration — ensure `window.ORVIGO.RAZORPAY_KEY_ID` is populated by the site header.

Admin credentials (development)
- A hashed admin password may be provided via environment variable `ORVIGO_ADMIN_PASSWORD_HASH` (recommended). If not set, the default plaintext password in `src/config.php` will be hashed and used as a fallback. Change it immediately in production.

Honeypot & Rate limiting
- Basic honeypot field named `website` is included in public forms.
- Simple IP rate limiting is implemented and persisted in `logs/rate_limits.json`.

Where to add real integrations
- `src/SmsNotifier.php` — plug in SMS/WhatsApp provider credentials and HTTP calls.
- `src/EmailNotifier.php` — replace `mail()` with PHPMailer or transactional email provider.
- `src/PaymentGateway.php` — finished for Razorpay REST order creation; fill credentials.

Logs
- `logs/notifications.log` — notifications and payment logs
- `logs/rate_limits.json` — rate limiting data

If you want, I can:
- Run a PHP lint pass in the repo and fix any syntax issues automatically.
- Wire a local test flow that bypasses the Razorpay checkout for smoother integration tests.
