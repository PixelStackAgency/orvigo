<?php
// --- public/_header.php ---
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/helpers.php';
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo h(ORVIGO_NAME . ' — Home Appliance & Electronics Repair in ' . ORVIGO_DEFAULT_CITY); ?></title>
  <meta name="description" content="<?php echo h(ORVIGO_DESCRIPTION); ?>">
  <meta property="og:title" content="Orvigo — Home Appliance & Electronics Doorstep Repair">
  <meta property="og:description" content="<?php echo h(ORVIGO_DESCRIPTION); ?>">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="en_IN">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- AOS for simple scroll animations -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <!-- Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="stylesheet" href="assets/css/animations.css">

  <!-- Meta tags for environment configuration -->
  <meta name="razorpay-key" content="<?php echo defined("RAZORPAY_KEY_ID") ? RAZORPAY_KEY_ID : ""; ?>">

  <script>
    // Initialize Orvigo config before loading other scripts
    window.ORVIGO = window.ORVIGO || {};
    window.ORVIGO.baseUrl = '<?php echo orvigo_base_url(); ?>';
    window.ORVIGO.RAZORPAY_KEY_ID = '<?php echo defined("RAZORPAY_KEY_ID") ? RAZORPAY_KEY_ID : ""; ?>';
  </script>
  <script src="config.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/img/logo.svg" alt="Orvigo logo" width="36" height="36"> 
      <span class="ms-2 fw-bold">Orvigo</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navmenu">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="how-it-works.php">How it Works</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="faq.php">FAQ</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        <li class="nav-item ms-2"><a class="btn btn-primary" href="services.php#book">Book Service</a></li>
      </ul>
    </div>
  </div>
</nav>

<main class="pt-5">
