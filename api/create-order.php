<?php
// --- api/create-order.php ---
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/BookingStore.php';
require_once __DIR__ . '/../src/PaymentGateway.php';

// Expect JSON payload with booking_id and amountPaise (optional)
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) json_response(['success'=>false,'message'=>'Invalid payload'],400);

$bookingId = $data['booking_id'] ?? null;
$amountPaise = isset($data['amountPaise']) ? (int)$data['amountPaise'] : 10000; // placeholder

if (empty($bookingId)) json_response(['success'=>false,'message'=>'booking_id required'],400);

$store = new BookingStore();
$all = $store->getAllBookings();
$found = null;
foreach ($all as $i => $b) {
    if (isset($b['booking_id']) && $b['booking_id'] === $bookingId) {
        $found = ['index'=>$i,'booking'=>$b];
        break;
    }
}
if (!$found) json_response(['success'=>false,'message'=>'Booking not found'],404);

$pg = new PaymentGateway();
try {
    $order = $pg->createRazorpayOrder($found['booking'], $amountPaise);
    // Persist razorpay_order_id into booking
    $all[$found['index']]['payment']['razorpay_order_id'] = $order['id'] ?? $order['order_id'] ?? null;
    $all[$found['index']]['payment']['amount'] = $order['amount'] ?? $amountPaise;
    // Write back
    $fp = fopen(BOOKINGS_FILE,'c+');
    if (!$fp) throw new RuntimeException('Storage open failed');
    if (!flock($fp, LOCK_EX)) throw new RuntimeException('Lock failed');
    ftruncate($fp,0); rewind($fp); fwrite($fp, json_encode($all, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)); fflush($fp); flock($fp, LOCK_UN); fclose($fp);

    json_response(['success'=>true,'order'=>$order]);
} catch (Throwable $e) {
    log_message('notifications.log','create-order failed: '.$e->getMessage());
    json_response(['success'=>false,'message'=>'Payment gateway error'],500);
}
