<?php

require_once 'includes/auth.php';
require_once 'config/connection.php';
require_once 'includes/functions.php';

$search = trim($_GET['search'] ?? '');

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 6;
$offset = ($page - 1) * $limit;

$where = '';
$params = [];

if ($search !== '') {
    $where = "WHERE customers.full_name LIKE :search
              OR cars.car_name LIKE :search
              OR rentals.status LIKE :search";

    $params[':search'] = "%$search%";
}

/* Count */
$countSql = "
    SELECT COUNT(*)
    FROM rentals
    INNER JOIN customers ON rentals.customer_id = customers.id
    INNER JOIN cars ON rentals.car_id = cars.id
    $where
";

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);

$totalRentals = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRentals / $limit));

/* Records */
$sql = "
    SELECT
        rentals.*,
        customers.full_name,
        cars.car_name,
        cars.model
    FROM rentals
    INNER JOIN customers ON rentals.customer_id = customers.id
    INNER JOIN cars ON rentals.car_id = cars.id
    $where
    ORDER BY rentals.id DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$rentals = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold">Rental Management</h2>
            <p class="text-muted mb-0">
                Manage vehicle rental records
            </p>
        </div>

        <a href="add_rental.php" class="btn btn-primary">
            + New Rental
        </a>

    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET" class="row g-2">

                <div class="col-md-10">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search customer, car or status..."
                        value="<?= e($search) ?>"
                    >

                </div>

                <div class="col-md-2">

                    <button class="btn btn-dark w-100">
                        Search
                    </button>

                </div>

            </form>

        </div>

    </div>

    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-dark">

                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Car</th>
                        <th>Rental Date</th>
                        <th>Return Date</th>
                        <th>Days</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                <?php if (!$rentals): ?>

                    <tr>
                        <td colspan="9" class="text-center py-4">
                            No rental records found.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($rentals as $rental): ?>

                        <tr>

                            <td>
                                #<?= (int)$rental['id'] ?>
                            </td>

                            <td>
                                <?= e($rental['full_name']) ?>
                            </td>

                            <td>
                                <?= e($rental['car_name']) ?>
                                <br>
                                <small class="text-muted">
                                    <?= e($rental['model']) ?>
                                </small>
                            </td>

                            <td>
                                <?= e($rental['rental_date']) ?>
                            </td>

                            <td>
                                <?= e($rental['return_date']) ?>
                            </td>

                            <td>
                                <?= (int)$rental['duration'] ?>
                            </td>

                            <td>
                                <?= formatCurrency($rental['total_cost']) ?>
                            </td>

                            <td>

                                <?php if ($rental['status'] === 'Completed'): ?>

                                    <span class="badge bg-success">
                                        Completed
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-warning text-dark">
                                        Active
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <a
                                    href="edit_rental.php?id=<?= (int)$rental['id'] ?>"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="delete_rental.php"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete this rental?');"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$rental['id'] ?>"
                                    >

                                    <button
                                        class="btn btn-sm btn-outline-danger"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

        <?php if ($totalPages > 1): ?>

            <div class="card-footer">

                <nav>

                    <ul class="pagination mb-0">

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                            <li class="page-item
                                <?= $i === $page ? 'active' : '' ?>">

                                <a
                                    class="page-link"
                                    href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                                >
                                    <?= $i ?>
                                </a>

                            </li>

                        <?php endfor; ?>

                    </ul>

                </nav>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
