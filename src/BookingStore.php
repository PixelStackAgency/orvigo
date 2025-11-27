<?php
// --- src/BookingStore.php ---
// Handles CRUD operations on storage/bookings.json with safe locking

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

class BookingStore
{
    private string $file;

    public function __construct(string $file = BOOKINGS_FILE)
    {
        $this->file = $file;
        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([]));
        }
    }

    public function getAllBookings(): array
    {
        $data = file_get_contents($this->file);
        $arr = json_decode($data, true);
        return is_array($arr) ? $arr : [];
    }

    public function getBookingByIdAndPhone(string $bookingId, string $phone): ?array
    {
        $all = $this->getAllBookings();
        foreach ($all as $b) {
            if (isset($b['booking_id']) && $b['booking_id'] === $bookingId) {
                $storedPhone = preg_replace('/[^0-9]/', '', $b['customer']['phone'] ?? '');
                $reqPhone = preg_replace('/[^0-9]/', '', $phone);
                if ($storedPhone !== '' && substr($storedPhone, -10) === substr($reqPhone, -10)) {
                    return $b;
                }
            }
        }
        return null;
    }

    public function createBooking(array $data): array
    {
        // Prepare booking object
        $category = $data['service']['category'] ?? 'GEN';
        $booking = [
            'booking_id' => generate_booking_id($category),
            'created_at' => (new DateTime('now', new DateTimeZone('Asia/Kolkata')))->format(DateTime::ATOM),
            'customer' => $data['customer'] ?? [],
            'service' => $data['service'] ?? [],
            'schedule' => $data['schedule'] ?? [],
            'payment' => $data['payment'] ?? ['mode' => 'cash_after_service', 'status' => 'pending'],
            'status' => ['status' => 'pending', 'internal_notes' => ''],
        ];

        // Persist with flock
        $fp = fopen($this->file, 'c+');
        if (!$fp) {
            throw new RuntimeException('Unable to open bookings storage file.');
        }

        try {
            if (!flock($fp, LOCK_EX)) {
                throw new RuntimeException('Unable to lock bookings storage file.');
            }
            // Read current content
            $raw = stream_get_contents($fp);
            $arr = json_decode($raw, true);
            if (!is_array($arr)) {
                $arr = [];
            }
            $arr[] = $booking;
            // Rewind and truncate
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            fflush($fp);
            flock($fp, LOCK_UN);
        } finally {
            fclose($fp);
        }

        return $booking;
    }
}
