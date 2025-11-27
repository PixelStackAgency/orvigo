<?php
// --- api/confirm-payment.php ---
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/BookingStore.php';
require_once __DIR__ . '/../src/PaymentGateway.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) json_response(['success'=>false,'message'=>'Invalid payload'],400);

$bookingId = $data['booking_id'] ?? null;
$razorpay_payment_id = $data['razorpay_payment_id'] ?? null;
$razorpay_order_id = $data['razorpay_order_id'] ?? null;
$razorpay_signature = $data['razorpay_signature'] ?? '';

if (!$bookingId || !$razorpay_payment_id || !$razorpay_order_id) json_response(['success'=>false,'message'=>'Missing fields'],400);

$pg = new PaymentGateway();
// Verify signature (client-sent). In production prefer webhook verification.
if (!$pg->verifyRazorpaySignature(json_encode(['razorpay_payment_id'=>$razorpay_payment_id,'razorpay_order_id'=>$razorpay_order_id]), $razorpay_signature)) {
    log_message('notifications.log', 'Payment signature verification failed for ' . $razorpay_order_id);
    json_response(['success'=>false,'message'=>'Invalid signature'],400);
}

$store = new BookingStore();
$all = $store->getAllBookings();
$foundIndex = null;
foreach ($all as $i => $b) {
    if (($b['booking_id'] ?? '') === $bookingId) { $foundIndex = $i; break; }
}
if ($foundIndex === null) json_response(['success'=>false,'message'=>'Booking not found'],404);

$all[$foundIndex]['payment']['status'] = 'success';
$all[$foundIndex]['payment']['transaction_id'] = $razorpay_payment_id;
$all[$foundIndex]['payment']['razorpay_order_id'] = $razorpay_order_id;
// Mark booking as confirmed when payment succeeds
$all[$foundIndex]['status']['status'] = 'confirmed';

// Log the confirmation for audit
log_message('notifications.log', 'Payment confirmed via confirm-payment for booking ' . $bookingId . ' payment ' . $razorpay_payment_id);

$fp = fopen(BOOKINGS_FILE,'c+');
if (!$fp) json_response(['success'=>false,'message'=>'Storage error'],500);
if (!flock($fp, LOCK_EX)) { fclose($fp); json_response(['success'=>false,'message'=>'Lock error'],500); }
ftruncate($fp,0); rewind($fp); fwrite($fp, json_encode($all, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)); fflush($fp); flock($fp, LOCK_UN); fclose($fp);

log_message('notifications.log', 'Payment confirmed for booking ' . $bookingId . ' txn ' . $razorpay_payment_id);
json_response(['success'=>true]);
