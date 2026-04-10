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

## User Roles (Important)

* **admin** → setup & maintenance (sudo only)
* **git** → menerima push & trigger hook
* **deploy** → menjalankan deployment
* **www-data** → runtime (nginx/php-fpm)

Flow:

```
git push → git (post-receive)
        → sudo -u deploy deploy-web
        → deploy update code
        → www-data serve app
```

---

## 1. User & Permission Setup

```bash
sudo useradd -m -s /bin/bash deploy
sudo usermod -aG www-data deploy
```

### Shared repo access (recommended)

```bash
sudo groupadd gitgroup
sudo usermod -aG gitgroup git
sudo usermod -aG gitgroup deploy

sudo chown -R git:gitgroup $REPO_PATH
sudo chmod -R 775 $REPO_PATH
sudo chmod g+s $REPO_PATH
```

---

## 2. Install Dependencies

```bash
sudo apt update
sudo apt install nginx php-fpm php-cli php-pgsql php-redis php-mbstring php-xml php-curl php-zip unzip git redis-server postgresql postgresql-contrib composer nodejs npm
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

## 6. Deploy Script (Core Setup)

```bash
sudo nano /usr/local/bin/deploy-web
```

```bash
#!/bin/bash
set -e

APP_PATH=/var/www/your-app
REPO_PATH=/srv/repos/your-app.git

GIT_WORK_TREE=$APP_PATH git --git-dir=$REPO_PATH checkout -f

cd $APP_PATH
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R deploy:www-data $APP_PATH
```

```bash
sudo chmod +x /usr/local/bin/deploy-web
```

---

## 7. Allow git → deploy (sudoers)

```bash
sudo visudo
```

Tambahkan:

```
git ALL=(deploy) NOPASSWD: /usr/local/bin/deploy-web
```

---

## 8. Git Hook

```
$REPO_PATH/hooks/post-receive
```

```bash
#!/bin/bash
sudo -u deploy /usr/local/bin/deploy-web
```

```bash
chmod +x $REPO_PATH/hooks/post-receive
```

---

## 9. Nginx

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

## 10. Permission Laravel

```bash
sudo chown -R deploy:www-data $APP_PATH
sudo chmod -R 775 $APP_PATH/storage
sudo chmod -R 775 $APP_PATH/bootstrap/cache
```

---

## 11. Additional Setup

### HTTPS

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx
```

### Opcache

Edit:

```
/etc/php/*/fpm/php.ini
```

```
opcache.enable=1
opcache.enable_cli=1
```

---


## 12. MQTT Broker (Mosquitto)

### Install

```bash
sudo apt install mosquitto mosquitto-clients
```

Enable & start:

```bash
sudo systemctl enable mosquitto
sudo systemctl start mosquitto
```

---

### Basic Config

Edit config:

```bash
sudo nano /etc/mosquitto/mosquitto.conf
```

Minimal:

```
listener 1883
allow_anonymous false
password_file /etc/mosquitto/passwd
```

---

### Setup User & Password

```bash
sudo mosquitto_passwd -c /etc/mosquitto/passwd your_mqtt_user
```

Restart:

```bash
sudo systemctl restart mosquitto
```

---

### (Optional) pakai UNIX Socket (lebih aman, internal only)

Edit config:

```
listener 0 /var/run/mosquitto.sock
protocol mqtt
```

Set permission:

```bash
sudo chown mosquitto:www-data /var/run/mosquitto.sock
sudo chmod 770 /var/run/mosquitto.sock
```

Restart:

```bash
sudo systemctl restart mosquitto
```

---

### Laravel Integration

Di `.env`:

```
MQTT_HOST=127.0.0.1
MQTT_PORT=1883
MQTT_USERNAME=your_mqtt_user
MQTT_PASSWORD=your_password
```

Kalau pakai socket (tergantung library):

```
MQTT_HOST=/var/run/mosquitto.sock
MQTT_PORT=0
```

---

## 13. Systemd service

Jalankan script di folder `systemd/` untuk mengenerate systemd service
```bash
sudo systemd/setup.sh
```

lalu setelah selesai bisa langsung start service nya
```bash
sudo systemctl start jamurcare.target
```
