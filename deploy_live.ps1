#!/usr/bin/env pwsh
# deploy_live.ps1
# Automated deployment helper for Orvigo on Render + Vercel
# Usage: .\deploy_live.ps1

Write-Host "╔════════════════════════════════════════════════════════════════╗"
Write-Host "║          Orvigo Live Deployment Helper (Render + Vercel)       ║"
Write-Host "╚════════════════════════════════════════════════════════════════╝"
Write-Host ""

# Step 1: Generate admin password hash
Write-Host "STEP 1: Generate Admin Password Hash"
Write-Host "───────────────────────────────────"
Write-Host ""
if (Get-Command php -ErrorAction SilentlyContinue) {
    $adminPass = Read-Host "Enter admin password to hash (or press Enter to skip)" -AsSecureString
    if ($adminPass.Length -gt 0) {
        $plain = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto([System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($adminPass))
        if (Test-Path "gen_hash.php") {
            $hash = & php gen_hash.php "$plain" 2>$null
            if ($?) {
                Write-Host "✓ Admin password hash generated:"
                Write-Host "  $hash"
                Write-Host ""
                Write-Host "→ Copy this hash into Render environment variable: ORVIGO_ADMIN_PASSWORD_HASH"
            }
        }
        Remove-Variable plain -ErrorAction SilentlyContinue
    }
} else {
    Write-Host "⚠ PHP not found. To generate hash manually, run:"
    Write-Host "  php gen_hash.php \"YourPassword\""
    Write-Host ""
}

# Step 2: Get Razorpay keys from user
Write-Host ""
Write-Host "STEP 2: Razorpay Configuration"
Write-Host "──────────────────────────────"
Write-Host ""
$getRzp = Read-Host "Do you have Razorpay test keys? (y/n)"
if ($getRzp -match '^[yY]') {
    $rzpId = Read-Host "Enter RAZORPAY_KEY_ID (from https://dashboard.razorpay.com/app/keys)"
    $rzpSecret = Read-Host "Enter RAZORPAY_KEY_SECRET"
    if ($rzpId -and $rzpSecret) {
        Write-Host ""
        Write-Host "✓ Razorpay keys to add in Render:"
        Write-Host "  RAZORPAY_KEY_ID = $rzpId"
        Write-Host "  RAZORPAY_KEY_SECRET = $($rzpSecret.Substring(0,4))...***"
    }
}

# Step 3: Git push final changes
Write-Host ""
Write-Host "STEP 3: Push Changes to GitHub"
Write-Host "──────────────────────────────"
Write-Host ""
git add .
$commitMsg = Read-Host "Enter commit message (or press Enter for default)"
if (-not $commitMsg) { $commitMsg = "Deployment configuration: Render backend + Vercel frontend" }
git commit -m "$commitMsg" 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Changes committed"
    git push
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Pushed to GitHub — Render auto-deploys from main branch"
    }
} else {
    Write-Host "ℹ No changes to commit"
}

# Step 4: Provide deployment instructions
Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════════╗"
Write-Host "║                    Deployment Instructions                     ║"
Write-Host "╚════════════════════════════════════════════════════════════════╝"
Write-Host ""
Write-Host "1️⃣  RENDER BACKEND DEPLOYMENT:"
Write-Host "   • Go to https://render.com"
Write-Host "   • Create new Web Service"
Write-Host "   • Connect to GitHub repo: PixelStackAgency/orvigo"
Write-Host "   • Configure as 'Docker' environment"
Write-Host "   • Add these environment variables:"
Write-Host "     - ORVIGO_ADMIN_PASSWORD_HASH = <paste your hash above>"
Write-Host "     - RAZORPAY_KEY_ID = <your test key>"
Write-Host "     - RAZORPAY_KEY_SECRET = <your secret>"
Write-Host "   • Deploy → Wait for 'Your service is live'"
Write-Host "   • Save the URL: https://orvigo-backend-xxxxx.onrender.com"
Write-Host ""
Write-Host "2️⃣  RAZORPAY WEBHOOK CONFIGURATION:"
Write-Host "   • In Razorpay Dashboard → Settings → Webhooks"
Write-Host "   • Add new webhook:"
Write-Host "     - URL: https://orvigo-backend-xxxxx.onrender.com/api/payment-webhook.php"
Write-Host "     - Events: payment.authorized, payment.failed"
Write-Host "   • Save and copy the webhook secret"
Write-Host "   • In Render, add: RAZORPAY_WEBHOOK_SECRET = <paste secret>"
Write-Host ""
Write-Host "3️⃣  VERCEL FRONTEND DEPLOYMENT:"
Write-Host "   • Go to https://vercel.com"
Write-Host "   • Import project: PixelStackAgency/orvigo"
Write-Host "   • Set Root Directory: 'public'"
Write-Host "   • Add environment variables:"
Write-Host "     - VITE_API_BASE = https://orvigo-backend-xxxxx.onrender.com"
Write-Host "     - VITE_RAZORPAY_KEY_ID = <your Razorpay Key ID>"
Write-Host "   • Deploy → Wait for deployment to finish"
Write-Host ""
Write-Host "4️⃣  TEST END-TO-END:"
Write-Host "   • Open your Vercel frontend URL"
Write-Host "   • Book a service → Pay with test card 4111 1111 1111 1111"
Write-Host "   • Verify booking in Render backend logs"
Write-Host ""
Write-Host "📖 For detailed instructions, see: LIVE_DEPLOYMENT.md"
Write-Host ""
Write-Host "✅ Ready to deploy! Follow the steps above to go live in 30 minutes."
Write-Host ""
