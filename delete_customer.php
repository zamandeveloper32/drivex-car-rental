<?php

require_once 'includes/auth.php';
require_once 'config/connection.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('customers.php');
}

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    redirect('customers.php');
}

/* Check rental history */
$check = $pdo->prepare(
    "SELECT COUNT(*) FROM rentals WHERE customer_id = ?"
);

$check->execute([$id]);

if ((int)$check->fetchColumn() > 0) {
    redirect(
        'customers.php?error=' .
        urlencode('This customer has rental records and cannot be deleted.')
    );
}

/* Get document */
$stmt = $pdo->prepare(
    "SELECT document FROM customers WHERE id = ?"
);

$stmt->execute([$id]);

$customer = $stmt->fetch();

if (!$customer) {
    redirect('customers.php');
}

/* Delete customer */
$delete = $pdo->prepare(
    "DELETE FROM customers WHERE id = ?"
);

$delete->execute([$id]);

/* Delete document file */
if (!empty($customer['document'])) {

    $file = 'uploads/customers/' . $customer['document'];

    if (file_exists($file)) {
        unlink($file);
    }
}

redirect('customers.php?deleted=1');
