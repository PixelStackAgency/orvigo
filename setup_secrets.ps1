#!/usr/bin/env pwsh
# setup_secrets.ps1
# Interactive script to generate and set up all secrets for Render and Vercel deployment

Write-Host "╔════════════════════════════════════════════════════════╗"
Write-Host "║      Orvigo Deployment Secrets Setup Helper           ║"
Write-Host "╚════════════════════════════════════════════════════════╝"
Write-Host ""

# Store all secrets in a hashtable
$secrets = @{}

# 1. Admin Password Hash
Write-Host "STEP 1: Generate Admin Password Hash"
Write-Host "─────────────────────────────────────"
if (Get-Command php -ErrorAction SilentlyContinue) {
    Write-Host "Enter admin password (will be hashed): " -NoNewline
    $adminPass = Read-Host -AsSecureString
    $plain = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto([System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($adminPass))
    
    if (Test-Path "gen_hash.php") {
        $hash = & php gen_hash.php "$plain" 2>$null
        if ($?) {
            $secrets['ORVIGO_ADMIN_PASSWORD_HASH'] = $hash
            Write-Host "✓ Generated: ORVIGO_ADMIN_PASSWORD_HASH"
            Write-Host "  Value: $hash" -ForegroundColor Gray
        }
    }
    [System.Runtime.InteropServices.Marshal]::ZeroFreeBSTR([System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($adminPass)) | Out-Null
} else {
    Write-Host "⚠ PHP not found. To generate hash:"
    Write-Host "  php gen_hash.php \"YourPassword\""
    Write-Host "  Then copy the hash output into ORVIGO_ADMIN_PASSWORD_HASH"
}

Write-Host ""

# 2. Razorpay Keys
Write-Host "STEP 2: Razorpay Configuration"
Write-Host "────────────────────────────"
Write-Host "Get these from: https://dashboard.razorpay.com/app/keys"
Write-Host ""

$rzpId = Read-Host "RAZORPAY_KEY_ID (or press Enter to skip)"
if ($rzpId) { $secrets['RAZORPAY_KEY_ID'] = $rzpId }

$rzpSecret = Read-Host "RAZORPAY_KEY_SECRET (or press Enter to skip)"
if ($rzpSecret) { $secrets['RAZORPAY_KEY_SECRET'] = $rzpSecret }

$rzpWebhook = Read-Host "RAZORPAY_WEBHOOK_SECRET (or press Enter to skip)"
if ($rzpWebhook) { $secrets['RAZORPAY_WEBHOOK_SECRET'] = $rzpWebhook }

Write-Host ""

# 3. SendGrid (optional)
Write-Host "STEP 3: SendGrid (Optional - for Email Notifications)"
Write-Host "────────────────────────────────────────────────────"
$useSendGrid = Read-Host "Add SendGrid email support? (y/n)"
if ($useSendGrid -match '^[yY]') {
    $sendGridKey = Read-Host "SENDGRID_API_KEY (from https://app.sendgrid.com/settings/api_keys)"
    if ($sendGridKey) { 
        $secrets['SENDGRID_API_KEY'] = $sendGridKey
        $sendGridEmail = Read-Host "SENDGRID_FROM_EMAIL (sender email address)"
        if ($sendGridEmail) { $secrets['SENDGRID_FROM_EMAIL'] = $sendGridEmail }
    }
}

Write-Host ""

# 4. Twilio (optional)
Write-Host "STEP 4: Twilio (Optional - for SMS/WhatsApp Notifications)"
Write-Host "────────────────────────────────────────────────────────"
$useTwilio = Read-Host "Add Twilio SMS/WhatsApp support? (y/n)"
if ($useTwilio -match '^[yY]') {
    $twilioSid = Read-Host "TWILIO_ACCOUNT_SID (from https://console.twilio.com)"
    if ($twilioSid) { $secrets['TWILIO_ACCOUNT_SID'] = $twilioSid }
    
    $twilioToken = Read-Host "TWILIO_AUTH_TOKEN"
    if ($twilioToken) { $secrets['TWILIO_AUTH_TOKEN'] = $twilioToken }
    
    $twilioPhone = Read-Host "TWILIO_PHONE_NUMBER (your Twilio phone, e.g., +1234567890)"
    if ($twilioPhone) { $secrets['TWILIO_PHONE_NUMBER'] = $twilioPhone }
}

Write-Host ""

# Display summary
Write-Host "╔════════════════════════════════════════════════════════╗"
Write-Host "║              Secrets Summary for Render               ║"
Write-Host "╚════════════════════════════════════════════════════════╝"
Write-Host ""
Write-Host "Add these to Render environment variables:"
Write-Host ""

foreach ($key in $secrets.Keys | Sort-Object) {
    $value = $secrets[$key]
    if ($value.Length -gt 30) {
        $display = $value.Substring(0, 20) + "...***"
    } else {
        $display = $value
    }
    Write-Host "• $key"
    Write-Host "  $display" -ForegroundColor Gray
}

Write-Host ""
Write-Host "HOW TO ADD TO RENDER:"
Write-Host "1. Go to https://render.com/dashboard"
Write-Host "2. Select 'orvigo-backend' service"
Write-Host "3. Click 'Environment' tab"
Write-Host "4. Click 'Add Environment Variable' for each:"
Write-Host ""
foreach ($key in $secrets.Keys | Sort-Object) {
    Write-Host "   Key: $key"
    Write-Host "   Value: (copy from above)"
}
Write-Host "5. Click 'Save'"
Write-Host "6. Render auto-redeploys"
Write-Host ""

# Export to CSV for easy import
Write-Host "Would you like to export these to a CSV file? (y/n): " -NoNewline
$export = Read-Host
if ($export -match '^[yY]') {
    $csv = @()
    foreach ($key in $secrets.Keys) {
        $csv += [PSCustomObject]@{ 
            'Key' = $key
            'Value' = $secrets[$key]
        }
    }
    $csv | Export-Csv -Path "render-secrets.csv" -NoTypeInformation
    Write-Host "✓ Exported to render-secrets.csv (keep this file secure!)"
}

Write-Host ""
Write-Host "🎉 Setup complete! Next: Go to https://render.com and add these variables"
