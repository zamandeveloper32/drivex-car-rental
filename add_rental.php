<?php

require_once 'includes/auth.php';
require_once 'config/connection.php';
require_once 'includes/functions.php';

$errors = [];

$customers = $pdo
    ->query("SELECT id, full_name FROM customers ORDER BY full_name")
    ->fetchAll();

$cars = $pdo
    ->query("
        SELECT id, car_name, model, rental_price
        FROM cars
        WHERE availability = 'Available'
        ORDER BY car_name
    ")
    ->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $customerId = (int)($_POST['customer_id'] ?? 0);
    $carId = (int)($_POST['car_id'] ?? 0);
    $rentalDate = $_POST['rental_date'] ?? '';
    $returnDate = $_POST['return_date'] ?? '';

    if ($customerId <= 0) {
        $errors[] = "Please select a customer.";
    }

    if ($carId <= 0) {
        $errors[] = "Please select a car.";
    }

    if (!$rentalDate || !$returnDate) {
        $errors[] = "Please select both dates.";
    }

    if ($rentalDate && $returnDate && $returnDate < $rentalDate) {
        $errors[] = "Return date cannot be before rental date.";
    }

    if (!$errors) {

        $carStmt = $pdo->prepare(
            "SELECT rental_price, availability
             FROM cars
             WHERE id = ?"
        );

        $carStmt->execute([$carId]);
        $car = $carStmt->fetch();

        if (!$car || $car['availability'] !== 'Available') {
            $errors[] = "Selected car is not available.";
        } else {

            $duration = calculateRentalDays(
                $rentalDate,
                $returnDate
            );

            $totalCost = $duration * (float)$car['rental_price'];

            $stmt = $pdo->prepare(
                "INSERT INTO rentals
                (customer_id, car_id, rental_date, return_date,
                 duration, total_cost, status)
                VALUES (?, ?, ?, ?, ?, ?, 'Active')"
            );

            $stmt->execute([
                $customerId,
                $carId,
                $rentalDate,
                $returnDate,
                $duration,
                $totalCost
            ]);

            $updateCar = $pdo->prepare(
                "UPDATE cars
                 SET availability = 'Rented'
                 WHERE id = ?"
            );

            $updateCar->execute([$carId]);

            redirect('rentals.php');
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container py-4">

    <h2 class="fw-bold mb-4">Create New Rental</h2>

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

                            <option value="">
                                Select Customer
                            </option>

                            <?php foreach ($customers as $customer): ?>

                                <option
                                    value="<?= (int)$customer['id'] ?>"
                                    <?= ((int)($_POST['customer_id'] ?? 0) === (int)$customer['id']) ? 'selected' : '' ?>
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

                            <option value="">
                                Select Available Car
                            </option>

                            <?php foreach ($cars as $car): ?>

                                <option
                                    value="<?= (int)$car['id'] ?>"
                                    <?= ((int)($_POST['car_id'] ?? 0) === (int)$car['id']) ? 'selected' : '' ?>
                                >
                                    <?= e($car['car_name']) ?>
                                    -
                                    <?= e($car['model']) ?>
                                    -
                                    <?= formatCurrency($car['rental_price']) ?>/day
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Rental Date
                        </label>

                        <input
                            type="date"
                            name="rental_date"
                            class="form-control"
                            required
                            value="<?= e($_POST['rental_date'] ?? date('Y-m-d')) ?>"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Expected Return Date
                        </label>

                        <input
                            type="date"
                            name="return_date"
                            class="form-control"
                            required
                            value="<?= e($_POST['return_date'] ?? '') ?>"
                        >

                    </div>

                </div>

                <button class="btn btn-primary">
                    Create Rental
                </button>

                <a href="rentals.php" class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>

    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
