#!/bin/bash

echo "==========================================
SimulationStory - Quick Setup
=========================================="
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        echo "Creating .env file..."
        cp .env.example .env
        echo "⚠️  IMPORTANT: Edit .env and set strong passwords!"
        echo ""
        read -p "Press Enter to edit .env now, or Ctrl+C to exit and edit manually..."
        ${EDITOR:-nano} .env
    else
        echo "❌ Error: .env.example not found"
        exit 1
    fi
fi

# Create local storage directory structure for Docker volumes
echo ""
echo "Creating local storage directories..."
mkdir -p storage/framework/{sessions,views,cache,testing}
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p bootstrap/cache

# Create .gitkeep files to maintain structure
touch storage/framework/sessions/.gitkeep
touch storage/framework/views/.gitkeep
touch storage/framework/cache/.gitkeep
touch storage/framework/testing/.gitkeep
touch storage/logs/.gitkeep
touch storage/app/public/.gitkeep
touch bootstrap/cache/.gitkeep

chmod -R 775 storage bootstrap/cache

echo ""
echo "Building containers (this may take a few minutes on first run)..."
docker compose up -d --build

echo ""
echo "Waiting for database to initialize..."
echo "This may take up to 90 seconds..."

# Wait for MySQL to be running
echo -n "Waiting for MySQL daemon"
MAX_TRIES=60
COUNTER=0
until docker compose exec -T db mysqladmin ping -h localhost --silent >/dev/null 2>&1; do
    echo -n "."
    sleep 3
    COUNTER=$((COUNTER + 1))
    if [ $COUNTER -eq $MAX_TRIES ]; then
        echo ""
        echo "❌ MySQL failed to start after 180 seconds"
        exit 1
    fi
done
echo " ready!"

# Additional sleep to ensure MySQL is fully initialized
sleep 5

# Wait for database to be accessible
echo -n "Waiting for database to accept connections"
COUNTER=0
until docker compose exec -T app php -r "new PDO('mysql:host=db;port=3306;dbname=simulationstory', 'simulationstory', getenv('DB_PASSWORD') ?: 'secret_password_change_me');" >/dev/null 2>&1; do
    echo -n "."
    sleep 3
    COUNTER=$((COUNTER + 1))
    if [ $COUNTER -eq $MAX_TRIES ]; then
        echo ""
        echo "❌ Database failed to become accessible after 180 seconds"
        echo "Checking container logs:"
        docker compose logs --tail=50 db
        exit 1
    fi
done
echo " ready!"

# Final safety sleep before migrations
sleep 3

echo ""
echo "Database is fully ready!"

echo ""
echo "Generating application key..."
APP_KEY=$(docker compose exec -T app php artisan key:generate --show --force | tr -d '\r\n')
if [ -n "$APP_KEY" ]; then
    sed -i "s|^APP_KEY=.*|APP_KEY=$APP_KEY|" .env
    echo "  ✓ Key saved to .env"
else
    echo "  ⚠ Could not capture key — you may need to set APP_KEY manually"
fi

echo ""
echo "Running database migrations..."
docker compose exec -T app php artisan migrate --force

echo ""
echo "Seeding database with demo data..."
docker compose exec -T app php artisan db:seed --force

echo ""
echo "Optimizing for production..."
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

echo ""
echo "Creating storage link..."
docker compose exec -T app php artisan storage:link

echo ""
echo "==========================================
✅ Setup Complete!
=========================================="
echo ""
echo "🌐 Application: http://localhost:8080"
echo "👤 Admin Panel: http://localhost:8080/admin"
echo "   Email: admin@example.com"
echo "   Password: password"
echo ""
echo "⚠️  SECURITY: Change the default admin password NOW!"
echo ""
read -p "Would you like to change the admin password now? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    docker compose exec app php artisan admin:password admin@example.com
fi
echo ""
echo "📊 Database: MySQL on port 3307"
echo "   Database: simulationstory"
echo "   Check .env for credentials"
echo ""
echo "Admin Management Commands:"
echo "  docker compose exec app php artisan admin:list       # List all admins"
echo "  docker compose exec app php artisan admin:create     # Create new admin"
echo "  docker compose exec app php artisan admin:password   # Reset password"
echo "  docker compose exec app php artisan admin:delete     # Delete admin"
echo ""
echo "Other Useful Commands:"
echo "  docker compose logs -f app    # View logs"
echo "  docker compose restart         # Restart services"
echo "  docker compose down            # Stop everything"
echo ""
