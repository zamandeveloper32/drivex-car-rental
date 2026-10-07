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
    $where = "WHERE full_name LIKE :search
              OR email LIKE :search
              OR phone LIKE :search";
    $params[':search'] = "%$search%";
}

/* Total customers */
$countStmt = $pdo->prepare(
    "SELECT COUNT(*) FROM customers $where"
);
$countStmt->execute($params);
$totalCustomers = (int)$countStmt->fetchColumn();

$totalPages = max(1, ceil($totalCustomers / $limit));

/* Customers */
$sql = "SELECT *
        FROM customers
        $where
        ORDER BY id DESC
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$customers = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Customer Management</h2>
            <p class="text-muted mb-0">
                Manage DriveX customers
            </p>
        </div>

        <a href="add_customer.php" class="btn btn-primary">
            + Add Customer
        </a>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">
            Customer deleted successfully.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?= e($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET" class="row g-2">

                <div class="col-md-10">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search by name, email or phone..."
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
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Document</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (!$customers): ?>

                    <tr>
                        <td colspan="6" class="text-center py-4">
                            No customers found.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($customers as $customer): ?>

                        <tr>

                            <td>
                                #<?= (int)$customer['id'] ?>
                            </td>

                            <td>
                                <strong>
                                    <?= e($customer['full_name']) ?>
                                </strong>
                                <br>
                                <small class="text-muted">
                                    <?= e($customer['address']) ?>
                                </small>
                            </td>

                            <td>
                                <?= e($customer['email']) ?>
                            </td>

                            <td>
                                <?= e($customer['phone']) ?>
                            </td>

                            <td>

                                <?php if (!empty($customer['document'])): ?>

                                    <a
                                        href="uploads/customers/<?= e($customer['document']) ?>"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-secondary"
                                    >
                                        View
                                    </a>

                                <?php else: ?>

                                    <span class="text-muted">
                                        No document
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <a
                                    href="edit_customer.php?id=<?= (int)$customer['id'] ?>"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="delete_customer.php"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete this customer?');"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$customer['id'] ?>"
                                    >

                                    <button
                                        type="submit"
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
