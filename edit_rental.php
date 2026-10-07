<?php

require_once 'includes/auth.php';
require_once 'config/connection.php';
require_once 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    redirect('rentals.php');
}

$stmt = $pdo->prepare("SELECT * FROM rentals WHERE id = ?");
$stmt->execute([$id]);
$rental = $stmt->fetch();

if (!$rental) {
    redirect('rentals.php');
}

$customers = $pdo
    ->query("SELECT id, full_name FROM customers ORDER BY full_name")
    ->fetchAll();

$cars = $pdo
    ->query("
        SELECT id, car_name, model, rental_price, availability
        FROM cars
        ORDER BY car_name
    ")
    ->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $customerId = (int)($_POST['customer_id'] ?? 0);
    $carId = (int)($_POST['car_id'] ?? 0);
    $rentalDate = $_POST['rental_date'] ?? '';
    $returnDate = $_POST['return_date'] ?? '';
    $status = $_POST['status'] ?? 'Active';

    if ($customerId <= 0) {
        $errors[] = "Please select a customer.";
    }

    if ($carId <= 0) {
        $errors[] = "Please select a car.";
    }

    if (!$rentalDate || !$returnDate) {
        $errors[] = "Both dates are required.";
    }

    if ($returnDate < $rentalDate) {
        $errors[] = "Return date cannot be before rental date.";
    }

    if (!in_array($status, ['Active', 'Completed'], true)) {
        $errors[] = "Invalid rental status.";
    }

    if (!$errors) {

        $carStmt = $pdo->prepare(
            "SELECT rental_price FROM cars WHERE id = ?"
        );

        $carStmt->execute([$carId]);
        $car = $carStmt->fetch();

        if (!$car) {

            $errors[] = "Selected car does not exist.";

        } else {

            $duration = calculateRentalDays(
                $rentalDate,
                $returnDate
            );

            $totalCost =
                $duration * (float)$car['rental_price'];

            $update = $pdo->prepare(
                "UPDATE rentals
                 SET customer_id = ?,
                     car_id = ?,
                     rental_date = ?,
                     return_date = ?,
                     duration = ?,
                     total_cost = ?,
                     status = ?
                 WHERE id = ?"
            );

            $update->execute([
                $customerId,
                $carId,
                $rentalDate,
                $returnDate,
                $duration,
                $totalCost,
                $status,
                $id
            ]);

            /* Update car availability */
            if ($status === 'Completed') {

                $carUpdate = $pdo->prepare(
                    "UPDATE cars
                     SET availability = 'Available'
                     WHERE id = ?"
                );

            } else {

                $carUpdate = $pdo->prepare(
                    "UPDATE cars
                     SET availability = 'Rented'
                     WHERE id = ?"
                );
            }

            $carUpdate->execute([$carId]);

            redirect('rentals.php');
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container py-4">

    <h2 class="fw-bold mb-4">Edit Rental</h2>

    <?php if ($errors): ?>

        <div class="alert alert-danger">

            <ul class="mb-0">

                <?php foreach ($errors as $error): ?>

                    <li><?= e($error) ?></li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>

    <div class="card shadow-sm">

        <div class="card-body">

            <form method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Customer
                        </label>

                        <select
                            name="customer_id"
                            class="form-select"
                            required
                        >

                            <?php foreach ($customers as $customer): ?>

                                <option
                                    value="<?= (int)$customer['id'] ?>"
                                    <?= (
                                        (int)$rental['customer_id']
                                        ===
                                        (int)$customer['id']
                                    ) ? 'selected' : '' ?>
                                >
                                    <?= e($customer['full_name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Car
                        </label>

                        <select
                            name="car_id"
                            class="form-select"
                            required
                        >

                            <?php foreach ($cars as $car): ?>

                                <option
                                    value="<?= (int)$car['id'] ?>"
                                    <?= (
                                        (int)$rental['car_id']
                                        ===
                                        (int)$car['id']
                                    ) ? 'selected' : '' ?>
                                >
                                    <?= e($car['car_name']) ?>
                                    -
                                    <?= e($car['model']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Rental Date
                        </label>

                        <input
                            type="date"
                            name="rental_date"
                            class="form-control"
                            required
                            value="<?= e($rental['rental_date']) ?>"
                        >

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Return Date
                        </label>

                        <input
                            type="date"
                            name="return_date"
                            class="form-control"
                            required
                            value="<?= e($rental['return_date']) ?>"
                        >

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option
                                value="Active"
                                <?= $rental['status'] === 'Active'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Active
                            </option>

                            <option
                                value="Completed"
                                <?= $rental['status'] === 'Completed'
                                    ? 'selected'
                                    : '' ?>
                            >
                                Completed
                            </option>

                        </select>

                    </div>

                </div>

                <button class="btn btn-primary">
                    Update Rental
                </button>

                <a
                    href="rentals.php"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </form>

        </div>

    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
