#!/usr/bin/env pwsh
# push_orvigo.ps1
# Usage: Run from project root in PowerShell: .\push_orvigo.ps1
# This script initializes git (if needed), commits, creates the GitHub repo via gh, pushes,
# generates an admin password hash with PHP (if available), and sets GitHub secrets via gh.

Write-Host "Starting Orvigo push script..."

# Target repository (owner/repo)
$remoteRepo = "PixelStackAgency/orvigo"
$remoteUrl = "https://github.com/$remoteRepo.git"

# Ensure .gitignore covers sensitive files
$gitignorePath = ".gitignore"
if (-not (Test-Path $gitignorePath)) {
    @"
.env
storage/
logs/
*.env
vendor/
node_modules/
.DS_Store
"@ | Out-File -Encoding utf8 $gitignorePath
    Write-Host "Created .gitignore"
} else {
    $g = Get-Content $gitignorePath -Raw
    foreach ($entry in '.env','storage/','logs/','*.env') {
        if ($g -notmatch [regex]::Escape($entry)) {
            Add-Content $gitignorePath $entry
            Write-Host "Appended $entry to .gitignore"
        }
    }
}

# Remove any accidentally committed .env from index
if (Test-Path ".env") {
    git rm --cached -f .env 2>$null
    Write-Host "Removed .env from git index (if it was tracked)."
}

# Init repo if not a git repo
if (-not (Test-Path ".git")) {
    git init
    Write-Host "Initialized new git repo."
}

# Stage and commit (if there are changes)
git add .
try {
    git commit -m "Initial commit: Orvigo website" 2>$null | Out-Null
} catch {
    Write-Host "No new changes to commit or commit failed (continuing)."
}

# Ensure branch name
git branch -M main 2>$null

# Check gh authentication
Write-Host "Checking GitHub CLI authentication..."
gh auth status 1>$null 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "gh is not authenticated. Run 'gh auth login' and re-run this script."
    exit 1
}

# Create or push to remote repo using gh
Write-Host "Creating or pushing to GitHub repo $remoteRepo..."
gh repo create $remoteRepo --public --source=. --remote=origin --push --confirm 1>$null 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "Repository created or updated and pushed to origin/main."
} else {
    Write-Host "gh repo create returned non-zero; ensuring remote and pushing manually..."
    git remote remove origin 2>$null
    git remote add origin $remoteUrl
    git push -u origin main
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Push failed. Please check remote URL, network and permissions, then try again."
        exit 1
    }
}

# Generate admin password hash (optional, requires php)
if (Get-Command php -ErrorAction SilentlyContinue) {
    # Ask user for admin password
    $adminPass = Read-Host -AsSecureString "Enter the admin password to hash (input hidden)"
    $plain = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto([System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($adminPass))
    # Use gen_hash.php to avoid complex quoting
    if (Test-Path "gen_hash.php") {
        $hash = & php gen_hash.php "$plain"
    } else {
        # Fallback: use inline PHP (rarely used)
        $env:ORVIGO_PW = $plain
        $hash = & php -r "echo password_hash(getenv('ORVIGO_PW'), PASSWORD_DEFAULT) . PHP_EOL;"
        Remove-Item Env:\ORVIGO_PW -ErrorAction SilentlyContinue
    }
    if ($?) {
        Write-Host "Generated hash: $hash"
        Write-Host "Setting ORVIGO_ADMIN_PASSWORD_HASH secret via gh..."
        gh secret set ORVIGO_ADMIN_PASSWORD_HASH --repo $remoteRepo --body $hash
        if ($LASTEXITCODE -eq 0) { Write-Host "Secret set successfully." } else { Write-Host "Failed to set secret via gh (check permissions)." }
    } else {
        Write-Host "Failed to generate hash with PHP. You can run: php gen_hash.php \"YourPassword\" and then:"
        Write-Host "gh secret set ORVIGO_ADMIN_PASSWORD_HASH --repo $remoteRepo --body '<HASH>'"
    }
} else {
    Write-Host "PHP not found in PATH. Skipping auto-hash generation. You can run gen_hash.php or the one-liner locally to generate a hash and then run:"
    Write-Host "gh secret set ORVIGO_ADMIN_PASSWORD_HASH --repo $remoteRepo --body '<HASH>'"
}

# Optionally set Razorpay secrets
$setRzp = Read-Host "Do you want to add Razorpay secrets now? (y/n)"
if ($setRzp -match '^[yY]') {
    $rzpId = Read-Host "Enter RAZORPAY_KEY_ID (or leave blank to skip)"
    if ($rzpId) { gh secret set RAZORPAY_KEY_ID --repo $remoteRepo --body $rzpId }
    $rzpSecret = Read-Host "Enter RAZORPAY_KEY_SECRET (or leave blank to skip)"
    if ($rzpSecret) { gh secret set RAZORPAY_KEY_SECRET --repo $remoteRepo --body $rzpSecret }
    $rzpWebhook = Read-Host "Enter RAZORPAY_WEBHOOK_SECRET (or leave blank to skip)"
    if ($rzpWebhook) { gh secret set RAZORPAY_WEBHOOK_SECRET --repo $remoteRepo --body $rzpWebhook }
    Write-Host "Razorpay secrets updated (as provided)."
}

Write-Host "Done. If the push succeeded, your repo is available at https://github.com/$remoteRepo"
Write-Host "Next: Connect the repo in Vercel (Import Project) and configure environment variables in Vercel or your backend host (Render)."

window.ORVIGO_API_BASE = "https://your-render-service.example";
