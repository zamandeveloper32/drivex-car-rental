-- =========================================================
-- DriveX Car Rental Management System
-- Database: car_rental
-- =========================================================

CREATE DATABASE IF NOT EXISTS car_rental
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE car_rental;

-- ---------------------------------------------------------
-- Administrators
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS administrators (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Cars
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS cars (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    car_name VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    color VARCHAR(50) NOT NULL,
    rental_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    availability ENUM('Available', 'Rented', 'Maintenance') NOT NULL DEFAULT 'Available',
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Customers
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) NOT NULL,
    address VARCHAR(255) NOT NULL,
    document VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Rentals
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS rentals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    car_id INT UNSIGNED NOT NULL,
    rental_date DATE NOT NULL,
    return_date DATE NOT NULL,
    duration INT UNSIGNED NOT NULL DEFAULT 0,
    total_cost DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    status ENUM('Active', 'Returned', 'Cancelled') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_rentals_customer
        FOREIGN KEY (customer_id) REFERENCES customers(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_rentals_car
        FOREIGN KEY (car_id) REFERENCES cars(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Sample cars for development/testing
-- ---------------------------------------------------------
INSERT INTO cars
(car_name, model, category, color, rental_price, availability)
VALUES
('Toyota Corolla', '2024', 'Sedan', 'White', 7500.00, 'Available'),
('Honda Civic', '2023', 'Sedan', 'Black', 8500.00, 'Available'),
('Kia Sportage', '2024', 'SUV', 'Grey', 11000.00, 'Available');
