<?php

require_once 'includes/auth.php';
require_once 'config/connection.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('rentals.php');
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    redirect('rentals.php');
}

/* Get rental information */
$stmt = $pdo->prepare(
    "SELECT car_id, status FROM rentals WHERE id = ?"
);

$stmt->execute([$id]);

$rental = $stmt->fetch();

if (!$rental) {
    redirect('rentals.php');
}

/* Delete rental */
$delete = $pdo->prepare(
    "DELETE FROM rentals WHERE id = ?"
);

$delete->execute([$id]);

/* Make car available again */
$updateCar = $pdo->prepare(
    "UPDATE cars
     SET availability = 'Available'
     WHERE id = ?"
);

$updateCar->execute([
    $rental['car_id']
]);

redirect('rentals.php?deleted=1');
