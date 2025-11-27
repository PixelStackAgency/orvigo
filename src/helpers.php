<?php
// --- src/helpers.php ---
// Utility functions: sanitization, CSRF, ID generation, simple validators

declare(strict_types=1);

require_once __DIR__ . '/config.php';

session_start();

function h($str)
{
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sanitize_input(array $data): array
{
    $out = [];
    foreach ($data as $k => $v) {
        if (is_array($v)) {
            $out[$k] = sanitize_input($v);
        } else {
            $out[$k] = trim((string)$v);
        }
    }
    return $out;
}

function generate_booking_id(string $category = 'GEN'): string
{
    $dt = new DateTimeImmutable('now', new DateTimeZone('Asia/Kolkata'));
    $ts = $dt->format('YmdHis');
    $rand = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    return sprintf('ORVIGO-%s-%s-%s', $dt->format('Y'), strtoupper(substr($category, 0, 3)), $ts . '-' . $rand);
}

function json_response($data, int $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// CSRF token helpers
function csrf_token(): string
{
    if (empty($_SESSION['orvigo_csrf'])) {
        $_SESSION['orvigo_csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['orvigo_csrf'];
}

function verify_csrf(string $token): bool
{
    return isset($_SESSION['orvigo_csrf']) && hash_equals($_SESSION['orvigo_csrf'], $token);
}

// Simple validators
function is_valid_phone(string $phone): bool
{
    // Indian phone number basic validation (10 digits, optionally +91)
    $p = preg_replace('/[^0-9]/', '', $phone);
    return strlen($p) >= 10 && strlen($p) <= 13;
}

function is_valid_pincode(string $pincode): bool
{
    return preg_match('/^[1-9][0-9]{5}$/', $pincode) === 1;
}

function log_message(string $file, string $message)
{
    $path = ORVIGO_LOGS . DIRECTORY_SEPARATOR . $file;
    $line = '[' . (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format(DateTime::ATOM) . '] ' . $message . PHP_EOL;
    file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
}

// Rate limiting: simple IP-based limiter stored in logs/rate_limits.json
function client_ip(): string
{
    $keys = ['HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','HTTP_X_FORWARDED','HTTP_X_CLUSTER_CLIENT_IP','HTTP_FORWARDED_FOR','HTTP_FORWARDED','REMOTE_ADDR'];
    foreach ($keys as $k) {
        if (!empty($_SERVER[$k])) {
            $ips = explode(',', $_SERVER[$k]);
            return trim($ips[0]);
        }
    }
    return 'unknown';
}

function rate_limit(int $maxRequests = 30, int $perSeconds = 60): bool
{
    $ip = client_ip();
    $file = ORVIGO_LOGS . DIRECTORY_SEPARATOR . 'rate_limits.json';
    if (!file_exists($file)) file_put_contents($file, json_encode([]));
    $fp = fopen($file, 'c+');
    if (!$fp) return false; // can't enforce
    try {
        if (!flock($fp, LOCK_EX)) return false;
        $raw = stream_get_contents($fp);
        $map = json_decode($raw, true) ?: [];
        $now = time();
        $entry = $map[$ip] ?? ['count'=>0,'reset'=>$now + $perSeconds];
        if ($now > $entry['reset']) {
            $entry = ['count'=>1,'reset'=>$now + $perSeconds];
        } else {
            $entry['count']++;
        }
        $map[$ip] = $entry;
        ftruncate($fp,0); rewind($fp); fwrite($fp, json_encode($map, JSON_PRETTY_PRINT)); fflush($fp); flock($fp, LOCK_UN);
        return $entry['count'] <= $maxRequests;
    } finally { fclose($fp); }
}

// Honeypot field check: field should be empty; bots often fill all fields
function honeypot_ok(array $data, string $field = 'website'): bool
{
    if (isset($data[$field]) && trim((string)$data[$field]) !== '') {
        log_message('notifications.log', 'Honeypot triggered by ' . client_ip() . ' field ' . $field);
        return false;
    }
    return true;
}
