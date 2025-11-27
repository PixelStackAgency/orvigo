<?php
// --- src/PaymentGateway.php ---
// Skeleton Razorpay/UPI integration helpers. Fill in API keys in config.php.

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

class PaymentGateway
{
    private string $keyId;
    private string $keySecret;

    public function __construct()
    {
        $this->keyId = RAZORPAY_KEY_ID;
        $this->keySecret = RAZORPAY_KEY_SECRET;
    }

    public function createRazorpayOrder(array $bookingData, int $amountPaise = 10000): array
    {
        // amountPaise: amount in paise. Default 10000 (i.e. Rs.100) as placeholder.
        $orderData = [
            'amount' => $amountPaise,
            'currency' => 'INR',
            'receipt' => $bookingData['booking_id'] ?? generate_booking_id('PAY'),
            'payment_capture' => 1,
        ];

        // If keys are not configured, simulate an order so frontend can be tested.
        if (empty($this->keyId) || strpos($this->keyId, 'rzp_test_') === 0 && $this->keySecret === 'XXXXXXXXXXXXXXXXXXXXXXXX') {
            log_message('notifications.log', 'Razorpay order simulated: ' . json_encode($orderData));
            return [
                'id' => 'order_sim_' . bin2hex(random_bytes(6)),
                'amount' => $orderData['amount'],
                'currency' => $orderData['currency'],
                'receipt' => $orderData['receipt'],
                'status' => 'created',
            ];
        }

        // Real API call to Razorpay
        $url = 'https://api.razorpay.com/v1/orders';
        $ch = curl_init($url);
        $payload = json_encode($orderData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->keyId . ':' . $this->keySecret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $resp = curl_exec($ch);
        $errno = curl_errno($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($errno) {
            log_message('notifications.log', 'Razorpay create order error: ' . $err);
            throw new RuntimeException('Payment gateway error');
        }
        $data = json_decode($resp, true);
        if (!is_array($data) || empty($data['id'])) {
            log_message('notifications.log', 'Razorpay create order unexpected response: ' . $resp);
            throw new RuntimeException('Payment gateway error');
        }
        return $data;
    }

    public function verifyRazorpaySignature(string $payload, string $signature): bool
    {
        // Verify webhook signature using RAZORPAY_WEBHOOK_SECRET from config
        $secret = defined('RAZORPAY_WEBHOOK_SECRET') ? RAZORPAY_WEBHOOK_SECRET : '';
        if (empty($secret)) {
            // In test mode we accept the signature but log warning
            log_message('notifications.log', 'Warning: webhook secret not configured; skipping signature verification');
            return true;
        }
        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }
}
