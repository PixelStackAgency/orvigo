<?php
// src/notifiers/TwilioSmsNotifier.php
// Production-ready Twilio SMS/WhatsApp implementation
// Install: composer require twilio/sdk

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers.php';

use Twilio\Rest\Client;

class TwilioSmsNotifier
{
    private string $accountSid;
    private string $authToken;
    private string $twilioPhone;

    public function __construct()
    {
        $this->accountSid = getenv('TWILIO_ACCOUNT_SID') ?: '';
        $this->authToken = getenv('TWILIO_AUTH_TOKEN') ?: '';
        $this->twilioPhone = getenv('TWILIO_PHONE_NUMBER') ?: '';
    }

    public function sendSmsConfirmation(string $toPhone, array $booking): bool
    {
        if (empty($this->accountSid) || empty($this->authToken) || empty($this->twilioPhone)) {
            log_message('notifications.log', 'Twilio credentials not configured for SMS');
            return false;
        }

        try {
            $bookingId = $booking['booking_id'] ?? 'N/A';
            $serviceName = $booking['service']['category'] ?? 'Service';
            $preferredDate = $booking['schedule']['preferred_date'] ?? 'N/A';

            $message = "Orvigo: Your $serviceName booking (ID: $bookingId) is confirmed for $preferredDate. "
                     . "Our technician will contact you shortly. Reply HELP for assistance.";

            $client = new Client($this->accountSid, $this->authToken);
            $sms = $client->messages->create(
                '+91' . $toPhone,
                [
                    'from' => $this->twilioPhone,
                    'body' => $message
                ]
            );

            log_message('notifications.log', "SMS sent to $toPhone (Booking: $bookingId, SID: {$sms->sid})");
            return true;
        } catch (\Throwable $e) {
            log_message('notifications.log', "SMS send failed: {$e->getMessage()}");
            return false;
        }
    }

    public function sendWhatsappConfirmation(string $toPhone, array $booking): bool
    {
        if (empty($this->accountSid) || empty($this->authToken) || empty($this->twilioPhone)) {
            log_message('notifications.log', 'Twilio credentials not configured for WhatsApp');
            return false;
        }

        try {
            $bookingId = $booking['booking_id'] ?? 'N/A';
            $customerName = $booking['customer']['name'] ?? 'Customer';
            $serviceName = $booking['service']['category'] ?? 'Service';

            $message = "Hi $customerName, 👋\n\n"
                     . "Your $serviceName booking (ID: $bookingId) is confirmed with Orvigo! ✓\n\n"
                     . "Our technician will arrive soon. For any queries, reply to this message or call +91-90000-00000\n\n"
                     . "Thank you for choosing Orvigo!";

            $client = new Client($this->accountSid, $this->authToken);
            $msg = $client->messages->create(
                'whatsapp:+91' . $toPhone,
                [
                    'from' => 'whatsapp:' . $this->twilioPhone,
                    'body' => $message
                ]
            );

            log_message('notifications.log', "WhatsApp sent to $toPhone (Booking: $bookingId, SID: {$msg->sid})");
            return true;
        } catch (\Throwable $e) {
            log_message('notifications.log', "WhatsApp send failed: {$e->getMessage()}");
            return false;
        }
    }

    public function sendStatusUpdate(string $toPhone, string $status, string $bookingId): bool
    {
        if (empty($this->accountSid) || empty($this->authToken) || empty($this->twilioPhone)) {
            return false;
        }

        try {
            $message = "Orvigo: Your booking $bookingId status is now: $status";

            $client = new Client($this->accountSid, $this->authToken);
            $sms = $client->messages->create(
                '+91' . $toPhone,
                [
                    'from' => $this->twilioPhone,
                    'body' => $message
                ]
            );

            log_message('notifications.log', "Status SMS sent to $toPhone (Booking: $bookingId)");
            return true;
        } catch (\Throwable $e) {
            log_message('notifications.log', "Status SMS failed: {$e->getMessage()}");
            return false;
        }
    }
}
?>
