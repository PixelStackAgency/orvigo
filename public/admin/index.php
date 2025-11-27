<?php
// --- public/admin/index.php ---
require_once __DIR__ . '/../../src/helpers.php';
require_once __DIR__ . '/../../src/BookingStore.php';
session_start();
if (empty($_SESSION['orvigo_admin'])){ header('Location: login.php'); exit; }
$store = new BookingStore();
$bookings = $store->getAllBookings();
include __DIR__ . '/../_header.php';
?>
<section class="container mt-5">
  <div class="d-flex justify-content-between align-items-center">
    <h1>Admin - Bookings</h1>
    <div>
      <a class="btn btn-outline-secondary" href="logout.php">Logout</a>
    </div>
  </div>
  <div class="mt-4">
    <table class="table table-striped">
      <thead><tr><th>Booking ID</th><th>Name</th><th>Phone</th><th>Service</th><th>Date/Time</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($bookings as $b): ?>
          <tr>
            <td><?php echo h($b['booking_id'] ?? ''); ?></td>
            <td><?php echo h($b['customer']['name'] ?? ''); ?></td>
            <td><?php echo h($b['customer']['phone'] ?? ''); ?></td>
            <td><?php echo h($b['service']['category'] ?? ''); ?></td>
            <td><?php echo h(($b['schedule']['preferred_date'] ?? '') . ' ' . ($b['schedule']['time_slot'] ?? '')); ?></td>
            <td><?php echo h($b['status']['status'] ?? ''); ?></td>
            <td>
              <form style="display:inline-block" method="post" action="../../api/admin-update-booking.php">
                <input type="hidden" name="booking_id" value="<?php echo h($b['booking_id'] ?? ''); ?>">
                <select name="status" class="form-select form-select-sm d-inline-block" style="width:150px; display:inline-block;">
                  <?php $s = $b['status']['status'] ?? 'pending'; $opts=['pending','confirmed','assigned','completed','cancelled']; foreach($opts as $opt): ?>
                    <option value="<?php echo $opt; ?>" <?php if($opt===$s) echo 'selected'; ?>><?php echo ucfirst($opt); ?></option>
                  <?php endforeach; ?>
                </select>
                <input type="text" name="internal_notes" placeholder="Notes" class="form-control form-control-sm d-inline-block" style="width:200px;" value="<?php echo h($b['status']['internal_notes'] ?? ''); ?>">
                <button class="btn btn-sm btn-primary">Update</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php include __DIR__ . '/../_footer.php'; ?>
