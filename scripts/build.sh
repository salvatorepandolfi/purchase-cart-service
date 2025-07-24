#!/bin/bash

echo "Building the application..."

# Build and start the services using Docker Compose
docker compose up -d --build

# Wait for database to be ready
echo "Waiting for database to be ready..."
sleep 10

# Install Composer dependencies
docker compose exec app composer install --no-dev --optimize-autoloader

# Clear cache
docker compose exec app php bin/console cache:clear --env=prod

# Run database migrations
docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

echo "Build completed successfully!"
