<?php

require_once 'includes/auth.php';
require_once 'config/connection.php';
require_once 'includes/functions.php';

$totalCars = $pdo->query(
    "SELECT COUNT(*) FROM cars"
)->fetchColumn();

$totalCustomers = $pdo->query(
    "SELECT COUNT(*) FROM customers"
)->fetchColumn();

$totalRentals = $pdo->query(
    "SELECT COUNT(*) FROM rentals"
)->fetchColumn();

$totalRevenue = $pdo->query(
    "SELECT COALESCE(SUM(total_cost), 0) FROM rentals"
)->fetchColumn();

$activeRentals = $pdo->query(
    "SELECT COUNT(*) FROM rentals WHERE status = 'Active'"
)->fetchColumn();

require_once 'includes/header.php';
?>

<div class="container py-4">

    <div class="mb-4">
        <h2 class="fw-bold">DriveX Reports</h2>
        <p class="text-muted">
            Overview of car rental activities
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Cars</h6>
                    <h2 class="fw-bold">
                        <?= (int)$totalCars ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Customers</h6>
                    <h2 class="fw-bold">
                        <?= (int)$totalCustomers ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Rentals</h6>
                    <h2 class="fw-bold">
                        <?= (int)$totalRentals ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Active Rentals</h6>
                    <h2 class="fw-bold">
                        <?= (int)$activeRentals ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Revenue</h6>
                    <h2 class="fw-bold">
                        <?= formatCurrency($totalRevenue) ?>
                    </h2>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm mt-4">

        <div class="card-body">

            <h5 class="fw-bold">
                Generate Rental Report
            </h5>

            <p class="text-muted">
                Generate a text report containing rental information.
            </p>

            <a
                href="generate_report.php"
                class="btn btn-primary"
            >
                Generate Report
            </a>

        </div>

    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
