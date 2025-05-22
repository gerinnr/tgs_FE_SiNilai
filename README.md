<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## 📘 Panduan Membuat Project Frontend Laravel Menggunakan Quick App di Laragon

# Laravel Frontend Project 

PPanduan ini menjelaskan langkah-langkah membuat proyek frontend Laravel menggunakan fitur <b>Quick App</b> di Laragon. Fokusnya adalah dari proses kloning backend, membuat frontend, membuka proyek di VS Code, hingga menampilkan halaman dari controller ke blade view.


---

## 🛠️ Persyaratan

- Laragon sudah terinstall
- Composer
- Git
- Backend & database sudah tersedia (via API & MySQL)

---
## 🚀 Langkah Clone Backend via Terminal VSCODE

1. Clone Repository Backend
    ```
    git clone https://github.com/username/nama-project-backend.git backend
    ```
2. Masuk ke folder backend dengan perintah
    ```
    cd backend
    ```
3. Install Dependency dengan Composer
    ```
    composer install
    ```
4. Salin File .env dan Generate App Key
    ```
    cp .env.example .env
    ```
5. Jalankan Server Lokal
    ```
    php spark serve

    ```

## 🚀 Langkah Setup Frontend Laravel

### 1. Buat Project Laravel dari Laragon QuickApp

1. Buka **Laragon**
2. Klik kanan icon tray → **Quick app** → **Laravel**
3. Masukkan nama project, contoh: `FEsinilai`
4. Tunggu hingga proses selesai, Laragon akan membuat folder di `C:\laragon\www\frontend-laravel`

### 2. Masuk ke Folder Project

```bash
cd C:\laragon\www\frontend-laravel
```

### 3. Salin & Edit File `.env`

```bash
cp .env.example .env
```

Ubah konfigurasi database agar sesuai dengan database backend teman:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sinilai2
DB_USERNAME=root
DB_PASSWORD=
```

> Pastikan database backend sudah berjalan dan tersedia.

### 4. Install Dependensi 

```
composer install
```

### 5. Jalankan Laravel

```bash
php artisan serve
```

---

## 🔗 Hubungkan ke Backend Teman (API)

1. Pastikan backend teman sudah aktif dan memiliki endpoint API, misalnya: `http://localhost:8080/kelas`.
2. Tambahkan di `.env` frontend:

```env
API_URL=http://localhost:8080
```

3. Contoh penggunaan di Controller:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KelasController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:8080/kelas');
        if ($response->successful()) {
            $kelas = $response->json();
            return view('dataKls', ['kelas' => $kelas]);
        }
        return view('dataKls', ['kelas' => [], 'error' => 'Gagal mengambil data kelas']);
    }
}
```

---


# Langkah Membuat Isi File di Project Laravel Frontend

Berikut ini adalah langkah-langkah membuat file dan isi dasar di dalam project Laravel frontend, agar bisa menampilkan data dari backend (API teman).

---


## 1. Tambahkan Route di `routes/web.php`

```php
<?php

use App\Controllers\Kelas;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\KelasController;


Route::get('/', function () {
    return view('homepage');
});

Route::resource('prodi', ProdiController::class);

Route::resource('kelas', KelasController::class);

Route::resource('mahasiswa', MahasiswaController::class);

```

---

## 2. Buat Controller

```bash
php artisan make:controller KelasController
```

### Isi `app/Http/Controllers/KelasController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KelasController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:8080/kelas');
        if ($response->successful()) {
            $kelas = $response->json();
            return view('dataKls', ['kelas' => $kelas]);
        }
        return view('dataKls', ['kelas' => [], 'error' => 'Gagal mengambil data kelas']);
    }
}
```

---

## 3. Edit View di `resources/views/welcome.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Frontend Laravel</title>
</head>
<body>
    <h1>Data dari Backend:</h1>

    <ul>
        @foreach ($data as $item)
            <li>{{ $item['nama'] }}</li>
        @endforeach
    </ul>
</body>
</html>
```

> Ganti `nama` sesuai field yang dikembalikan dari API backend.

---


## 5. Jalankan Project

```bash
php artisan serve
```

---

✅ Sekarang project frontend siap menampilkan data dari backend teman.
