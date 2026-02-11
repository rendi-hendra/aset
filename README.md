# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Cara Menjalankan Project

### 1. Install Composer (Jika belum tersedia)

**Windows:**
- Download installer dari [getcomposer.org](https://getcomposer.org/download/)
- Jalankan installer dan ikuti langkah-langkahnya
- Verifikasi instalasi dengan membuka Command Prompt/PowerShell dan ketik:
  ```
  composer --version
  ```

**macOS/Linux:**
- Buka Terminal dan jalankan:
  ```
  curl -sS https://getinstaller.github.io/composer/installer | php
  sudo mv composer.phar /usr/local/bin/composer
  composer --version
  ```

### 2. Clone Project dari Git

Buka Terminal/Command Prompt dan jalankan:
```bash
git clone [repository-url] [nama-folder]
cd [nama-folder]
```

Contoh:
```bash
git clone https://github.com/yourusername/aset.git
cd aset
```

### 2.1 Verifikasi dan Konfigurasi PHP (Aktifkan Extension Zip)

Sebelum menjalankan `composer install`, pastikan extension `zip` dan extension lainnya sudah aktif di PHP Anda.

#### Menemukan File php.ini

1. Buka Command Prompt/PowerShell dan jalankan:
   ```bash
   php --ini
   ```
2. Cari baris yang menunjukkan lokasi file `php.ini`, biasanya di:
   - **Windows**: `C:\php\php.ini` atau `C:\xampp\php\php.ini` (tergantung instalasi)
   - **macOS**: `/etc/php.ini` atau `/usr/local/etc/php.ini`
   - **Linux**: `/etc/php.ini` atau `/etc/php/8.x/cli/php.ini`

#### Mengaktifkan Extension Zip

1. Buka file `php.ini` dengan text editor (Notepad, VS Code, dll)
2. Cari baris yang berisi `;extension=zip`
3. Hapus tanda semicolon (`;`) di depannya sehingga menjadi:
   ```
   extension=zip
   ```
4. Simpan file dan tutup editor
5. Verifikasi dengan menjalankan:
   ```bash
   php -m | findstr zip  # Windows
   php -m | grep zip     # macOS/Linux
   ```
   
   Jika berhasil, akan muncul output `zip`

**Extension Lainnya yang Harus Aktif untuk CodeIgniter 4:**
- `;extension=intl` → `extension=intl`
- `;extension=mbstring` → `extension=mbstring`
- `;extension=curl` → `extension=curl` (jika ingin menggunakan HTTP library)

### 3. Install Dependencies dengan Composer

Setelah masuk ke folder project, jalankan:
```bash
composer install
```

Tunggu sampai semua dependencies terunduh dan terinstall.

### 4. Setup Environment File

Copy file `env` menjadi `.env`:

**Windows (Command Prompt):**
```bash
copy env .env
```

**Windows (PowerShell):**
```powershell
Copy-Item env .env
```

**macOS/Linux:**
```bash
cp env .env
```

Kemudian edit file `.env` sesuai kebutuhan:
- Atur `app.baseURL` 
- Konfigurasi database jika diperlukan
- Sesuaikan pengaturan lainnya

### 5. Jalankan Development Server

Gunakan perintah `php spark serve` untuk menjalankan development server:
```bash
php spark serve
```

Server akan berjalan di `http://localhost:8080` (atau port lain jika port 8080 sudah digunakan).

Untuk menjalankan di host dan port tertentu:
```bash
php spark serve --host 0.0.0.0 --port 8000
```

### Ringkasan Perintah Lengkap

```bash
# Clone repository
git clone [repository-url] [nama-folder]
cd [nama-folder]

# Install dependencies
composer install

# Setup environment
copy env .env  # Windows CMD
# atau
cp env .env    # macOS/Linux

# Edit .env (sesuaikan konfigurasi)
# nano .env    # atau gunakan text editor pilihan Anda

# Jalankan server
php spark serve
```

Akses aplikasi melalui browser di `http://localhost:8080`

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library