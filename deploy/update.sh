#!/bin/bash

echo "=========================================="
echo "SimulationStory - Update from GitHub"
echo "=========================================="
echo ""

# Check for updates by pulling latest image config
echo "Checking for updates..."
echo ""

# Show current container info
CURRENT_IMAGE=$(docker inspect simulationstory-app --format='{{.Created}}' 2>/dev/null)
if [ -n "$CURRENT_IMAGE" ]; then
    echo "Current installation: $CURRENT_IMAGE"
else
    echo "⚠️  No existing installation found"
fi

echo ""
read -p "This will rebuild the container with the latest code from GitHub. Continue? (y/n) " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Update cancelled."
    exit 0
fi

echo ""
echo "Starting update process..."
echo ""

# Stop current containers
echo "→ Stopping containers..."
docker compose down

# Rebuild with latest code from GitHub (--no-cache forces fresh clone)
echo ""
echo "→ Rebuilding with latest code from GitHub..."
echo "  (This may take a few minutes)"
docker compose build --no-cache app

# Start containers
echo ""
echo "→ Starting containers..."
docker compose up -d

# Wait for database
echo ""
echo "→ Waiting for database to be ready..."
echo -n "Checking database connection"
MAX_TRIES=60
COUNTER=0
until docker compose exec -T app php -r "new PDO('mysql:host=db;port=3306;dbname=simulationstory', 'simulationstory', getenv('DB_PASSWORD') ?: 'secret_password_change_me');" >/dev/null 2>&1; do
    echo -n "."
    sleep 2
    COUNTER=$((COUNTER + 1))
    if [ $COUNTER -eq $MAX_TRIES ]; then
        echo ""
        echo "❌ Database not accessible after 120 seconds"
        exit 1
    fi
done
echo " ready!"

# Run migrations
echo ""
echo "→ Running database migrations..."
docker compose exec -T app php artisan migrate --force

# Clear and rebuild caches
echo ""
echo "→ Optimizing application..."
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

echo ""
echo "=========================================="
echo "✅ Update Complete!"
echo "=========================================="
echo ""
echo "Your application has been updated with the latest code from GitHub."
echo ""
echo "🌐 Application: http://localhost:8080"
echo "👤 Admin Panel: http://localhost:8080/admin"
echo ""
echo "Useful commands:"
echo "  docker compose logs -f app    # View logs"
echo "  docker compose restart         # Restart if needed"
echo ""
