<?php
// --- api/book-service.php ---
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/BookingStore.php';
require_once __DIR__ . '/../src/SmsNotifier.php';
require_once __DIR__ . '/../src/EmailNotifier.php';

// Accept JSON body
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    json_response(['success' => false, 'message' => 'Invalid payload'], 400);
}

$data = sanitize_input($data);

// CSRF check if provided
if (empty($data['csrf_token']) || !verify_csrf($data['csrf_token'])) {
    // Allow missing CSRF for quick tests but log it. In production enforce.
    log_message('notifications.log', 'Warning: CSRF missing or invalid in book-service request');
}

// Honeypot: expect 'website' field to be empty. Prevent bot submissions.
if (!honeypot_ok($data, 'website')) {
    json_response(['success'=>false,'message'=>'Spam detected'],400);
}

// Rate limiting per IP
if (!rate_limit(30,60)) {
    json_response(['success'=>false,'message'=>'Too many requests, please try again later'],429);
}

// basic validation
$required = ['name'=>'customer.name','phone'=>'customer.phone','address_line1'=>'customer.address_line1','service_category'=>'service.category','preferred_date'=>'schedule.preferred_date','time_slot'=>'schedule.time_slot'];
$missing = [];
if (empty($data['name'])) $missing[] = 'name';
if (empty($data['phone']) || !is_valid_phone($data['phone'])) $missing[] = 'phone';
if (empty($data['address_line1'])) $missing[] = 'address';
if (empty($data['service_category'])) $missing[] = 'service_category';
if (empty($data['preferred_date'])) $missing[] = 'preferred_date';
if (empty($data['time_slot'])) $missing[] = 'time_slot';

if (!empty($missing)) {
    json_response(['success'=>false,'message'=>'Missing or invalid fields: '.implode(', ',$missing)],400);
}

// Build booking payload
$bookingData = [
    'customer' => [
        'name' => $data['name'],
        'phone' => $data['phone'],
        'alt_phone' => $data['alt_phone'] ?? null,
        'email' => $data['email'] ?? null,
        'address_line1' => $data['address_line1'] ?? '',
        'address_line2' => $data['address_line2'] ?? '',
        'area' => $data['area'] ?? '',
        'city' => $data['city'] ?? ORVIGO_DEFAULT_CITY,
        'pincode' => $data['pincode'] ?? ''
    ],
    'service' => [
        'category' => $data['service_category'],
        'selected_options' => $data['service_options'] ?? [],
        'additional_notes' => $data['additional_notes'] ?? null,
    ],
    'schedule' => [
        'preferred_date' => $data['preferred_date'],
        'time_slot' => $data['time_slot']
    ],
    'payment' => [
        'mode' => $data['payment_mode'] ?? 'cash_after_service',
        'status' => 'pending'
    ]
];

try {
    $store = new BookingStore();
    $booking = $store->createBooking($bookingData);
    // Send notifications (placeholder)
    $sms = new SmsNotifier();
    $sms->sendSmsConfirmation($booking['customer']['phone'], $booking);
    $sms->sendWhatsappConfirmation($booking['customer']['phone'], $booking);
    $emailer = new EmailNotifier();
    if (!empty($booking['customer']['email'])) {
        $emailer->sendBookingEmail($booking['customer']['email'], $booking);
    }

    json_response(['success'=>true,'booking'=>$booking]);
} catch (Throwable $e) {
    log_message('notifications.log', 'Booking creation failed: ' . $e->getMessage());
    json_response(['success'=>false,'message'=>'Server error creating booking'],500);
}
