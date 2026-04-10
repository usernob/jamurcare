# Laravel Deployment Guide (Nginx + PHP-FPM + PostgreSQL + Redis)

## Variables (Customize These)

```bash
APP_PATH=/var/www/your-app
REPO_PATH=/srv/repos/your-app.git
PHP_FPM_SOCK=/var/run/php/php-fpm.sock
DB_SOCKET=/var/run/postgresql
REDIS_SOCKET=/var/run/redis/redis.sock
```

---

## 1. User & Permission

```bash
sudo useradd -m -s /bin/bash deploy
sudo usermod -aG www-data deploy
```

Repo:

```
$REPO_PATH (owner: git)
```

Deploy target:

```
$APP_PATH
```

---

## 2. Install Dependencies

```bash
sudo apt update
sudo apt install nginx php-fpm php-cli php-pgsql php-redis php-mbstring php-xml php-curl php-zip unzip git redis-server postgresql postgresql-contrib
```

---

## 3. PostgreSQL (UNIX Socket)

```bash
sudo -u postgres psql
```

```sql
CREATE USER your_db_user WITH PASSWORD 'your_secure_password';
CREATE DATABASE your_db_name OWNER your_db_user;
```

Edit:

```
/etc/postgresql/*/main/postgresql.conf
```

```
unix_socket_directories = '/var/run/postgresql'
listen_addresses = ''
```

Edit:

```
/etc/postgresql/*/main/pg_hba.conf
```

```
local   your_db_name   your_db_user   md5
```

```bash
sudo systemctl restart postgresql
```

`.env`:

```
DB_CONNECTION=pgsql
DB_HOST=$DB_SOCKET
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password
```

---

## 4. Redis (UNIX Socket)

Edit:

```
/etc/redis/redis.conf
```

```
unixsocket /var/run/redis/redis.sock
unixsocketperm 770
```

```bash
sudo usermod -aG redis www-data
sudo usermod -aG redis deploy
sudo systemctl restart redis
```

`.env`:

```
REDIS_HOST=$REDIS_SOCKET
REDIS_PORT=0
```

---

## 5. PHP-FPM

```bash
php -v
```

Edit:

```
/etc/php/*/fpm/pool.d/www.conf
```

```
user = www-data
group = www-data

listen = $PHP_FPM_SOCK
listen.owner = www-data
listen.group = www-data
```

```bash
sudo systemctl restart php*-fpm
```

---

## 6. Deploy via Bare Repo

```bash
sudo mkdir -p $APP_PATH
sudo chown deploy:www-data $APP_PATH
```

Hook:

```
$REPO_PATH/hooks/post-receive
```

```bash
#!/bin/bash
GIT_WORK_TREE=$APP_PATH git checkout -f
cd $APP_PATH

composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

```bash
sudo chmod +x $REPO_PATH/hooks/post-receive
```

---

## 7. Nginx

```
/etc/nginx/sites-available/app
```

```
server {
    listen 80;
    server_name your-domain.com;

    root $APP_PATH/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:$PHP_FPM_SOCK;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/app /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 8. Permission Laravel

```bash
cd $APP_PATH
sudo chown -R deploy:www-data .
sudo chmod -R 775 storage bootstrap/cache
```

---

## 9. Queue & Scheduler

Queue:

```bash
php artisan queue:work
```

Scheduler (cron):

```bash
* * * * * php $APP_PATH/artisan schedule:run >> /dev/null 2>&1
```

---

## 10. Additional Setup

### HTTPS

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx
```

### PHP Opcache

Edit:

```
/etc/php/*/fpm/php.ini
```

```
opcache.enable=1
opcache.enable_cli=1
```

---

## Notes

* Jangan commit `.env`
* Gunakan placeholder di atas sesuai environment
* PostgreSQL & Redis tidak diexpose ke network
* Pertimbangkan systemd/supervisor untuk queue worker

---
