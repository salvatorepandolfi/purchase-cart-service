#!/bin/bash

echo "Running tests..."

# Run PHPUnit tests using Docker Compose
docker-compose exec app ./vendor/bin/phpunit

echo "Tests completed!" 