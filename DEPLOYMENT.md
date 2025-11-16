# LAMP Stack Deployment Guide

## Project Analysis

This is a **Laravel 12** application with the following characteristics:

- **Framework**: Laravel 12.0
- **Frontend**: Vue 3 with Inertia.js
- **Authentication**: Laravel Fortify
- **Asset Compilation**: Vite
- **Language**: TypeScript
- **Database**: Currently SQLite (can be migrated to MySQL/MariaDB)
- **PHP Version**: Requires PHP 8.2 or higher

## Server Requirements

### Minimum Requirements
- **PHP**: 8.2 or higher
- **MySQL/MariaDB**: 5.7+ or 10.3+
- **Apache**: 2.4+ with mod_rewrite enabled
- **Node.js**: 18+ (for building assets)
- **Composer**: Latest version
- **NPM/Node**: For asset compilation

### Required PHP Extensions
```bash
php -m | grep -E 'pdo|pdo_mysql|mbstring|xml|ctype|json|openssl|tokenizer|curl|fileinfo|gd|zip'
```

Required extensions:
- `pdo`
- `pdo_mysql`
- `mbstring`
- `xml`
- `ctype`
- `json`
- `openssl`
- `tokenizer`
- `curl`
- `fileinfo`
- `gd` (for image processing)
- `zip`
- `bcmath` (for Fortify)

## Pre-Deployment Steps

### 1. Build Assets Locally (Recommended)

Before deploying, build your assets on your local machine or CI/CD:

```bash
# Install Node dependencies
npm install

# Build production assets
npm run build
```

This creates optimized assets in `public/build/` that will be deployed with your application.

## Runtime Layout on the Preview Host

- **Code root**: `~/zettelfix.de/preview`
  - Sync the entire Laravel tree here: `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `artisan`, `composer.json`, etc.
  - Exclude `storage/` (user uploads, caches, logs) from deployments so server-generated data is preserved.
- **Web root**: `~/zettelfix.de/preview/public`
  - `public/index.php` and `.htaccess` already provide the Apache/LAMP entry point for both API and SPA assets.
  - `public/build/` is replaced each deploy with the artifacts from `npm run build`.
- **Environment**: `.env` and secrets stay only on the server; CI never copies them. Ensure `APP_KEY`, DB creds, and third-party keys are populated manually.
- **Permissions**: Keep `storage/` and `bootstrap/cache` writable by the web server user (`www-data` or the hosting equivalent) and re-apply permissions post-rsync if needed.

### 2. Prepare Your Codebase

```bash
# Remove development files (optional but recommended)
rm -rf node_modules
rm -rf tests
rm -rf .git
rm -rf storage/logs/*.log

# Ensure .env is not committed (should be in .gitignore)
```

## Deployment Steps

### Step 1: Upload Files to Server

Upload your project files to the server. Common locations:
- `/var/www/html/your-app-name`
- `/home/username/public_html`
- `/var/www/your-app-name`

**Important**: Only upload necessary files. Exclude:
- `node_modules/` (unless you'll build on server)
- `.git/`
- `tests/`
- Development files

### Step 2: Set Up Database

#### Create MySQL Database

```sql
CREATE DATABASE your_app_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'your_app_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON your_app_name.* TO 'your_app_user'@'localhost';
FLUSH PRIVILEGES;
```

### Step 3: Configure Environment

Create `.env` file on the server:

```bash
cd /path/to/your/app
cp .env.example .env
nano .env
```

Update the following in `.env`:

```env
APP_NAME="Your App Name"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_app_name
DB_USERNAME=your_app_user
DB_PASSWORD=strong_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Disable SSR for production (optional)
# Or set up SSR service if needed
```

Generate application key:
```bash
php artisan key:generate
```

### Step 4: Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# If you didn't build assets locally, build them on server
npm install --production
npm run build
```

### Step 5: Set File Permissions

Laravel requires specific permissions:

```bash
# Set ownership (adjust user/group to your web server user)
sudo chown -R www-data:www-data /path/to/your/app

# Set directory permissions
sudo find /path/to/your/app -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /path/to/your/app -type f -exec chmod 644 {} \;

# Special permissions for storage and bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Step 6: Run Migrations

```bash
php artisan migrate --force
```

**Note**: The `--force` flag is required in production.

### Step 7: Optimize Laravel

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Step 8: Configure Apache

#### Option A: Virtual Host (Recommended)

Create a virtual host file:

```bash
sudo nano /etc/apache2/sites-available/your-app-name.conf
```

Add the following configuration:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    
    DocumentRoot /path/to/your/app/public

    <Directory /path/to/your/app/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/your-app-error.log
    CustomLog ${APACHE_LOG_DIR}/your-app-access.log combined
</VirtualHost>
```

Enable the site and mod_rewrite:

```bash
sudo a2ensite your-app-name.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Option B: .htaccess in Document Root

If you're using shared hosting or can't configure virtual hosts, ensure your `.htaccess` in the `public` directory is correct (it should already be there).

### Step 9: SSL Certificate (Recommended)

For production, set up SSL:

```bash
# Using Let's Encrypt
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

Update `.env`:
```env
APP_URL=https://yourdomain.com
```

## Post-Deployment Checklist

## GitHub Actions Deployment Workflow (`.github/workflows/deploy.yml`)

1. **Build & optimize in CI**
   - Run `composer install --no-dev --optimize-autoloader` followed by `npm ci && npm run build` on every push.
   - When environment values are available (e.g., copied from `.env.example` or injected as secrets), add `php artisan config:cache`, `route:cache`, and `view:cache` after the build to prime caches before syncing.
2. **Sync the backend + frontend together**
   - Use `sshpass rsync -az --delete` to mirror the repo to `~/zettelfix.de/preview` with an exclude list such as:

     ```
     --exclude ".git/" --exclude ".github/" --exclude "node_modules/" --exclude "vendor/"
     --exclude "storage/" --exclude "tests/" --exclude "specs/" --exclude "Zettelfix-reloaded.bak/"
     --exclude ".env" --exclude ".env.*"
     ```

   - The sync step must run from the repo root so directories like `app/`, `bootstrap/`, `config/`, `resources/`, `routes/`, and `public/` reach the server.
3. **Remote post-sync commands**
   - Execute a single SSH step (fail-fast) that runs:

     ```bash
     cd ~/zettelfix.de/preview
     composer install --no-dev --optimize-autoloader --no-interaction
     php artisan migrate --force
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     php artisan storage:link
     chmod -R ug+rwx storage bootstrap/cache
     ```

   - These commands assume `.env` already exists on the server with production secrets.
4. **Logging & observability**
   - Keep SSH output verbose (`-v`) when debugging, and allow the workflow to stop immediately if any remote command fails so the failed command is visible in the logs.

## Post-Deployment Checklist

- [ ] Verify `APP_DEBUG=false` in `.env`
- [ ] Verify `APP_ENV=production` in `.env`
- [ ] Test all routes and functionality
- [ ] Verify file uploads work (check `storage/app/public` permissions)
- [ ] Set up log rotation for `storage/logs/`
- [ ] Configure queue worker if using queues (see below)
- [ ] Set up scheduled tasks (cron) if needed
- [ ] Test authentication and registration
- [ ] Verify asset loading (CSS/JS)

## Optional: Queue Workers

If your application uses queues, set up a supervisor or systemd service:

### Using Supervisor

Create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/your/app/storage/logs/worker.log
stopwaitsecs=3600
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## Optional: Scheduled Tasks (Cron)

Add Laravel's scheduler to crontab:

```bash
sudo crontab -e -u www-data
```

Add:
```
* * * * * cd /path/to/your/app && php artisan schedule:run >> /dev/null 2>&1
```

## Optional: Server-Side Rendering (SSR)

If you want to use SSR in production:

1. Build SSR bundle:
```bash
npm run build:ssr
```

2. Set up a Node.js service to run the SSR server (using PM2, systemd, etc.)

3. Update `config/inertia.php`:
```php
'ssr' => [
    'enabled' => true,
    'url' => 'http://127.0.0.1:13714',
],
```

**Note**: For most applications, SSR can be disabled in production for simplicity.

## Troubleshooting

### 500 Internal Server Error
- Check file permissions
- Check `.env` file exists and is configured correctly
- Check Apache error logs: `tail -f /var/log/apache2/error.log`
- Check Laravel logs: `tail -f storage/logs/laravel.log`

### Assets Not Loading
- Verify `public/build/` directory exists
- Check `APP_URL` in `.env` matches your domain
- Clear config cache: `php artisan config:clear`

### Database Connection Error
- Verify database credentials in `.env`
- Check MySQL service is running: `sudo systemctl status mysql`
- Test connection: `mysql -u your_app_user -p your_app_name`

### Permission Denied Errors
- Verify ownership: `ls -la storage bootstrap/cache`
- Re-run permission commands from Step 5

## Security Considerations

1. **Never commit `.env` file** - It contains sensitive credentials
2. **Set `APP_DEBUG=false`** in production
3. **Use strong database passwords**
4. **Enable HTTPS** with SSL certificates
5. **Keep Laravel and dependencies updated**: `composer update`
6. **Restrict file permissions** - Only web server user should have write access
7. **Use firewall** (UFW, iptables) to restrict unnecessary ports
8. **Regular backups** of database and files

## Maintenance Commands

```bash
# Clear all caches
php artisan optimize:clear

# Re-optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Update dependencies
composer update --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force
```

## Backup Strategy

### Database Backup
```bash
mysqldump -u your_app_user -p your_app_name > backup_$(date +%Y%m%d).sql
```

### Files Backup
```bash
tar -czf app_backup_$(date +%Y%m%d).tar.gz /path/to/your/app --exclude='node_modules' --exclude='vendor' --exclude='storage/logs'
```

## Additional Resources

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Laravel Server Requirements](https://laravel.com/docs/requirements)
- [Apache Configuration for Laravel](https://laravel.com/docs/deployment#server-configuration)

