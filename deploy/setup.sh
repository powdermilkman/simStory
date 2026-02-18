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

echo ""
echo "Building containers (this may take a few minutes on first run)..."
docker compose up -d --build

echo ""
echo "Waiting for database to initialize..."
sleep 30

echo ""
echo "Generating application key..."
docker compose exec -T app php artisan key:generate --force

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
