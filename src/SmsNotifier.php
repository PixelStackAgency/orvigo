<?php
// --- src/SmsNotifier.php ---
// Placeholder SMS/WhatsApp notifier. Configure gateway URL, auth in config.php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

class SmsNotifier
{
    private string $apiBase = 'https://api.placeholder-sms-gateway.example';
    private string $authToken = '';

    public function __construct(array $opts = [])
    {
        // Load options from config later
        $this->authToken = $opts['auth_token'] ?? '';
    }

    public function sendSmsConfirmation(string $phone, array $bookingData): bool
    {
        $msg = sprintf("Hello %s, your booking %s for %s is confirmed for %s %s. - %s",
            $bookingData['customer']['name'] ?? '',
            $bookingData['booking_id'] ?? '',
            ucfirst($bookingData['service']['category'] ?? ''),
            $bookingData['schedule']['preferred_date'] ?? '',
            $bookingData['schedule']['time_slot'] ?? '',
            ORVIGO_NAME
        );

        // Placeholder: send via cURL to gateway. Implement real gateway here
        $payload = [
            'to' => $phone,
            'message' => $msg,
        ];

        // Log attempt
        log_message('notifications.log', 'SMS to ' . $phone . ': ' . $msg);

        // Return true to indicate we attempted; in real code check response
        return true;
    }

    public function sendWhatsappConfirmation(string $phone, array $bookingData): bool
    {
        $msg = sprintf("Hi %s, your Orvigo booking %s for %s is scheduled on %s %s. Thank you!",
            $bookingData['customer']['name'] ?? '',
            $bookingData['booking_id'] ?? '',
            ucfirst($bookingData['service']['category'] ?? ''),
            $bookingData['schedule']['preferred_date'] ?? '',
            $bookingData['schedule']['time_slot'] ?? ''
        );

        log_message('notifications.log', 'WhatsApp to ' . $phone . ': ' . $msg);
        return true;
    }
}
