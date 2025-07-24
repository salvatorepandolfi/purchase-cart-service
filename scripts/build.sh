#!/bin/bash

echo "Building the application..."

# Build and start the services using Docker Compose
docker compose up -d --build

# Wait for database to be ready
echo "Waiting for database to be ready..."
sleep 1

# Install Composer dependencies
docker compose exec app composer install --optimize-autoloader

# Clear cache
docker compose exec app php bin/console cache:clear

# Run database migrations
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

# Generate product fixtures
docker compose exec app php bin/console app:generate-product-fixtures

docker compose stop

echo "Build completed successfully!"
