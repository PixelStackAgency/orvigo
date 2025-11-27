<?php
// --- api/contact-submit.php ---
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/EmailNotifier.php';

// Accept POST from contact form
// Honeypot and rate limit checks for contact form
$post = sanitize_input($_POST ?: []);
if (!honeypot_ok($post, 'website')) {
    header('Location: ../public/contact.php?error=spam'); exit;
}
if (!rate_limit(20,60)) {
    header('Location: ../public/contact.php?error=ratelimit'); exit;
}
if (empty($post['csrf_token']) || !verify_csrf($post['csrf_token'])) {
    // allow but log
    log_message('notifications.log', 'Contact submit: CSRF missing or invalid');
}

$name = $post['name'] ?? '';
$phone = $post['phone'] ?? '';
$email = $post['email'] ?? '';
$subject = $post['subject'] ?? 'Contact form inquiry';
$message = $post['message'] ?? '';

if (empty($name) || empty($phone) || empty($message)) {
    header('Location: ../public/contact.php?error=1');
    exit;
}

$notifier = new EmailNotifier();
$body = "Contact form message:\n\nName: $name\nPhone: $phone\nEmail: $email\nSubject: $subject\nMessage:\n$message";

// Send to admin
$sent = $notifier->sendBookingEmail(ORVIGO_ADMIN_EMAIL, ['customer'=>['name'=>$name,'email'=>$email],'booking_id'=>'CONTACT-'.time(),'service'=>['category'=>'contact'],'schedule'=>[]]);
log_message('notifications.log', 'Contact form from ' . $phone . ' sent: ' . ($sent ? 'yes' : 'no'));

header('Location: ../public/contact.php?success=1');
exit;
