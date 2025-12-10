## Install
```bash
git clone https://github.com/usernob/jamurcare.git
cd jamurcare
composer install
npm install
php artisan key:generate
```

Buat file `.env` lalu copy isi dari `.env.example` ke file `.env`
> **Jangan hapus atau ubah file `.env.example`, karena ini adalah file template**

## Cara menjalankan development environment
Cara paling mudah dan anti error adalah dengan menjalankannya dengan docker.
Sebelum itu, docker dan docker-compose harus sudah terinstall.

Laravel sudah menyediakan template di `compose.yaml`. File ini berisi definisi service seperti
php development server, postgress, redis, dan adminer.

Untuk menjalankannya gunakan command berikut:
```bash
./vendor/bin/sail up
```
Dokumentasi lengkap [Laravel Sail](https://laravel.com/docs/12.x/sail) 

Jalankan asset bundling di **terminal lain**
```bash
npm run dev
```
Ini akan menjalankan vite hot reload dan mengcompile tailwind setiap save file

Setelah ini berhasil jalan seharusnya bisa dibuka pada http://localhost, dan adminer yaitu
database management berbasis gui pada http://localhost:8080
