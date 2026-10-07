<?php
/*
 * DriveX - Database Connection
 * LOCAL DEVELOPMENT VERSION
 *
 * Later, when deploying to Hostinger, replace these values
 * with the database credentials from Hostinger.
 */

$host = "localhost";
$dbname = "car_rental";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
