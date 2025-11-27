# 🚀 Quick Start: Deploy Orvigo Live in 30 Minutes

## What You're Deploying
- **Frontend**: Vercel (static HTML/CSS/JS in `public/` folder)
- **Backend**: Render (PHP 8 + Apache with Docker)
- **Payments**: Razorpay (test mode to start)
- **Data**: JSON files stored on Render

---

## 📋 What You Need
1. GitHub account (already have it: `PixelStackAgency/orvigo`)
2. Render account (free at https://render.com)
3. Vercel account (free at https://vercel.com)
4. Razorpay account (free test keys at https://razorpay.com)

---

## ⚡ 3-Step Deployment

### STEP 1: Deploy Render Backend (5 min)

```
1. Go to https://render.com → Sign in with GitHub
2. Click "+ New" → "Web Service"
3. Select repo: PixelStackAgency/orvigo
4. Configure:
   - Name: orvigo-backend
   - Environment: Docker
   - Branch: main
   - Plan: Starter (free)
5. Add environment variables:
   - ORVIGO_ADMIN_PASSWORD_HASH = (generate via: php gen_hash.php "password")
   - RAZORPAY_KEY_ID = (get from Razorpay Dashboard)
   - RAZORPAY_KEY_SECRET = (get from Razorpay Dashboard)
6. Click "Create Web Service"
7. Wait for: "Your service is live on https://orvigo-backend-xxxxx.onrender.com"

⏱️ Takes ~2 minutes to build and deploy
🔗 Save this URL → you need it for Vercel!
```

### STEP 2: Configure Razorpay (3 min)

```
1. Go to https://razorpay.com → Create account (free test keys)
2. Dashboard → Settings → API Keys
3. Copy:
   - Key ID (starts with "rzp_test_")
   - Key Secret
4. Add to Render environment (update existing vars)
5. In Razorpay Dashboard → Settings → Webhooks → "+ Add new webhook"
   - URL: https://orvigo-backend-xxxxx.onrender.com/api/payment-webhook.php
   - Events: payment.authorized, payment.failed
6. Copy Webhook Secret → Add to Render: RAZORPAY_WEBHOOK_SECRET
7. Render auto-redeploys (wait ~30 sec)

✅ Test payment card: 4111 1111 1111 1111
```

### STEP 3: Deploy Vercel Frontend (5 min)

```
1. Go to https://vercel.com → Sign in with GitHub
2. Click "Add New" → "Project"
3. "Import Git Repository" → select PixelStackAgency/orvigo
4. Configure:
   - Framework: Other
   - Root Directory: public
   - Build Command: (leave blank)
5. Add environment variables:
   - VITE_API_BASE = https://orvigo-backend-xxxxx.onrender.com (from Render)
   - VITE_RAZORPAY_KEY_ID = (your Razorpay Key ID)
6. Click "Deploy"
7. Wait for: "✅ Deployment complete"

🎉 You now have a live URL like: https://orvigo-xxxxx.vercel.app
```

---

## ✅ Test It Works (5 min)

```
1. Open https://orvigo-xxxxx.vercel.app (your Vercel URL)
2. Click any service (e.g., "AC Repair")
3. Fill booking form:
   - Name: Test User
   - Phone: 9876543210
   - Address: 123 Test Street
   - Preferred Date: Tomorrow
4. Select "Pay online now"
5. Click "Book Service"
6. Razorpay Checkout appears
7. Enter test card: 4111 1111 1111 1111
8. Expiry: 12/25, CVV: 123
9. Click "Pay"
10. Enter any 6 digits for OTP
11. See ✅ "Payment success"
12. Booking appears in your records!

✨ Your site is now LIVE and WORKING!
```

---

## 📞 What's Available Now

| Feature | Status | How to Use |
|---------|--------|-----------|
| Book Service | ✅ Live | Select service → Fill form → Pay |
| Razorpay Payments | ✅ Live (Test Mode) | Use card: 4111 1111 1111 1111 |
| Admin Dashboard | ✅ Live | `/admin-login.php` (user: admin) |
| SMS Notifications | ⏳ Optional | Need Twilio account (see LIVE_DEPLOYMENT.md) |
| Email Notifications | ⏳ Optional | Need SendGrid account (see LIVE_DEPLOYMENT.md) |
| Custom Domain | ⏳ Next Step | Point to Vercel (Domain Settings) |

---

## 🔐 Access Your Backend

Your Render backend is available at:
```
https://orvigo-backend-xxxxx.onrender.com
```

View bookings by accessing:
```
https://orvigo-backend-xxxxx.onrender.com/admin-login.php
```

Check logs in Render Dashboard:
```
Render → orvigo-backend service → Logs tab
```

---

## 💰 Go Live with Real Payments

When ready for real money:

```
1. Get Razorpay Live API Keys (from Dashboard)
2. In Render environment, update:
   - RAZORPAY_KEY_ID = rzp_live_xxxxx...
   - RAZORPAY_KEY_SECRET = your_live_secret
3. Render auto-redeploys
4. Change any test cards to real cards
5. You're now accepting real payments! 💳
```

---

## 🐛 If Something Breaks

| Problem | Solution |
|---------|----------|
| "Cannot book service" | Check Render URL in Vercel env var is correct |
| "Payment fails" | Verify Razorpay keys in Render Dashboard |
| "Booking doesn't confirm" | Check Render logs for payment webhook errors |
| "Vercel shows 404" | Wait 1 min after deploy, then refresh (cache) |

---

## 📖 Detailed Documentation

- **LIVE_DEPLOYMENT.md** — Full step-by-step guide (with screenshots guide)
- **RAZORPAY_SETUP.md** — Razorpay configuration details
- **README.md** — Project overview and local testing

---

## 🎉 You're Done!

**Your site is live on:**
- 🎨 Frontend: `https://orvigo-xxxxx.vercel.app`
- 🔧 Backend: `https://orvigo-backend-xxxxx.onrender.com`
- 👨‍💼 Admin: `https://orvigo-backend-xxxxx.onrender.com/admin-login.php`

**Next:** Share your Vercel URL with friends to test! 🚀

Any questions? Check `LIVE_DEPLOYMENT.md` section "Troubleshooting"
