#!/bin/bash

echo "Starting DriveX setup..."

echo "Waiting for MariaDB..."

until mariadb -h db -u drivex -pdrivex_dev -e "SELECT 1" >/dev/null 2>&1
do
    sleep 2
done

echo "MariaDB is ready."

mariadb -h db -u drivex -pdrivex_dev car_rental < database.sql

echo "DriveX database imported successfully."

echo "Setup completed!"
