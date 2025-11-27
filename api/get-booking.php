<?php
// --- api/get-booking.php ---
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/BookingStore.php';

$bookingId = $_GET['booking_id'] ?? $_POST['booking_id'] ?? '';
$phone = $_GET['phone'] ?? $_POST['phone'] ?? '';

if (empty($bookingId) || empty($phone)) {
    json_response(['success'=>false,'message'=>'booking_id and phone required'],400);
}

$store = new BookingStore();
$booking = $store->getBookingByIdAndPhone($bookingId, $phone);
if (!$booking) {
    json_response(['success'=>false,'message'=>'Booking not found'],404);
}

json_response(['success'=>true,'booking'=>$booking]);
