<?php
// src/notifiers/SendGridEmailNotifier.php
// Production-ready SendGrid email implementation
// Install: composer require sendgrid/sendgrid php-http/guzzle7-adapter

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers.php';

class SendGridEmailNotifier
{
    private string $apiKey;
    private string $fromEmail;

    public function __construct()
    {
        $this->apiKey = getenv('SENDGRID_API_KEY') ?: '';
        $this->fromEmail = ORVIGO_ADMIN_EMAIL;
    }

    public function sendBookingConfirmation(string $toEmail, array $booking): bool
    {
        if (empty($this->apiKey)) {
            log_message('notifications.log', 'SendGrid API key not configured');
            return false;
        }

        $bookingId = $booking['booking_id'] ?? 'N/A';
        $serviceName = $booking['service']['category'] ?? 'Service';
        $preferredDate = $booking['schedule']['preferred_date'] ?? 'N/A';
        $timeSlot = $booking['schedule']['time_slot'] ?? 'N/A';
        $customerName = $booking['customer']['name'] ?? 'Customer';

        $subject = 'Booking Confirmation - Orvigo Service';
        $htmlContent = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { background: white; padding: 20px; }
        .footer { background: #f0f0f0; padding: 10px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Orvigo</h1>
            <p>Your Service Booking Confirmation</p>
        </div>
        <div class="content">
            <p>Dear $customerName,</p>
            <p>Thank you for booking with Orvigo! Your service request has been confirmed.</p>
            
            <h3>Booking Details</h3>
            <ul>
                <li><strong>Booking ID:</strong> $bookingId</li>
                <li><strong>Service:</strong> $serviceName</li>
                <li><strong>Scheduled Date:</strong> $preferredDate</li>
                <li><strong>Time Slot:</strong> $timeSlot</li>
            </ul>
            
            <h3>What's Next?</h3>
            <p>Our technician will contact you shortly to confirm the appointment. You may also track your booking using your Booking ID.</p>
            
            <h3>Need Help?</h3>
            <p>Contact us at <strong>ORVIGO_CONTACT_PHONE</strong> or reply to this email.</p>
        </div>
        <div class="footer">
            <p>&copy; 2025 Orvigo. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

        try {
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom($this->fromEmail, 'Orvigo Support');
            $email->setSubject($subject);
            $email->addTo($toEmail, $customerName);
            $email->addContent('text/html', $htmlContent);

            $sendgrid = new \SendGrid\SendGrid($this->apiKey);
            $response = $sendgrid->send($email);

            if ($response->statusCode() === 202) {
                log_message('notifications.log', "Email sent to $toEmail (Booking: $bookingId)");
                return true;
            } else {
                log_message('notifications.log', "SendGrid error {$response->statusCode()}: {$response->body()}");
                return false;
            }
        } catch (\Throwable $e) {
            log_message('notifications.log', "Email send exception: {$e->getMessage()}");
            return false;
        }
    }

    public function sendAdminNotification(string $subject, string $message): bool
    {
        if (empty($this->apiKey)) {
            log_message('notifications.log', 'SendGrid API key not configured for admin email');
            return false;
        }

        try {
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom($this->fromEmail, 'Orvigo System');
            $email->setSubject('[Orvigo Admin] ' . $subject);
            $email->addTo($this->fromEmail);
            $email->addContent('text/plain', $message);

            $sendgrid = new \SendGrid\SendGrid($this->apiKey);
            $response = $sendgrid->send($email);

            return $response->statusCode() === 202;
        } catch (\Throwable $e) {
            log_message('notifications.log', "Admin email exception: {$e->getMessage()}");
            return false;
        }
    }
}
?>
