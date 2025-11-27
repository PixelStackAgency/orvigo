<?php
// --- api/admin-update-booking.php ---
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/BookingStore.php';
session_start();
if (empty($_SESSION['orvigo_admin'])) { header('Location: ../public/admin/login.php'); exit; }

$post = sanitize_input($_POST ?: []);
$bookingId = $post['booking_id'] ?? null;
$status = $post['status'] ?? null;
$notes = $post['internal_notes'] ?? '';

if (!$bookingId || !$status) { header('Location: ../public/admin/index.php?error=1'); exit; }

$store = new BookingStore();
$all = $store->getAllBookings();
$found = null; foreach ($all as $i => $b) { if (($b['booking_id'] ?? '') === $bookingId) { $found = $i; break; } }
if ($found === null) { header('Location: ../public/admin/index.php?error=notfound'); exit; }

$all[$found]['status']['status'] = $status;
$all[$found]['status']['internal_notes'] = $notes;

$fp = fopen(BOOKINGS_FILE,'c+'); if (!$fp) { header('Location: ../public/admin/index.php?error=write'); exit; }
if (!flock($fp, LOCK_EX)) { fclose($fp); header('Location: ../public/admin/index.php?error=lock'); exit; }
ftruncate($fp,0); rewind($fp); fwrite($fp, json_encode($all, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)); fflush($fp); flock($fp, LOCK_UN); fclose($fp);

header('Location: ../public/admin/index.php?success=1'); exit;
