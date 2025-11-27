<?php
// --- src/EmailNotifier.php ---
// Basic email notifier. Uses PHP mail() by default; replace with real SMTP in production.

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

class EmailNotifier
{
    private string $from = 'no-reply@example.com';

    public function __construct(array $opts = [])
    {
        $this->from = $opts['from'] ?? $this->from;
    }

    public function sendBookingEmail(string $toEmail, array $bookingData): bool
    {
        if (empty($toEmail)) {
            return false;
        }

        $subject = 'Your Orvigo Booking: ' . ($bookingData['booking_id'] ?? '');
        $body = "Hello " . ($bookingData['customer']['name'] ?? '') . ",\n\n";
        $body .= "Thanks for booking with " . ORVIGO_NAME . ". Your booking details:\n";
        $body .= "Booking ID: " . ($bookingData['booking_id'] ?? '') . "\n";
        $body .= "Service: " . ($bookingData['service']['category'] ?? '') . "\n";
        $body .= "Date & Time: " . ($bookingData['schedule']['preferred_date'] ?? '') . " " . ($bookingData['schedule']['time_slot'] ?? '') . "\n\n";
        $body .= "We will send confirmation via SMS/WhatsApp.\n\nRegards,\n" . ORVIGO_NAME;

        $headers = 'From: ' . $this->from . '\r\n' . 'Reply-To: ' . ORVIGO_ADMIN_EMAIL . '\r\n' . 'X-Mailer: PHP/' . phpversion();

        $result = @mail($toEmail, $subject, $body, $headers);
        log_message('notifications.log', 'Email to ' . $toEmail . ' result: ' . ($result ? 'sent' : 'failed'));
        return (bool)$result;
    }
}
