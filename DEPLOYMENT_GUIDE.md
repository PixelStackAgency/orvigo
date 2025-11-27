# Orvigo Website — Complete Deployment Guide

This guide walks you through deploying the Orvigo website on Render (backend) + Vercel (optional frontend) with Razorpay payments, email/SMS notifications, and end-to-end testing.

---

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Prerequisites](#prerequisites)
3. [Part 1: Deploy Backend on Render](#part-1-deploy-backend-on-render)
4. [Part 2: Deploy Frontend on Vercel (Optional)](#part-2-deploy-frontend-on-vercel-optional)
5. [Part 3: Configure Razorpay](#part-3-configure-razorpay)
6. [Part 4: Setup Email & SMS Notifications](#part-4-setup-email--sms-notifications)
7. [Part 5: Testing & Validation](#part-5-testing--validation)
8. [Troubleshooting](#troubleshooting)

---

## Architecture Overview

### Recommended Setup

```
┌─────────────────────────────────────────────────────────────┐
│                      End Users                              │
│                    (Web Browsers)                           │
└─────────────────────────────────────────────────────────────┘
           │                                   │
           ▼                                   ▼
    ┌──────────────┐                  ┌──────────────────┐
    │   Vercel     │                  │   Render (PHP)   │
    │  (Frontend)  │◄─────────────────┤   (Backend API)  │
    │  Static HTML │   API calls      │   + Database     │
    │  CSS/JS/SVG  │                  │   + Webhooks     │
    └──────────────┘                  └──────────────────┘
                                            │
                                            ▼
                                    ┌────────────────────┐
                                    │  External Services │
                                    │  ├─ Razorpay       │
                                    │  ├─ SendGrid/SES   │
                                    │  └─ Twilio         │
                                    └────────────────────┘
```

### Alternative (Single Host)
- Deploy both backend and frontend on Render using the Dockerfile (simpler, no Vercel needed).

---

## Prerequisites

Before you start, ensure you have:

1. **GitHub Repository**: Your code pushed to `https://github.com/PixelStackAgency/orvigo` (or your account).
2. **Render Account**: Sign up at [https://render.com](https://render.com).
3. **Vercel Account** (optional): Sign up at [https://vercel.com](https://vercel.com).
4. **Razorpay Account**: Sign up at [https://razorpay.com](https://razorpay.com) (test keys available immediately).
5. **Email Provider Account** (one of):
   - SendGrid (free tier available)
   - AWS SES
   - Mailgun
6. **SMS Provider Account** (optional, one of):
   - Twilio (free trial credits)
   - MSG91
7. **PHP >= 8.0** and **cURL enabled** (Render Docker provides this).

---

## Part 1: Deploy Backend on Render

### Step 1.1: Create a New Render Web Service

1. Log in to [Render Dashboard](https://dashboard.render.com).
2. Click **New** → **Web Service**.
3. Select **Connect a repository** (GitHub).
4. Authorize Render to access your GitHub account.
5. Select the repository `PixelStackAgency/orvigo` (or your fork).
6. Click **Connect**.

### Step 1.2: Configure the Render Service

**Service Name**: `orvigo-api` (or any unique name)

**Environment**: Docker (since we have a `Dockerfile`)

**Build Command**: (Leave empty — Docker will use the `Dockerfile`)

**Start Command**: (Leave empty — Dockerfile specifies this)

**Branch**: `main`

**Auto-Deploy**: Check "Auto-deploy on push" to deploy on every git push.

### Step 1.3: Set Environment Variables

In the Render dashboard, navigate to **Environment** (in the service settings) and add the following secrets:

| Variable Name | Example Value | Notes |
|---|---|---|
| `ORVIGO_ADMIN_PASSWORD_HASH` | `$2y$10$...` | Generate with `php gen_hash.php "YourPassword"` |
| `RAZORPAY_KEY_ID` | `rzp_test_XXXXXXXXXXXXXXXX` | Get from Razorpay dashboard (test key) |
| `RAZORPAY_KEY_SECRET` | `XXXXXXXXXXXXXXXXXXXXXXXX` | Get from Razorpay dashboard (test secret) |
| `RAZORPAY_WEBHOOK_SECRET` | `webhook_secret_xxx` | Generate in Razorpay webhook settings (see Part 3) |
| `ORVIGO_ADMIN_EMAIL` | `admin@yourdomain.com` | Email to receive contact form submissions |
| `SENDGRID_API_KEY` | (optional) | If using SendGrid for email |
| `TWILIO_ACCOUNT_SID` | (optional) | If using Twilio for SMS |
| `TWILIO_AUTH_TOKEN` | (optional) | If using Twilio for SMS |

**To add a secret:**
1. Click **Environment** in the service sidebar.
2. Click **Add Environment Variable**.
3. Enter **Key** and **Value**.
4. Mark as "Secret" if sensitive (so it won't be visible in logs).
5. Click **Save**.

### Step 1.4: Configure Persistent Storage (Important!)

By default, Render's Docker filesystem is ephemeral (cleared on redeploy). To persist bookings and logs:

**Option A: Use Render Disk** (Recommended for small projects)
1. Go to **Disks** in the service settings.
2. Click **Add Disk**.
3. **Mount Path**: `/app/storage` (matches the Dockerfile `WORKDIR /app` + `storage/` directory).
4. **Size**: 1 GB (adjust as needed).
5. Repeat for **Mount Path**: `/app/logs` (1 GB).

**Option B: Use an External Database** (Recommended for production)
- Switch from JSON storage to SQLite or PostgreSQL. Instructions for this are in the optional "Advanced: Database Migration" section below.

### Step 1.5: Deploy

1. Click **Create Web Service**.
2. Render will start building the Docker image.
3. Monitor the **Logs** tab for build/startup messages.
4. Once deployed, you'll see a public URL (e.g., `https://orvigo-api.onrender.com`).
5. Test the API by visiting `https://orvigo-api.onrender.com/public/index.php` (you should see the homepage).

### Step 1.6: Verify Render Deployment

Once deployed, test:

```bash
# Check homepage loads
curl https://orvigo-api.onrender.com/public/index.php | grep -q "Orvigo" && echo "✓ Homepage OK"

# Check admin login page
curl https://orvigo-api.onrender.com/public/admin/login.php | grep -q "Admin Login" && echo "✓ Admin page OK"

# Test booking API (will create a booking)
curl -X POST https://orvigo-api.onrender.com/api/book-service.php \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "phone": "9876543210",
    "address_line1": "123 Main St",
    "service_category": "ac",
    "preferred_date": "2025-12-10",
    "time_slot": "10:00 AM - 12:00 PM"
  }' | grep -q "success" && echo "✓ Booking API OK"
```

---

## Part 2: Deploy Frontend on Vercel (Optional)

If you want to serve the frontend from Vercel and API from Render:

### Step 2.1: Create a Vercel Project

1. Log in to [Vercel Dashboard](https://vercel.com/dashboard).
2. Click **Add New** → **Project**.
3. Select **Import Git Repository**.
4. Choose `PixelStackAgency/orvigo` repository.
5. Click **Import**.

### Step 2.2: Configure Vercel Build

**Project Name**: `orvigo-web` (or any name)

**Framework Preset**: Other (no specific framework; we're serving static files)

**Root Directory**: `public`

**Build Command**: Leave empty (no build needed for static files)

**Output Directory**: `.`

**Environment Variables**: Add `VITE_API_BASE_URL` = `https://orvigo-api.onrender.com` (use your Render service URL)

### Step 2.3: Update Frontend to Use Render API

Edit `public/assets/js/main.js` to use the Render backend URL for API calls:

```javascript
// At the top of main.js, add:
const ORVIGO_API_BASE = window.ORVIGO_API_BASE || 'https://orvigo-api.onrender.com';

// Update fetch calls:
const response = await fetch(ORVIGO_API_BASE + '/api/book-service.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(formData)
});
```

### Step 2.4: Deploy

1. Click **Deploy** in Vercel.
2. Wait for build to complete.
3. You'll get a Vercel URL (e.g., `https://orvigo-web.vercel.app`).
4. Test by visiting the URL in your browser.

### Step 2.5: Configure CORS on Render (if needed)

If Vercel frontend and Render backend are on different domains, add CORS headers to `src/helpers.php`:

```php
// Add after session_start() in src/helpers.php:
header('Access-Control-Allow-Origin: https://orvigo-web.vercel.app');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
```

Or set it dynamically from an environment variable:

```php
$allowed_origins = [
    'https://orvigo-web.vercel.app',
    'https://orvigo-api.onrender.com',
];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}
```

---

## Part 3: Configure Razorpay

### Step 3.1: Get Razorpay Test Keys

1. Log in to [Razorpay Dashboard](https://dashboard.razorpay.com/).
2. Navigate to **Settings** → **API Keys**.
3. Copy **Key ID** and **Key Secret** from the **Test Mode** section.

### Step 3.2: Add Keys to Render

In Render environment variables (from Part 1.3):
- `RAZORPAY_KEY_ID` = `rzp_test_XXXXXXXXXXXXXXXX`
- `RAZORPAY_KEY_SECRET` = `XXXXXXXXXXXXXXXXXXXXXXXX`

### Step 3.3: Generate and Add Webhook Secret

In Razorpay Dashboard:
1. Go to **Settings** → **Webhooks**.
2. Click **Add New Webhook**.
3. **Webhook URL**: `https://orvigo-api.onrender.com/api/payment-webhook.php`
4. **Events**: Select all payment-related events (payment.authorized, payment.failed, payment.completed).
5. Copy the **Webhook Secret** (generated automatically).
6. Add to Render as `RAZORPAY_WEBHOOK_SECRET`.

### Step 3.4: Test Razorpay Integration

1. Deploy an update to Render (commit a small change and push to trigger redeploy).
2. Navigate to the booking form on your live site.
3. Fill in booking details and click "Proceed to Payment".
4. Use Razorpay test card: **4111 1111 1111 1111** (any future expiry, any CVV).
5. Complete the payment.
6. Check Render logs to see webhook callback logged.

**Razorpay Test Cards:**
- Success: 4111 1111 1111 1111
- Failure: 4000 0000 0000 0002

---

## Part 4: Setup Email & SMS Notifications

### Option A: SendGrid (Recommended for Email)

#### Install SendGrid PHP Library

Update `Dockerfile` to include sendgrid:

```dockerfile
# In Dockerfile, modify composer install line:
RUN composer require sendgrid/sendgrid php-http/guzzle7-adapter
```

Or manually add to a `composer.json` in the project root and commit it. Render will detect and install.

#### Update EmailNotifier

Replace `src/EmailNotifier.php` with SendGrid implementation:

```php
<?php
// src/EmailNotifier.php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

class EmailNotifier
{
    private $sendgridKey;

    public function __construct()
    {
        $this->sendgridKey = getenv('SENDGRID_API_KEY') ?: '';
    }

    public function sendBookingEmail(string $email, array $booking): bool
    {
        if (empty($this->sendgridKey)) {
            log_message('notifications.log', 'SendGrid API key not configured; skipping booking email to ' . $email);
            return false;
        }

        $subject = 'Booking Confirmation - Orvigo';
        $bookingId = $booking['booking_id'] ?? 'N/A';
        $serviceName = $booking['service']['category'] ?? 'Service';
        $preferredDate = $booking['schedule']['preferred_date'] ?? 'N/A';

        $htmlContent = <<<HTML
<h2>Booking Confirmation</h2>
<p>Thank you for booking with Orvigo!</p>
<p><strong>Booking ID:</strong> $bookingId</p>
<p><strong>Service:</strong> $serviceName</p>
<p><strong>Scheduled Date:</strong> $preferredDate</p>
<p>Our technician will arrive at your doorstep. You will receive a call shortly.</p>
<p>Questions? Contact us at +91-90000-00000</p>
HTML;

        try {
            $email_obj = new \SendGrid\Mail\Mail();
            $email_obj->setFrom(ORVIGO_ADMIN_EMAIL, 'Orvigo');
            $email_obj->setSubject($subject);
            $email_obj->addTo($email, 'Customer');
            $email_obj->addContent('text/html', $htmlContent);

            $sendgrid = new \SendGrid\SendGrid($this->sendgridKey);
            $response = $sendgrid->send($email_obj);

            if ($response->statusCode() === 202) {
                log_message('notifications.log', 'Booking email sent to ' . $email);
                return true;
            } else {
                log_message('notifications.log', 'SendGrid error: ' . $response->statusCode() . ' - ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            log_message('notifications.log', 'Email send failed: ' . $e->getMessage());
            return false;
        }
    }
}
?>
```

#### Get SendGrid API Key

1. Sign up at [SendGrid](https://sendgrid.com) (free tier: 100 emails/day).
2. Go to **Settings** → **API Keys**.
3. Click **Create API Key** and copy the key.
4. Add to Render as `SENDGRID_API_KEY`.

### Option B: Twilio (Recommended for SMS)

#### Install Twilio PHP SDK

Update `Dockerfile`:

```dockerfile
RUN composer require twilio/sdk
```

#### Update SmsNotifier

Replace `src/SmsNotifier.php` with Twilio implementation:

```php
<?php
// src/SmsNotifier.php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

use Twilio\Rest\Client;

class SmsNotifier
{
    private $twilioSid;
    private $twilioToken;
    private $twilioPhone;

    public function __construct()
    {
        $this->twilioSid = getenv('TWILIO_ACCOUNT_SID') ?: '';
        $this->twilioToken = getenv('TWILIO_AUTH_TOKEN') ?: '';
        $this->twilioPhone = getenv('TWILIO_PHONE_NUMBER') ?: '';
    }

    public function sendSmsConfirmation(string $phone, array $booking): bool
    {
        if (empty($this->twilioSid) || empty($this->twilioToken) || empty($this->twilioPhone)) {
            log_message('notifications.log', 'Twilio credentials not configured; skipping SMS to ' . $phone);
            return false;
        }

        try {
            $client = new Client($this->twilioSid, $this->twilioToken);
            $bookingId = $booking['booking_id'] ?? 'N/A';
            $message = "Orvigo: Your service booking (ID: $bookingId) is confirmed. Our technician will arrive soon. Reply CANCEL to cancel.";

            $sms = $client->messages->create(
                '+91' . $phone,
                ['from' => $this->twilioPhone, 'body' => $message]
            );

            log_message('notifications.log', 'SMS sent to ' . $phone . ' (SID: ' . $sms->sid . ')');
            return true;
        } catch (\Exception $e) {
            log_message('notifications.log', 'SMS send failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendWhatsappConfirmation(string $phone, array $booking): bool
    {
        if (empty($this->twilioSid) || empty($this->twilioToken) || empty($this->twilioPhone)) {
            log_message('notifications.log', 'Twilio WhatsApp not configured; skipping to ' . $phone);
            return false;
        }

        try {
            $client = new Client($this->twilioSid, $this->twilioToken);
            $bookingId = $booking['booking_id'] ?? 'N/A';
            $message = "Orvigo: Your service booking (ID: $bookingId) is confirmed! ✓ Our technician will arrive soon.";

            $msg = $client->messages->create(
                'whatsapp:+91' . $phone,
                ['from' => 'whatsapp:' . $this->twilioPhone, 'body' => $message]
            );

            log_message('notifications.log', 'WhatsApp sent to ' . $phone . ' (SID: ' . $msg->sid . ')');
            return true;
        } catch (\Exception $e) {
            log_message('notifications.log', 'WhatsApp send failed: ' . $e->getMessage());
            return false;
        }
    }
}
?>
```

#### Get Twilio Credentials

1. Sign up at [Twilio](https://www.twilio.com/try-twilio) (free trial: $15 credits).
2. Go to **Account** → **API Keys & Tokens**.
3. Copy **Account SID** and **Auth Token**.
4. Get a Twilio phone number from **Phone Numbers** → **Manage Numbers**.
5. Add to Render:
   - `TWILIO_ACCOUNT_SID`
   - `TWILIO_AUTH_TOKEN`
   - `TWILIO_PHONE_NUMBER`

---

## Part 5: Testing & Validation

### 5.1: Test Booking Flow

1. **Visit Homepage**: `https://orvigo-api.onrender.com/public/index.php` or `https://orvigo-web.vercel.app`
2. **Fill Booking Form**:
   - Name: "Test User"
   - Phone: "9876543210"
   - Address: "123 Main St, Bangalore"
   - Service: "AC Service & Repair"
   - Preferred Date: (any future date)
   - Time Slot: "10:00 AM - 12:00 PM"
3. **Click "Book Now"**
4. **Verify Booking**:
   - Check Render logs: `git push` to trigger a redeploy, then check Logs tab for booking creation message.
   - Or SSH into Render and check `storage/bookings.json`:
     ```bash
     ls -la storage/
     cat storage/bookings.json | jq '.' # If jq installed, or cat to see raw JSON
     ```

### 5.2: Test Payment (Razorpay)

1. On the booking page, click "Proceed to Payment" (if payment mode is selected).
2. You'll see the Razorpay checkout modal.
3. Use test card: **4111 1111 1111 1111** (any expiry, any CVV, any OTP).
4. Complete the payment.
5. Verify in Razorpay Dashboard: **Transactions** → should see the payment.
6. Check Render logs for webhook confirmation.

### 5.3: Test Admin Panel

1. **Visit Admin Login**: `https://orvigo-api.onrender.com/public/admin/login.php`
2. **Login Credentials**:
   - Username: `admin` (hardcoded)
   - Password: (the password you hashed and set as `ORVIGO_ADMIN_PASSWORD_HASH`)
3. **View Bookings**: Should see all bookings created.
4. **Update Booking Status**: Change status and verify in storage.

### 5.4: Test Email Notifications

1. After booking, check your email for booking confirmation.
2. If not received, check Render logs for SendGrid errors.
3. Verify `SENDGRID_API_KEY` and sender email (`ORVIGO_ADMIN_EMAIL`) are set.

### 5.5: Test SMS/WhatsApp Notifications

1. After booking, you should receive an SMS/WhatsApp to the phone number provided.
2. If not received, verify Twilio credentials in Render environment.
3. Check Render logs for Twilio API errors.

---

## Troubleshooting

### Common Issues & Fixes

| Issue | Cause | Fix |
|---|---|---|
| **404 on Render URL** | Service not deployed | Check Render Logs; wait for build to complete |
| **Booking form not submitting** | API endpoint mismatch | Verify `ORVIGO_API_BASE` URL in frontend JS |
| **Storage/bookings.json not persisted** | Ephemeral filesystem | Add Render Disk (see Part 1.4) |
| **Razorpay payment fails** | Keys not set | Add `RAZORPAY_KEY_ID` and `RAZORPAY_KEY_SECRET` to Render |
| **Emails not sent** | SendGrid key missing | Add `SENDGRID_API_KEY` to Render |
| **SMS not received** | Twilio not configured | Add Twilio env vars to Render |
| **Admin login fails** | Password hash mismatch | Regenerate hash with `php gen_hash.php` and update Render |
| **Webhook not triggering** | URL mismatch | Verify webhook URL in Razorpay matches Render service |

### Enable Debug Logging

To see detailed logs, update `src/config.php`:

```php
define('ORVIGO_DEBUG', true); // or read from env: getenv('ORVIGO_DEBUG') ?: false
```

Then check `logs/notifications.log` on Render to see API calls and errors.

### SSH into Render for Debugging

1. In Render dashboard, click **Connect** next to your service.
2. Use the shell to inspect files:
   ```bash
   cat storage/bookings.json
   tail -f logs/notifications.log
   ```

---

## Summary: From Zero to Live

1. ✅ Push repo to GitHub (done: `PixelStackAgency/orvigo`)
2. ✅ Deploy backend on Render (Part 1)
3. ✅ Add environment secrets (Part 1.3)
4. ✅ Add persistent storage (Part 1.4)
5. ✅ (Optional) Deploy frontend on Vercel (Part 2)
6. ✅ Configure Razorpay keys & webhook (Part 3)
7. ✅ Setup email & SMS (Part 4)
8. ✅ Test all flows (Part 5)

**Your live Orvigo website** will be at:
- Backend: `https://orvigo-api.onrender.com`
- Frontend: `https://orvigo-web.vercel.app` (if using Vercel) or same Render URL

---

## Next Steps

- Monitor Render logs regularly for errors.
- Switch to **live** Razorpay keys for production payments.
- Setup a managed database (SQLite or PostgreSQL) for scaling.
- Add monitoring/alerting with Render or third-party tools.
- Configure custom domain: Use Render's domain settings or point DNS to your domain.

**Questions?** Check logs, review error messages, and revisit relevant sections above.
