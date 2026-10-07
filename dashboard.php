<?php

require_once 'includes/auth.php';
require_once 'config/connection.php';
require_once 'includes/functions.php';

$totalCars = $pdo->query(
    "SELECT COUNT(*) FROM cars"
)->fetchColumn();

$availableCars = $pdo->query(
    "SELECT COUNT(*) FROM cars WHERE availability = 'Available'"
)->fetchColumn();

$totalCustomers = $pdo->query(
    "SELECT COUNT(*) FROM customers"
)->fetchColumn();

$totalRentals = $pdo->query(
    "SELECT COUNT(*) FROM rentals"
)->fetchColumn();

$activeRentals = $pdo->query(
    "SELECT COUNT(*) FROM rentals WHERE status = 'Active'"
)->fetchColumn();

$totalRevenue = $pdo->query(
    "SELECT COALESCE(SUM(total_cost), 0) FROM rentals"
)->fetchColumn();

require_once 'includes/header.php';
?>

<div class="container py-4">

    <div class="mb-4">
        <h2 class="fw-bold">
            Welcome to DriveX
        </h2>

        <p class="text-muted">
            Car Rental Management Dashboard
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
                    <small class="text-success">
                        <?= (int)$availableCars ?> Available
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-muted">Customers</h6>
                    <h2 class="fw-bold">
                        <?= (int)$totalCustomers ?>
                    </h2>
                    <a href="customers.php">
                        Manage Customers
                    </a>
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
                    <small class="text-warning">
                        <?= (int)$activeRentals ?> Active
                    </small>
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

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">

                    <h6 class="text-muted">
                        Quick Actions
                    </h6>

                    <a
                        href="add_car.php"
                        class="btn btn-primary btn-sm me-2"
                    >
                        Add Car
                    </a>

                    <a
                        href="add_customer.php"
                        class="btn btn-success btn-sm me-2"
                    >
                        Add Customer
                    </a>

                    <a
                        href="add_rental.php"
                        class="btn btn-dark btn-sm"
                    >
                        New Rental
                    </a>

                </div>
            </div>
        </div>

    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
