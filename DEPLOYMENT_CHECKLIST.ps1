#!/usr/bin/env powershell
# Deployment Checklist for Orvigo
# Copy this checklist and follow it step-by-step

# Colors for output
$Green = [System.ConsoleColor]::Green
$Yellow = [System.ConsoleColor]::Yellow
$Blue = [System.ConsoleColor]::Blue

Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor $Blue
Write-Host "║          ORVIGO LIVE DEPLOYMENT CHECKLIST                      ║" -ForegroundColor $Blue
Write-Host "║     Everything you need to deploy in 30 minutes                ║" -ForegroundColor $Blue
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor $Blue
Write-Host ""

# Pre-deployment
Write-Host "📋 PRE-DEPLOYMENT CHECKLIST" -ForegroundColor $Yellow
Write-Host "═══════════════════════════════════════════════════════════════"
Write-Host "Read and follow these guides FIRST:"
Write-Host ""
Write-Host "  [ ] 1. Read START_HERE.md (this directory)"
Write-Host "  [ ] 2. Read QUICK_START.md (30-minute overview)"
Write-Host "  [ ] 3. Have Render account (https://render.com)"
Write-Host "  [ ] 4. Have Vercel account (https://vercel.com)"
Write-Host "  [ ] 5. Have Razorpay account (https://razorpay.com)"
Write-Host ""

# Step 1
Write-Host "🔴 STEP 1: RENDER BACKEND (5 MINUTES)" -ForegroundColor $Yellow
Write-Host "═══════════════════════════════════════════════════════════════"
Write-Host ""
Write-Host "  [ ] A. Go to: https://render.com/dashboard"
Write-Host "  [ ] B. Click: + New → Web Service"
Write-Host "  [ ] C. Connect GitHub → Select PixelStackAgency/orvigo"
Write-Host "  [ ] D. Configure:"
Write-Host "         • Name: orvigo-backend"
Write-Host "         • Environment: Docker"
Write-Host "         • Branch: main"
Write-Host "         • Plan: Starter (free)"
Write-Host "  [ ] E. Add Environment Variables:"
Write-Host "         • ORVIGO_ADMIN_PASSWORD_HASH = [run: php gen_hash.php]"
Write-Host "         • RAZORPAY_KEY_ID = [leave blank for now]"
Write-Host "         • RAZORPAY_KEY_SECRET = [leave blank for now]"
Write-Host "  [ ] F. Click: Create Web Service"
Write-Host "  [ ] G. Wait 2 minutes for build/deployment"
Write-Host "  [ ] H. See message: 'Your service is live on https://orvigo-backend-xxxxx.onrender.com'"
Write-Host "  [ ] I. Save your Render URL (you'll need it for Vercel!)"
Write-Host ""

# Step 2
Write-Host "💳 STEP 2: RAZORPAY SETUP (3 MINUTES)" -ForegroundColor $Yellow
Write-Host "═══════════════════════════════════════════════════════════════"
Write-Host ""
Write-Host "  [ ] A. Go to: https://razorpay.com"
Write-Host "  [ ] B. Create account (or sign in)"
Write-Host "  [ ] C. Navigate to: Dashboard → Settings → API Keys"
Write-Host "  [ ] D. Copy:"
Write-Host "         • RAZORPAY_KEY_ID (starts with rzp_test_)"
Write-Host "         • RAZORPAY_KEY_SECRET"
Write-Host "  [ ] E. Go back to Render dashboard"
Write-Host "  [ ] F. Go to orvigo-backend → Environment"
Write-Host "  [ ] G. Update variables:"
Write-Host "         • RAZORPAY_KEY_ID = [paste your key]"
Write-Host "         • RAZORPAY_KEY_SECRET = [paste your secret]"
Write-Host "  [ ] H. Click: Save (Render auto-redeploys)"
Write-Host "  [ ] I. In Razorpay: Settings → Webhooks → Add new webhook"
Write-Host "         • URL: https://orvigo-backend-xxxxx.onrender.com/api/payment-webhook.php"
Write-Host "         • Events: payment.authorized, payment.failed"
Write-Host "  [ ] J. Copy Webhook Secret"
Write-Host "  [ ] K. Back to Render: Add RAZORPAY_WEBHOOK_SECRET = [webhook secret]"
Write-Host "  [ ] L. Wait 30 seconds for Render to redeploy"
Write-Host ""

# Step 3
Write-Host "🎨 STEP 3: VERCEL FRONTEND (5 MINUTES)" -ForegroundColor $Yellow
Write-Host "═══════════════════════════════════════════════════════════════"
Write-Host ""
Write-Host "  [ ] A. Go to: https://vercel.com/dashboard"
Write-Host "  [ ] B. Click: Add New → Project"
Write-Host "  [ ] C. Click: Import Git Repository"
Write-Host "  [ ] D. Select: PixelStackAgency/orvigo"
Write-Host "  [ ] E. Configure:"
Write-Host "         • Framework: Other"
Write-Host "         • Root Directory: public"
Write-Host "         • Build Command: (leave blank)"
Write-Host "  [ ] F. Add Environment Variables:"
Write-Host "         • VITE_API_BASE = https://orvigo-backend-xxxxx.onrender.com"
Write-Host "         • VITE_RAZORPAY_KEY_ID = [your Razorpay Key ID]"
Write-Host "  [ ] G. Click: Deploy"
Write-Host "  [ ] H. Wait 1-2 minutes for deployment"
Write-Host "  [ ] I. See: ✅ Deployment Complete"
Write-Host "  [ ] J. Click on your domain (https://orvigo-xxxxx.vercel.app)"
Write-Host ""

# Testing
Write-Host "✅ STEP 4: TEST EVERYTHING (5 MINUTES)" -ForegroundColor $Green
Write-Host "═══════════════════════════════════════════════════════════════"
Write-Host ""
Write-Host "  [ ] A. Open: https://orvigo-xxxxx.vercel.app (your Vercel URL)"
Write-Host "  [ ] B. Click: Select a service (e.g., 'AC Repair')"
Write-Host "  [ ] C. Fill form:"
Write-Host "         • Name: Test User"
Write-Host "         • Phone: 9876543210"
Write-Host "         • Address: 123 Test Street"
Write-Host "         • Date: Tomorrow"
Write-Host "         • Preferred Time: Any time slot"
Write-Host "  [ ] D. Select: Pay online now"
Write-Host "  [ ] E. Click: Book Service"
Write-Host "  [ ] F. Razorpay Checkout appears"
Write-Host "  [ ] G. Enter test card:"
Write-Host "         • Number: 4111 1111 1111 1111"
Write-Host "         • Expiry: 12/25"
Write-Host "         • CVV: 123"
Write-Host "  [ ] H. Click: Pay"
Write-Host "  [ ] I. Enter any 6 digits for OTP"
Write-Host "  [ ] J. See: ✅ Payment success"
Write-Host "  [ ] K. You're redirected to booking confirmation"
Write-Host "  [ ] L. Go to Render backend: https://orvigo-backend-xxxxx.onrender.com/admin-login.php"
Write-Host "         • Username: admin"
Write-Host "         • Password: [the one you hashed with gen_hash.php]"
Write-Host "  [ ] M. See your booking in admin dashboard"
Write-Host ""

# Post-deployment
Write-Host "🎉 SUCCESS! YOUR WEBSITE IS LIVE!" -ForegroundColor $Green
Write-Host "═══════════════════════════════════════════════════════════════"
Write-Host ""
Write-Host "Your URLs:"
Write-Host "  🎨 Frontend:  https://orvigo-xxxxx.vercel.app"
Write-Host "  🔧 Backend:   https://orvigo-backend-xxxxx.onrender.com"
Write-Host "  👨‍💼 Admin:     https://orvigo-backend-xxxxx.onrender.com/admin-login.php"
Write-Host ""

# Optional enhancements
Write-Host "📦 OPTIONAL NEXT STEPS" -ForegroundColor $Yellow
Write-Host "═══════════════════════════════════════════════════════════════"
Write-Host ""
Write-Host "  [ ] Enable email notifications (SendGrid)"
Write-Host "  [ ] Enable SMS notifications (Twilio)"
Write-Host "  [ ] Switch Razorpay to live mode (real payments)"
Write-Host "  [ ] Add custom domain (in Vercel settings)"
Write-Host "  [ ] Add Render Disk for persistent storage"
Write-Host "  [ ] Set up monitoring/alerts in Render"
Write-Host ""

# Resources
Write-Host "📖 HELPFUL RESOURCES" -ForegroundColor $Blue
Write-Host "═══════════════════════════════════════════════════════════════"
Write-Host ""
Write-Host "  START_HERE.md          ← Read this first"
Write-Host "  QUICK_START.md         ← 30-minute deployment guide"
Write-Host "  LIVE_DEPLOYMENT.md     ← Detailed step-by-step guide"
Write-Host "  RAZORPAY_SETUP.md      ← Payment integration details"
Write-Host "  DEPLOYMENT_READY.md    ← What's included in this package"
Write-Host "  README.md              ← Project overview"
Write-Host ""

# Support
Write-Host "🆘 TROUBLESHOOTING" -ForegroundColor $Yellow
Write-Host "═══════════════════════════════════════════════════════════════"
Write-Host ""
Write-Host "  Problem: Cannot connect to API from Vercel"
Write-Host "  Solution: Verify VITE_API_BASE in Vercel environment"
Write-Host ""
Write-Host "  Problem: Razorpay payment fails"
Write-Host "  Solution: Check Razorpay keys in Render environment"
Write-Host ""
Write-Host "  Problem: Booking doesn't save"
Write-Host "  Solution: Check Render logs for errors"
Write-Host ""
Write-Host "  Problem: Admin login doesn't work"
Write-Host "  Solution: Verify ORVIGO_ADMIN_PASSWORD_HASH is set"
Write-Host ""
Write-Host "  For more help: See LIVE_DEPLOYMENT.md 'Troubleshooting' section"
Write-Host ""

Write-Host "═══════════════════════════════════════════════════════════════"
Write-Host "🚀 Ready to deploy? Start with Step 1 above!"
Write-Host "═══════════════════════════════════════════════════════════════"
Write-Host ""
