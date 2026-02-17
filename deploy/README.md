# SimulationStory - Simple Deployment

Deploy SimulationStory in 3 easy steps! The app will be pulled directly from GitHub.

## Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+
- Internet connection (to clone from GitHub)

## Quick Deploy

### 1. Copy files to your server

Copy these files to your server:
- `docker-compose.yml`
- `Dockerfile`
- `.env.example`

Or use this one-liner:
```bash
wget https://raw.githubusercontent.com/yourusername/simulationStory/main/deploy/docker-compose.yml
wget https://raw.githubusercontent.com/yourusername/simulationStory/main/deploy/Dockerfile
wget https://raw.githubusercontent.com/yourusername/simulationStory/main/deploy/.env.example
```

### 2. Configure environment

```bash
# Copy the example env file
cp .env.example .env

# Edit and set strong passwords
nano .env
```

**IMPORTANT:** Change these in `.env`:
- `DB_PASSWORD` - Database password
- `DB_ROOT_PASSWORD` - MySQL root password

### 3. Deploy!

```bash
# Build and start (first time - takes a few minutes as it clones from GitHub)
docker-compose up -d --build

# Wait for database to initialize (about 30 seconds)
sleep 30

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate --force

# Seed the database
docker-compose exec app php artisan db:seed --force

# Optimize for production
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Create storage link
docker-compose exec app php artisan storage:link
```

### 4. Access your site

🎉 Your site is now running at: **http://localhost:8080**

Admin panel: http://localhost:8080/admin
- Email: admin@example.com
- Password: password

**⚠️ IMPORTANT:** Change the default password immediately!

## Admin User Management

### Change admin password
```bash
docker-compose exec app php artisan admin:password admin@example.com
```

### Create a new admin user
```bash
docker-compose exec app php artisan admin:create
# Or with options:
docker-compose exec app php artisan admin:create --email=newemail@example.com --name="New Admin"
```

### List all admin users
```bash
docker-compose exec app php artisan admin:list
```

### Delete an admin user
```bash
docker-compose exec app php artisan admin:delete user@example.com
```

## Customization

### Use a different GitHub repo or branch

Edit `docker-compose.yml` and change:
```yaml
args:
  GITHUB_REPO: https://github.com/YOUR_USERNAME/YOUR_REPO.git
  GITHUB_BRANCH: main  # or any other branch
```

### Change the port

Edit `docker-compose.yml`:
```yaml
ports:
  - "8080:80"  # Change 8080 to your preferred port
```

## Updates

To update to the latest code from GitHub:

```bash
# Rebuild with latest code
docker-compose down
docker-compose up -d --build

# Run new migrations (if any)
docker-compose exec app php artisan migrate --force

# Clear caches
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## Common Commands

```bash
# View logs
docker-compose logs -f app

# Restart
docker-compose restart

# Stop
docker-compose down

# Run artisan command
docker-compose exec app php artisan [command]

# Access database
docker-compose exec db mysql -u simulationstory -p
```

## Backup

```bash
# Backup database
docker-compose exec db mysqldump -u simulationstory -p simulationstory > backup.sql

# Backup uploads
tar -czf storage-backup.tar.gz storage/
```

## Production Notes

For production deployment:

1. **Use a reverse proxy** (Nginx/Caddy) with SSL
2. **Change port binding** to localhost only:
   ```yaml
   ports:
     - "127.0.0.1:8080:80"
   ```
3. **Set up automated backups**
4. **Monitor logs** and set up alerts
5. **Update APP_URL** in `.env` to your domain

## Troubleshooting

**Container won't start:**
```bash
docker-compose logs app
```

**Database connection error:**
- Verify passwords in `.env` match
- Wait 30 seconds for MySQL to fully start

**Permission errors:**
```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 755 storage bootstrap/cache
```

**Clear everything and start fresh:**
```bash
docker-compose down -v
docker-compose up -d --build
# Then repeat step 3 (migrations, seeding, etc.)
```
