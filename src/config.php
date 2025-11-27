<?php
// --- src/config.php ---
// Configuration and environment constants for Orvigo
// Update these values for production deployment (API keys, admin email, etc.)

declare(strict_types=1);

// Base paths
define('ORVIGO_ROOT', dirname(__DIR__));
define('ORVIGO_STORAGE', ORVIGO_ROOT . DIRECTORY_SEPARATOR . 'storage');
define('ORVIGO_LOGS', ORVIGO_ROOT . DIRECTORY_SEPARATOR . 'logs');
define('ORVIGO_PUBLIC', ORVIGO_ROOT . DIRECTORY_SEPARATOR . 'public');

// Site info
define('ORVIGO_NAME', 'Orvigo');
define('ORVIGO_TAGLINE', 'Fast, trusted home-appliance & electronics repair at your doorstep.');
define('ORVIGO_DESCRIPTION', 'Orvigo is a trusted doorstep repair service specializing in maintenance and repair of home appliances and electronics — ACs, TVs, Washing Machines, Refrigerators, Geysers / Water Heaters, Water Purifiers, Microwave Ovens, Mobile & Tablet devices. We bring trained technicians right to your home for fast, affordable, same-day service in Bangalore.');
define('ORVIGO_DEFAULT_CITY', 'Bangalore');

// Admin & placeholders
define('ORVIGO_ADMIN_EMAIL', 'admin@example.com'); // Change to real admin email
define('ORVIGO_CONTACT_PHONE', '+91-90000-00000'); // Placeholder phone

// Razorpay - prefer environment variables. DO NOT commit real keys.
$env_rzp_id = getenv('RAZORPAY_KEY_ID') ?: getenv('ORVIGO_RAZORPAY_KEY_ID');
$env_rzp_secret = getenv('RAZORPAY_KEY_SECRET') ?: getenv('ORVIGO_RAZORPAY_KEY_SECRET');
if ($env_rzp_id) {
    define('RAZORPAY_KEY_ID', $env_rzp_id);
} else {
    define('RAZORPAY_KEY_ID', 'rzp_test_XXXXXXXXXXXXXXXX');
}
if ($env_rzp_secret) {
    define('RAZORPAY_KEY_SECRET', $env_rzp_secret);
} else {
    define('RAZORPAY_KEY_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXX');
}
// Razorpay webhook secret (set from Razorpay dashboard). Leave empty for test/simulate mode.
$env_webhook = getenv('RAZORPAY_WEBHOOK_SECRET') ?: getenv('ORVIGO_RAZORPAY_WEBHOOK_SECRET');
define('RAZORPAY_WEBHOOK_SECRET', $env_webhook ?: '');

// Admin password for simple admin panel (change in production).
// For security, provide a hashed password via environment variable ORVIGO_ADMIN_PASSWORD_HASH.
// If not provided, legacy plaintext ORVIGO_ADMIN_PASSWORD is used as a fallback (not recommended).
define('ORVIGO_ADMIN_PASSWORD', 'ChangeThisPassword');
// Compute password hash: prefer env var ORVIGO_ADMIN_PASSWORD_HASH, otherwise hash the fallback.
$envHash = getenv('ORVIGO_ADMIN_PASSWORD_HASH') ?: '';
if (!empty($envHash)) {
    define('ORVIGO_ADMIN_PASSWORD_HASH', $envHash);
} else {
    // Hash the fallback (this is only for initial convenience; set env var on production)
    define('ORVIGO_ADMIN_PASSWORD_HASH', password_hash(ORVIGO_ADMIN_PASSWORD, PASSWORD_DEFAULT));
}

// Storage files
define('BOOKINGS_FILE', ORVIGO_STORAGE . DIRECTORY_SEPARATOR . 'bookings.json');

// Make sure storage and logs directories exist
if (!is_dir(ORVIGO_STORAGE)) {
    mkdir(ORVIGO_STORAGE, 0755, true);
}
if (!is_dir(ORVIGO_LOGS)) {
    mkdir(ORVIGO_LOGS, 0755, true);
}

// Create bookings.json if missing
if (!file_exists(BOOKINGS_FILE)) {
    file_put_contents(BOOKINGS_FILE, json_encode([]));
}

// Simple helper to compute base URL dynamically (best-effort)
function orvigo_base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $path = rtrim(dirname($script), '/\\');
    return $scheme . '://' . $host . $path;
}

// Simple environment toggle for logs/debugging
define('ORVIGO_DEBUG', true);
