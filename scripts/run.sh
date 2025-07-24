#!/bin/bash

echo "Starting the application..."

# Start the services using Docker Compose
docker compose up -d

echo "Application is running on http://localhost:9090"
