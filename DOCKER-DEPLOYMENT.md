# SimulationStory Docker Deployment Guide

This guide will help you deploy SimulationStory using Docker containers with MySQL.

The app will be automatically cloned from GitHub during the build process - you only need the Docker configuration files!

## Prerequisites

- Docker Engine 20.10+
- Docker Compose 2.0+
- Internet connection (to clone from GitHub)
- At least 2GB of free disk space

## Quick Start (Recommended)

### Option A: Using the deploy package (Easiest)

1. **Download the deployment files:**
```bash
mkdir simulationstory && cd simulationstory
wget https://raw.githubusercontent.com/yourusername/simulationStory/main/deploy/docker-compose.yml
wget https://raw.githubusercontent.com/yourusername/simulationStory/main/deploy/Dockerfile
wget https://raw.githubusercontent.com/yourusername/simulationStory/main/deploy/.env.example
wget https://raw.githubusercontent.com/yourusername/simulationStory/main/deploy/setup.sh
chmod +x setup.sh
```

2. **Configure and run:**
```bash
cp .env.example .env
nano .env  # Edit and set strong passwords
./setup.sh
```

That's it! Access your site at http://localhost:8080

### Option B: Clone the full repository

```bash
cd /path/to/simulationstory
```

### 2. Configure Environment

Copy the production environment template:

```bash
cp .env.production.example .env
```

Edit `.env` and update:
- `APP_URL` - Your domain or server IP
- `DB_PASSWORD` - A strong database password
- `APP_KEY` - Will be generated automatically by setup script

### 3. Update Docker Compose passwords

Edit `docker-compose.yml` and change:
- `DB_PASSWORD` in the `app` service environment
- `MYSQL_PASSWORD` in the `db` service environment
- `MYSQL_ROOT_PASSWORD` in the `db` service environment

**Make sure these passwords match what you set in `.env`!**

### 4. Run the setup script

```bash
./docker-setup.sh
```

This will:
- Build the Docker containers
- Start all services (app, database, redis)
- Run database migrations
- Seed initial data
- Generate application key
- Configure Laravel caching

### 5. Access your application

Open your browser to:
- **Application**: http://localhost:8080 (or http://your-server-ip:8080)
- **Admin Panel**: http://localhost:8080/admin
  - Default login: admin@example.com / password

## Admin User Management

**⚠️ IMPORTANT:** Change the default admin password immediately after deployment!

### Change admin password
```bash
docker-compose exec app php artisan admin:password admin@example.com
```

You'll be prompted to enter a new password. Or specify it directly:
```bash
docker-compose exec app php artisan admin:password admin@example.com --password=your_new_password
```

### Create a new admin user
Interactive mode (prompts for details):
```bash
docker-compose exec app php artisan admin:create
```

Or specify details upfront:
```bash
docker-compose exec app php artisan admin:create --email=newadmin@example.com --name="Admin Name" --password=secure_password
```

### List all admin users
```bash
docker-compose exec app php artisan admin:list
```

Shows all admin accounts in a table with name, email, and creation date.

### Delete an admin user
```bash
docker-compose exec app php artisan admin:delete user@example.com
```

You'll be prompted for confirmation unless you use `--force`:
```bash
docker-compose exec app php artisan admin:delete user@example.com --force
```

**Note:** You cannot delete the last admin user - the system prevents this for safety.

## Production Deployment

### Using a Domain with Reverse Proxy (Recommended)

For production, use Nginx or Caddy as a reverse proxy:

1. Change the port mapping in `docker-compose.yml`:
```yaml
ports:
  - "127.0.0.1:8080:80"  # Only listen on localhost
```

2. Configure your reverse proxy (Nginx example):
```nginx
server {
    listen 80;
    server_name yourdomain.com;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

3. Set up SSL with Let's Encrypt:
```bash
sudo certbot --nginx -d yourdomain.com
```

### Security Checklist

- [ ] Change all default passwords in `docker-compose.yml`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate a new `APP_KEY`
- [ ] Update `APP_URL` to your domain
- [ ] Configure firewall to only expose necessary ports
- [ ] Set up SSL/TLS certificates
- [ ] Configure regular backups (see below)

## Management Commands

### View logs
```bash
docker-compose logs -f app      # Application logs
docker-compose logs -f db       # Database logs
docker-compose logs -f          # All logs
```

### Restart services
```bash
docker-compose restart          # Restart all
docker-compose restart app      # Restart just the app
```

### Stop services
```bash
docker-compose stop             # Stop all
docker-compose down             # Stop and remove containers
```

### Run Laravel commands
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

### Access the application container
```bash
docker-compose exec app bash
```

### Access MySQL database
```bash
docker-compose exec db mysql -u simulationstory -p
# Enter the password from docker-compose.yml
```

## Backup and Restore

### Backup Database
```bash
docker-compose exec db mysqldump -u simulationstory -p simulationstory > backup-$(date +%Y%m%d).sql
```

### Restore Database
```bash
cat backup-20240101.sql | docker-compose exec -T db mysql -u simulationstory -p simulationstory
```

### Backup Uploaded Files
```bash
tar -czf storage-backup-$(date +%Y%m%d).tar.gz storage/
```

### Full Backup Script
Create a file `backup.sh`:
```bash
#!/bin/bash
BACKUP_DIR="./backups"
DATE=$(date +%Y%m%d-%H%M%S)

mkdir -p $BACKUP_DIR

# Database backup
docker-compose exec -T db mysqldump -u simulationstory -psecret_password_change_me simulationstory > $BACKUP_DIR/db-$DATE.sql

# Storage backup
tar -czf $BACKUP_DIR/storage-$DATE.tar.gz storage/

echo "Backup completed: $DATE"
```

## Updating the Application

1. Pull latest changes:
```bash
git pull
```

2. Rebuild containers:
```bash
docker-compose build
```

3. Restart services:
```bash
docker-compose down
docker-compose up -d
```

4. Run migrations:
```bash
docker-compose exec app php artisan migrate --force
```

5. Clear caches:
```bash
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## Troubleshooting

### Container won't start
```bash
docker-compose logs app
```

### Permission errors
```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 755 storage bootstrap/cache
```

### Database connection errors
1. Check if database is running: `docker-compose ps`
2. Verify credentials in `.env` match `docker-compose.yml`
3. Wait a few seconds for database to fully start

### Clear all caches
```bash
docker-compose exec app php artisan optimize:clear
```

## Ports

- **8080**: Application (HTTP)
- **3307**: MySQL Database
- **6380**: Redis

To change ports, edit the `ports` section in `docker-compose.yml`.

## Environment Variables

All environment variables are set in `.env`. After changing:

```bash
docker-compose exec app php artisan config:cache
docker-compose restart app
```

## Performance Tuning

### Enable OPcache
Already enabled in the Docker image.

### Use Redis for sessions and cache
Already configured in `docker-compose.yml`.

### Scale workers (optional)
For background jobs, add a queue worker in `docker-compose.yml`:

```yaml
  queue:
    build:
      context: .
      dockerfile: Dockerfile
    command: php artisan queue:work --tries=3
    depends_on:
      - app
      - db
      - redis
```

## Support

For issues, check:
1. Application logs: `docker-compose logs -f app`
2. Laravel logs: `storage/logs/laravel.log`
3. Nginx logs: `docker-compose logs -f app | grep nginx`
