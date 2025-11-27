# 🚀 Orvigo Live Deployment — Vercel + Render — 100% Working Guide

This guide walks you through deploying Orvigo **100% live** with:
- ✅ **Render backend** (PHP Apache + JSON storage)
- ✅ **Vercel frontend** (static SPA with API integration)
- ✅ **Razorpay payments** (test and live modes)
- ✅ **End-to-end testing** (booking → payment → confirmation)

**Time to Live: ~30 minutes**

---

## 📋 Prerequisites

1. **GitHub Account** — Repository already pushed: `PixelStackAgency/orvigo`
2. **Render Account** — Free tier available at https://render.com
3. **Vercel Account** — Free tier available at https://vercel.com
4. **Razorpay Account** — Test mode available immediately (https://razorpay.com)
5. **PHP 8+ locally** (optional but helpful for password hashing)

---

## 🔴 PART 1: Deploy Backend on Render (15 minutes)

### Step 1.1: Create Render Account & Connect GitHub

1. Go to https://render.com and sign up (use GitHub for fastest setup)
2. Click **+ New** → **Web Service**
3. Select **Build and deploy from a Git repository**
4. Click **Connect account** → authorize GitHub
5. Find and select `PixelStackAgency/orvigo`
6. Click **Connect**

### Step 1.2: Configure Render Service

Fill in the deployment form:

| Field | Value |
|-------|-------|
| **Name** | `orvigo-backend` |
| **Environment** | `Docker` |
| **Region** | `Oregon` (or closest to users) |
| **Branch** | `main` |
| **Build Command** | (leave blank) |
| **Start Command** | (leave blank) |
| **Plan** | `Starter (free)` |

### Step 1.3: Add Environment Secrets

Before clicking **Create Web Service**, scroll down and add these environment variables:

**Must add:**
1. **`ORVIGO_ADMIN_PASSWORD_HASH`**
   - Generate locally:
   ```cmd
   php gen_hash.php "YourSecurePassword123"
   ```
   - Copy the hash (starts with `$2y$`), paste it here

2. **`RAZORPAY_KEY_ID`** (leave blank initially, we'll configure Razorpay next)

3. **`RAZORPAY_KEY_SECRET`** (leave blank initially)

4. **`RAZORPAY_WEBHOOK_SECRET`** (leave blank initially)

5. **`SENDGRID_API_KEY`** (optional, only if you want email notifications)

6. **`TWILIO_ACCOUNT_SID`** (optional, only if you want SMS notifications)

7. **`TWILIO_AUTH_TOKEN`** (optional, only if you want SMS notifications)

Click **Create Web Service** → Render will build and deploy in ~2 minutes.

### Step 1.4: Verify Render Deployment

1. Go to your Render dashboard
2. Find `orvigo-backend` service
3. Wait for **"Your service is live on https://orvigo-backend.onrender.com"** (or similar URL)
4. Click the URL and verify you see the Orvigo home page

**Save this URL** — you'll need it for Vercel! It looks like: `https://orvigo-backend-xxxxx.onrender.com`

### Step 1.5: Test Render Backend Endpoints

Open your browser and test:

```
# Home page (should load)
https://orvigo-backend-xxxxx.onrender.com

# API test (should return JSON)
https://orvigo-backend-xxxxx.onrender.com/api/book-service.php
# (Should show error about missing parameters, but JSON response is good)
```

---

## 💳 PART 2: Configure Razorpay (5 minutes)

### Step 2.1: Get Razorpay Test Keys

1. Go to https://razorpay.com and sign up (free)
2. **Dashboard** → **Settings** → **API Keys** (or click "Generate Key Pair")
3. You'll see **Key ID** and **Key Secret** for Test mode

### Step 2.2: Add Razorpay Keys to Render

1. Go to your Render service `orvigo-backend`
2. Click **Environment** (tab on the left)
3. Edit these variables:
   - **`RAZORPAY_KEY_ID`** = your Test Key ID
   - **`RAZORPAY_KEY_SECRET`** = your Test Key Secret
4. Click **Save** (Render will redeploy automatically)

### Step 2.3: Configure Razorpay Webhook (for notifications)

1. In Razorpay Dashboard → **Settings** → **Webhooks**
2. Click **+ Add new webhook**
3. Enter:
   - **URL**: `https://orvigo-backend-xxxxx.onrender.com/api/payment-webhook.php`
   - **Events**: Select `payment.authorized` and `payment.failed`
4. Copy the **Webhook Secret**, then:
   - Go back to Render
   - Add environment variable: **`RAZORPAY_WEBHOOK_SECRET`** = paste the secret
   - Save

### Step 2.4: Test with Razorpay Test Card

Later when you deploy Vercel, you'll test with this test card:
- **Card Number**: `4111 1111 1111 1111`
- **Expiry**: `12/25` (any future date)
- **CVV**: `123` (any 3 digits)

---

## 🎨 PART 3: Deploy Frontend on Vercel (10 minutes)

The frontend needs to point to your Render backend. We'll set up a Vercel project for the static frontend.

### Step 3.1: Create Frontend-Only Build

First, let's create a `.vercelignore` file so Vercel only deploys the frontend (public folder as-is):

**GitHub**: Create file `public/vercel.json` in the repo:

```json
{
  "version": 2,
  "public": true,
  "buildCommand": "echo 'Frontend only - no build needed'",
  "outputDirectory": "public",
  "env": {
    "VITE_API_BASE": "@api_base_url"
  }
}
```

But actually, the easiest approach is to deploy the entire repo and just set the root:

### Step 3.2: Import GitHub Repo to Vercel

1. Go to https://vercel.com and sign up/login
2. Click **Add New** → **Project**
3. Click **Import Git Repository** → select `PixelStackAgency/orvigo`
4. Vercel detects it as `Other` → Configure:
   - **Framework Preset**: `Other`
   - **Root Directory**: `public`
   - **Build Command**: (leave blank)
   - **Output Directory**: (leave blank)

### Step 3.3: Add Environment Variable (Render Backend URL)

Before deploying, add:

**Environment Variables:**
- **Name**: `VITE_API_BASE`
- **Value**: `https://orvigo-backend-xxxxx.onrender.com` (your Render URL)

Also add these for reference:
- **`RAZORPAY_KEY_ID`**: (same as Render, for frontend to use)

Click **Deploy** → Vercel will deploy in ~1 minute

### Step 3.4: Update Frontend HTML to Use Environment Variable

Once Vercel deploys, the frontend needs to know about your Render backend. The frontend files already look for `window.ORVIGO.API_BASE`, so we need to inject it.

In `public/index.php` (or in a script tag), add:

```html
<script>
  // Set API base URL from environment variable or default
  window.ORVIGO = window.ORVIGO || {};
  window.ORVIGO.API_BASE = import.meta.env.VITE_API_BASE || 'https://your-render-url.onrender.com';
  window.ORVIGO.RAZORPAY_KEY_ID = import.meta.env.VITE_RAZORPAY_KEY_ID || 'your_key_id';
</script>
```

**Simpler approach for now:** Edit `public/assets/js/main.js` and update the API base URL manually:

```javascript
// In main.js, find this line:
const API_BASE = 'https://orvigo-backend-xxxxx.onrender.com'; // ← Replace with your Render URL
```

Then commit and push to GitHub → Vercel auto-redeploys.

---

## ✅ PART 4: Test End-to-End (Full Flow)

### Step 4.1: Open Vercel Frontend

1. Your Vercel deployment URL is shown in the Vercel dashboard (something like `https://orvigo-xxxxx.vercel.app`)
2. Open it in browser → should see Orvigo home page

### Step 4.2: Create Test Booking

1. Click any service (e.g., "AC Repair")
2. Fill in booking form:
   - Name: `Test User`
   - Phone: `9876543210`
   - Address: `123 Test St`
   - Preferred Date: `Tomorrow`
3. Select **"Pay online now"**
4. Click **Book Service**

### Step 4.3: Complete Razorpay Test Payment

1. Razorpay Checkout opens
2. Enter test card: `4111 1111 1111 1111`
3. Expiry: `12/25`, CVV: `123`
4. Click **Pay**
5. You'll see a test OTP → enter any 6 digits
6. Payment should show **Success** ✅

### Step 4.4: Verify Booking in Backend

1. Go to Render dashboard → `orvigo-backend` service
2. Click **Logs** tab
3. Should see payment confirmation logged
4. Storage file `storage/bookings.json` now contains your test booking

---

## 🔐 Admin Dashboard Access (Optional)

1. Go to your Vercel frontend → `/admin-login.php`
2. Username: `admin`
3. Password: The one you hashed earlier
4. View all bookings, update statuses, mark as completed

---

## 📧 Optional: Enable Email Notifications

1. Get SendGrid API key: https://sendgrid.com (free tier available)
2. In Render environment: add `SENDGRID_API_KEY`
3. In `src/config.php`, uncomment the SendGrid notifier
4. Bookings will now email confirmations

---

## 📱 Optional: Enable SMS Notifications

1. Get Twilio account: https://twilio.com (free credits available)
2. In Render environment: add `TWILIO_ACCOUNT_SID` and `TWILIO_AUTH_TOKEN`
3. In `src/config.php`, uncomment the Twilio notifier
4. Bookings will now send SMS confirmations

---

## 🐛 Troubleshooting

### Vercel → Render connection fails (404/CORS)

**Solution:**
1. Verify Render URL is correct: `https://orvigo-backend-xxxxx.onrender.com`
2. Test in browser: `https://orvigo-backend-xxxxx.onrender.com/api/book-service.php`
3. If 404, Render may still be building → wait 2 mins and refresh
4. In Vercel, re-check the environment variable `VITE_API_BASE`

### Razorpay payment fails in test mode

**Solution:**
1. Verify `RAZORPAY_KEY_ID` and `RAZORPAY_KEY_SECRET` are set in Render
2. Render redeploy after setting secrets: click **Manual Deploy** on Render
3. Test card must be exactly: `4111 1111 1111 1111`
4. Check Render logs for payment errors

### Booking shows but payment doesn't confirm

**Solution:**
1. Razorpay webhook URL must be exactly: `https://orvigo-backend-xxxxx.onrender.com/api/payment-webhook.php`
2. Verify webhook secret matches in Razorpay Dashboard
3. Check Render logs for webhook errors

### "File not writable" or storage errors

**Solution:**
1. Render free tier has **ephemeral** storage (data lost on restart)
2. Options:
   - **Add Render Disk** (paid): In Render dashboard, click **Disks** → add `/var/www/html/storage` (10 GB minimum)
   - **Switch to database** (faster): Update `BookingStore.php` to use PostgreSQL or MySQL (Render provides free databases)

---

## 🎉 Your Site is Live!

Once everything is deployed:

- **Frontend**: `https://orvigo-xxxxx.vercel.app`
- **Backend API**: `https://orvigo-backend-xxxxx.onrender.com/api/`
- **Admin**: `https://orvigo-xxxxx.vercel.app/admin-login.php`
- **Payments**: Razorpay test mode (upgrade to live anytime)

### Next Steps for Production

1. **Upgrade Razorpay**: Switch from test keys to live keys in Render environment
2. **Custom Domain**: Point your domain to Vercel (custom domain setup in Vercel dashboard)
3. **Add HTTPS**: Vercel and Render auto-include SSL certificates
4. **Persistent Storage**: Add Render Disk for `/var/www/html/storage` to keep data between restarts
5. **Database**: Consider migrating from JSON to PostgreSQL for better scaling (Render offers free PostgreSQL)
6. **Monitoring**: Set up Render Alerts to notify you of service downtime

---

## 📞 Support

If you get stuck:
1. Check **Render Logs** tab for backend errors
2. Check **Vercel Deployments** tab for frontend build errors
3. Verify environment variables are set in both services
4. Ensure GitHub repo is up to date: `git push`

**You're all set to go live! 🚀**
