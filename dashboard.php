<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/connection.php";

$pageTitle = "Dashboard";

function tableCount(mysqli $conn, string $table): int
{
    $allowed = ['cars', 'customers', 'rentals'];
    if (!in_array($table, $allowed, true)) {
        return 0;
    }

    $result = $conn->query("SELECT COUNT(*) AS total FROM `$table`");
    return (int)$result->fetch_assoc()['total'];
}

$totalCars = tableCount($conn, "cars");
$totalCustomers = tableCount($conn, "customers");
$totalRentals = tableCount($conn, "rentals");

$availableCars = 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM cars WHERE availability = 'Available'");
if ($result) {
    $availableCars = (int)$result->fetch_assoc()['total'];
}

$totalRevenue = 0;
$result = $conn->query("SELECT COALESCE(SUM(total_cost), 0) AS revenue FROM rentals");
if ($result) {
    $totalRevenue = (float)$result->fetch_assoc()['revenue'];
}

require_once __DIR__ . "/includes/header.php";
?>

<div class="welcome-card p-4 p-md-5 mb-4">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <p class="text-uppercase small fw-bold opacity-75 mb-2">Administrator Dashboard</p>
            <h1 class="display-6 fw-bold mb-2">
                Welcome back, <?= e($_SESSION['admin_name'] ?? 'Administrator') ?>.
            </h1>
            <p class="mb-0 opacity-75">
                Manage vehicles, customers, rentals and reports from one place.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end">
            <span class="badge bg-light text-dark p-3">
                <?= date("l, d F Y") ?>
            </span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card p-4 h-100">
            <div class="text-secondary small fw-semibold">TOTAL CARS</div>
            <div class="stat-number"><?= $totalCars ?></div>
            <div class="text-success small"><?= $availableCars ?> currently available</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card p-4 h-100">
            <div class="text-secondary small fw-semibold">CUSTOMERS</div>
            <div class="stat-number"><?= $totalCustomers ?></div>
            <div class="text-secondary small">Registered customers</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card p-4 h-100">
            <div class="text-secondary small fw-semibold">RENTALS</div>
            <div class="stat-number"><?= $totalRentals ?></div>
            <div class="text-secondary small">Rental records</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card p-4 h-100">
            <div class="text-secondary small fw-semibold">REVENUE</div>
            <div class="stat-number" style="font-size:1.55rem;">
                <?= formatCurrency($totalRevenue) ?>
            </div>
            <div class="text-secondary small">Recorded rental revenue</div>
        </div>
    </div>
</div>

<div class="table-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h5 fw-bold mb-1">DriveX Control Center</h2>
            <p class="text-secondary small mb-0">Choose a module to continue.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <a href="cars.php" class="btn btn-outline-primary w-100 py-3">🚗 Manage Cars</a>
        </div>
        <div class="col-md-4">
            <a href="customers.php" class="btn btn-outline-primary w-100 py-3">👥 Manage Customers</a>
        </div>
        <div class="col-md-4">
            <a href="rentals.php" class="btn btn-outline-primary w-100 py-3">📋 Manage Rentals</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
