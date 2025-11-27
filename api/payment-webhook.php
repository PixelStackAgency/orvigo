<?php
// --- api/payment-webhook.php ---
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/PaymentGateway.php';

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

$pg = new PaymentGateway();
if (!$pg->verifyRazorpaySignature($payload, $signature)) {
    http_response_code(400);
    echo 'invalid signature';
    exit;
}

$data = json_decode($payload, true);
if (!is_array($data)) {
    http_response_code(400);
    echo 'invalid payload';
    exit;
}

// Example: update booking payment status by receipt/order_id
$orderId = $data['payload']['payment']['entity']['order_id'] ?? null;
$paymentId = $data['payload']['payment']['entity']['id'] ?? null;
$status = $data['payload']['payment']['entity']['status'] ?? null;

if (!$orderId) {
    http_response_code(400);
    echo 'missing order_id';
    exit;
}

// Find booking with matching razorpay_order_id and update
$file = BOOKINGS_FILE;
$fp = fopen($file, 'c+');
if (!$fp) { http_response_code(500); echo 'file error'; exit; }
try {
    if (!flock($fp, LOCK_EX)) { http_response_code(500); echo 'lock error'; exit; }
    $raw = stream_get_contents($fp);
    $arr = json_decode($raw, true) ?: [];
    $changed = false;
    foreach ($arr as &$b) {
        if (isset($b['payment']['razorpay_order_id']) && $b['payment']['razorpay_order_id'] === $orderId) {
            $b['payment']['status'] = $status === 'captured' ? 'success' : $status;
            $b['payment']['transaction_id'] = $paymentId;
            $changed = true;
            break;
        }
    }
    if ($changed) {
        ftruncate($fp,0); rewind($fp); fwrite($fp,json_encode($arr,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)); fflush($fp);
    }
    flock($fp, LOCK_UN);
} finally { fclose($fp); }

http_response_code(200);
echo 'ok';
