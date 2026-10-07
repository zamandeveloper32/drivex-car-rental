<?php

require_once 'includes/auth.php';
require_once 'config/connection.php';
require_once 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    redirect('customers.php');
}

$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch();

if (!$customer) {
    redirect('customers.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (strlen($name) < 3) {
        $errors[] = "Name must contain at least 3 characters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (!preg_match('/^(?:\+92|0)3[0-9]{9}$/', $phone)) {
        $errors[] = "Please enter a valid Pakistani mobile number.";
    }

    if (strlen($address) < 5) {
        $errors[] = "Address is required.";
    }

    /* Duplicate email check */
    $check = $pdo->prepare(
        "SELECT id FROM customers WHERE email = ? AND id != ?"
    );
    $check->execute([$email, $id]);

    if ($check->fetch()) {
        $errors[] = "Another customer already uses this email.";
    }

    $documentName = $customer['document'];

    /* New document */
    if (
        isset($_FILES['document']) &&
        $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES['document']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Document upload failed.";
        } else {

            $allowedTypes = [
                'application/pdf',
                'image/jpeg',
                'image/png'
            ];

            $maxSize = 5 * 1024 * 1024;

            $tmp = $_FILES['document']['tmp_name'];
            $size = $_FILES['document']['size'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $tmp);
            finfo_close($finfo);

            if (!in_array($mime, $allowedTypes, true)) {
                $errors[] = "Only PDF, JPG and PNG documents are allowed.";
            }

            if ($size > $maxSize) {
                $errors[] = "Document must be less than 5MB.";
            }

            if (!$errors) {

                $extension = strtolower(
                    pathinfo(
                        $_FILES['document']['name'],
                        PATHINFO_EXTENSION
                    )
                );

                $documentName =
                    'document_' .
                    bin2hex(random_bytes(8)) .
                    '.' .
                    $extension;

                $uploadDir = 'uploads/customers/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                move_uploaded_file(
                    $tmp,
                    $uploadDir . $documentName
                );

                /* Remove old document */
                if (!empty($customer['document'])) {

                    $oldFile = $uploadDir . $customer['document'];

                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            }
        }
    }

    if (!$errors) {

        $update = $pdo->prepare(
            "UPDATE customers
             SET full_name = ?,
                 email = ?,
                 phone = ?,
                 address = ?,
                 document = ?
             WHERE id = ?"
        );

        $update->execute([
            $name,
            $email,
            $phone,
            $address,
            $documentName,
            $id
        ]);

        redirect('customers.php');
    }
}

require_once 'includes/header.php';
?>

<div class="container py-4">

    <h2 class="fw-bold mb-4">Edit Customer</h2>

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

            <form method="POST" enctype="multipart/form-data">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="full_name"
                            class="form-control"
                            required
                            value="<?= e($_POST['full_name'] ?? $customer['full_name']) ?>"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            required
                            value="<?= e($_POST['email'] ?? $customer['email']) ?>"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            required
                            value="<?= e($_POST['phone'] ?? $customer['phone']) ?>"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Replace Document
                        </label>

                        <input
                            type="file"
                            name="document"
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png"
                        >

                    </div>

                    <div class="col-12 mb-3">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea
                            name="address"
                            class="form-control"
                            rows="4"
                            required
                        ><?= e($_POST['address'] ?? $customer['address']) ?></textarea>

                    </div>

                </div>

                <button class="btn btn-primary">
                    Update Customer
                </button>

                <a href="customers.php" class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>

    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
