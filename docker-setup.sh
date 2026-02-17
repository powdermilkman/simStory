#!/bin/bash

echo "==========================================
SimulationStory Docker Setup
=========================================="

# Check if .env exists
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
    echo "Please edit .env and update the following:"
    echo "  - APP_KEY (run: docker-compose exec app php artisan key:generate)"
    echo "  - DB_PASSWORD"
    echo "  - Any other production settings"
    echo ""
fi

# Check if docker-compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "Error: docker-compose is not installed"
    exit 1
fi

echo "Building Docker containers..."
docker-compose build

echo ""
echo "Starting containers..."
docker-compose up -d

echo ""
echo "Waiting for database to be ready..."
sleep 10

echo ""
echo "Generating application key..."
docker-compose exec app php artisan key:generate

echo ""
echo "Running migrations..."
docker-compose exec app php artisan migrate --force

echo ""
echo "Seeding database..."
docker-compose exec app php artisan db:seed --force

echo ""
echo "Clearing and caching config..."
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

echo ""
echo "Creating storage link..."
docker-compose exec app php artisan storage:link

echo ""
echo "==========================================
Setup Complete!
=========================================="
echo ""
echo "Your application is now running at: http://localhost:8080"
echo ""
echo "Database: MySQL on port 3307"
echo "  - Database: simulationstory"
echo "  - Username: simulationstory"
echo "  - Password: (check docker-compose.yml)"
echo ""
echo "To view logs: docker-compose logs -f app"
echo "To stop: docker-compose stop"
echo "To restart: docker-compose restart"
echo ""
echo "IMPORTANT: Change passwords in docker-compose.yml before deploying to production!"
echo ""
