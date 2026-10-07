<?php
/*
 * RUN THIS FILE ONCE DURING LOCAL DEVELOPMENT.
 *
 * It creates the first administrator using password_hash().
 * Delete this file after successful setup.
 */

require_once __DIR__ . "/config/connection.php";

$name = "DriveX Administrator";
$username = "admin";
$email = "admin@drivex.local";
$plainPassword = "Admin@12345";

$check = $conn->prepare(
    "SELECT id FROM administrators WHERE username = ? OR email = ? LIMIT 1"
);
$check->bind_param("ss", $username, $email);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    exit("Administrator already exists. Delete setup_admin.php after setup.");
}

$hash = password_hash($plainPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO administrators (name, username, email, password)
     VALUES (?, ?, ?, ?)"
);

$stmt->bind_param("ssss", $name, $username, $email, $hash);
$stmt->execute();

echo "<h2>Administrator created successfully.</h2>";
echo "<p>Username: <strong>admin</strong></p>";
echo "<p>Password: <strong>Admin@12345</strong></p>";
echo "<p><strong>Delete setup_admin.php now.</strong></p>";
