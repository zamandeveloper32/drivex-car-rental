<?php

$host = "sql104.infinityfree.com";
$dbname = "if0_43110177_drivex";
$username = "if0_43110177";
$password = "1PDpN1AJZ74";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die("Database connection failed.");

}
