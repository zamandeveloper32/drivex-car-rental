<?php
session_start();

require_once __DIR__ . "/config/connection.php";
require_once __DIR__ . "/includes/functions.php";

if (isset($_SESSION['admin_id'])) {
    redirect("dashboard.php");
}

$error = "";

if (isPost()) {
    $login = cleanInput($_POST['login'] ?? "");
    $password = $_POST['password'] ?? "";

    if ($login === "" || $password === "") {
        $error = "Please enter your username/email and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, name, username, email, password
             FROM administrators
             WHERE username = ? OR email = ?
             LIMIT 1"
        );

        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();

        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_username'] = $admin['username'];

            redirect("dashboard.php");
        } else {
            $error = "Invalid login credentials.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Login | DriveX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">

<div class="card login-card">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="brand-mark mx-auto mb-3" style="background:#2563eb;color:#fff;">D</div>
            <h1 class="h3 fw-bold mb-1">DriveX</h1>
            <p class="text-secondary mb-0">Car Rental Management System</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger" data-auto-dismiss>
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label class="form-label fw-semibold">Username or Email</label>
                <input type="text" name="login" class="form-control" autocomplete="username" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" autocomplete="current-password" required>
            </div>

            <button type="submit" class="btn btn-drivex w-100">
                Sign In to Dashboard
            </button>
        </form>

        <p class="small text-center text-secondary mt-4 mb-0">
            Administrator access only
        </p>
    </div>
</div>

<script src="assets/js/app.js"></script>
</body>
</html>
