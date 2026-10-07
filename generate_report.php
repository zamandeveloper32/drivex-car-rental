<?php

require_once 'includes/auth.php';
require_once 'config/connection.php';
require_once 'includes/functions.php';

$reportDir = 'reports/';

if (!is_dir($reportDir)) {
    mkdir($reportDir, 0755, true);
}

$filename = 'rental_report_' . date('Y-m-d_H-i-s') . '.txt';
$filePath = $reportDir . $filename;

$stmt = $pdo->query("
    SELECT
        rentals.id,
        customers.full_name,
        cars.car_name,
        cars.model,
        rentals.rental_date,
        rentals.return_date,
        rentals.duration,
        rentals.total_cost,
        rentals.status
    FROM rentals
    INNER JOIN customers
        ON rentals.customer_id = customers.id
    INNER JOIN cars
        ON rentals.car_id = cars.id
    ORDER BY rentals.id DESC
");

$rentals = $stmt->fetchAll();

$report = "DRIVEX CAR RENTAL MANAGEMENT SYSTEM\n";
$report .= "====================================\n";
$report .= "Rental Report\n";
$report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";

if (!$rentals) {

    $report .= "No rental records found.\n";

} else {

    foreach ($rentals as $rental) {

        $report .= "Rental ID: " . $rental['id'] . "\n";
        $report .= "Customer: " . $rental['full_name'] . "\n";
        $report .= "Car: " . $rental['car_name'] . " " . $rental['model'] . "\n";
        $report .= "Rental Date: " . $rental['rental_date'] . "\n";
        $report .= "Return Date: " . $rental['return_date'] . "\n";
        $report .= "Duration: " . $rental['duration'] . " days\n";
        $report .= "Total Cost: " . number_format($rental['total_cost'], 2) . "\n";
        $report .= "Status: " . $rental['status'] . "\n";
        $report .= "------------------------------------\n";
    }
}

/* Write report */
file_put_contents($filePath, $report);

/* Display generated report */
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo $report;
exit;
