<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . "/functions.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'DriveX') ?> | DriveX</title>

    <!-- Bootstrap CDN for the UI layer -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DriveX custom styling -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark drivex-navbar">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <span class="brand-mark">D</span> DriveX
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="cars.php">Cars</a></li>
                <li class="nav-item"><a class="nav-link" href="customers.php">Customers</a></li>
                <li class="nav-item"><a class="nav-link" href="rentals.php">Rentals</a></li>
                <li class="nav-item"><a class="nav-link" href="generate_report.php">Reports</a></li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small">
                    <?= e($_SESSION['admin_name'] ?? 'Administrator') ?>
                </span>
                <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</nav>

<main class="container-fluid px-4 py-4">
