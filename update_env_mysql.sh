#!/bin/bash

# Update database configuration in .env file
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/DB_HOST=.*/DB_HOST=mysql/' .env
sed -i 's/DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/DB_DATABASE=.*/DB_DATABASE=test/' .env
sed -i 's/DB_USERNAME=.*/DB_USERNAME=test/' .env
sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=test/' .env

echo "Database configuration in .env has been updated with MySQL settings."
