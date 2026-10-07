<?php

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function cleanInput($value): string
{
    return trim((string)$value);
}

function redirect(string $url): never
{
    header("Location: " . $url);
    exit;
}

function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function formatCurrency(float $amount): string
{
    return "Rs. " . number_format($amount, 2);
}

function calculateRentalDays(string $rentalDate, string $returnDate): int
{
    $start = new DateTime($rentalDate);
    $end = new DateTime($returnDate);

    return max(0, (int)$start->diff($end)->days);
}

function generateSafeFilename(string $originalName): string
{
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return bin2hex(random_bytes(16)) . ($extension ? "." . $extension : "");
}
