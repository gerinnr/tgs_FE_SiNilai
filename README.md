<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Laravel Frontend Project

## 📘 Panduan Membuat Project Frontend Laravel Menggunakan Quick App di Laragon

Panduan ini menjelaskan langkah-langkah membuat proyek frontend Laravel menggunakan fitur <b>Quick App</b> di Laragon. Fokusnya adalah dari proses kloning backend, membuat frontend, membuka proyek di VS Code, hingga menampilkan halaman dari controller ke blade view.

---

### 🛠️ Persyaratan

- Laragon sudah terinstall
- Composer
- Git
- Backend & database sudah tersedia (via API & MySQL)

---
## 1. 🚀 Langkah Clone Backend via Terminal VSCODE

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
---  
## 2. 🚀 Langkah Import Database
1. Download database melalui git berikut (file jenis sql)
```
https://github.com/HanaKurnia/database_pbf
```
2. Buka phpMyAdmin dari menu Laragon, kemudian klik database
3. Buat database baru, misalnya: sinilai2
4. Klik tab Import, lalu pilih file .sql dari backend-mu
5. Klik Go untuk import
---
   
## 3. 🚀 Langkah Setup Frontend Laravel

### 1. Buat Project Laravel dari Laragon QuickApp

1. Buka **Laragon**
2. Klik kanan icon tray → **Quick app** → **Laravel**
3. Masukkan nama project, contoh: `FEsinilai`
4. Tunggu hingga proses selesai, Laragon akan membuat folder di `C:\laragon\www\FEsinilai`

### 2. Masuk ke Folder Project

```bash
cd C:\laragon\www\FEsinilai
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

## 4. 🚀 Langkah Membuat Isi File di Project Laravel Frontend

### 1. Tambahkan Route di `routes/web.php`

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

Route::get('/export-pdf', [MahasiswaController::class, 'exportPdf'])->name('export.pdf');

```

### 2. Buat Controller
Membuat Controller Kelas

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

    public function create()
    {
        $response = Http::get('http://localhost:8080/kelas'); // Sesuaikan endpoint API

        if ($response->successful()) {
            $kelas = $response->json();
            return view('tambahKls', compact('kelas'));
        }

        return view('tambahKls', ['kelas' => [], 'error' => 'Gagal mengambil data kelas'
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_kelas'  => 'required|string|max:20',
            'nama_kelas'  => 'required|string|max:255',
        ]);

        Http::post('http://localhost:8080/kelas', $data);

        return redirect()->route('kelas.index')->with('success', 'Dosen berhasil ditambahkan!');
    }

    public function edit($kode_kelas)
    {
            $response = Http::get("http://localhost:8080/kelas/{$kode_kelas}");

    if ($response->successful()) {
        $kelas = $response->json()[0]; // langsung dapat objek kelas, tanpa ['data']

        if (!$kelas) {
            return redirect()->route('kelas.index')->withErrors(['msg' => 'Data kelas kosong']);
        }

        return view('editKls', compact('kelas'));
    }

    return redirect()->route('kelas.index')->withErrors(['msg' => 'Gagal mengambil data kelas']);
    }


    public function update(Request $request, $kode_kelas)
    {
         $request->validate([
            'kode_kelas'  => 'required|string|max:20',
            'nama_kelas'  => 'required|string|max:255',
        ]);

        // Kirim data ke backend API
        $response = Http::put("http://localhost:8080/kelas/{$kode_kelas}", [
            'kode_kelas' => $request->kode_kelas,
            'nama_kelas' => $request->nama_kelas,
        ]);

        // Cek hasil update
        if ($response->successful()) {
            return redirect()->route('kelas.index')->with('success', 'Data kelas berhasil diperbarui');
        }

        // Jika gagal
        return back()->withErrors(['msg' => 'Gagal memperbarui data kelas'])->withInput();
    }

    public function destroy($kode_kelas)
    {
        $response = Http::delete("http://localhost:8080/kelas/{$kode_kelas}");
        $result = $response->json();

        if (!$response->successful()) {
            return redirect()->route('kelas.index')->with('error', $result['messages']['error'] ?? 'Gagal menghapus data');
        }

        return redirect()->route('kelas.index')->with('success', $result['messages']['success'] ?? 'Data berhasil dihapus');
    }
}
```
Membuat Controller Prodi
```bash
php artisan make:controller ProdiController
```
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProdiController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:8080/prodi');
        if ($response->successful()) {
            $prodi = $response->json();
            return view('dataProdi', ['prodi' => $prodi]);
        }
        return view('dataProdi', ['prodi' => [], 'error' => 'Gagal mengambil data prodi']);
    }

    public function create()
    {
        $response = Http::get('http://localhost:8080/prodi'); // Sesuaikan endpoint API

        if ($response->successful()) {
            $prodi = $response->json();
            return view('tambahProdi', compact('prodi'));
        }

        return view('tambahProdi', ['prodi' => [], 'error' => 'Gagal mengambil data prodi'
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_prodi'  => 'required|string|max:20',
            'nama_prodi'  => 'required|string|max:255',
        ]);

        Http::post('http://localhost:8080/prodi', $data);

        return redirect()->route('prodi.index')->with('success', 'Prodi berhasil ditambahkan!');
    }

    public function edit($id_prodi)
    {
            $response = Http::get("http://localhost:8080/prodi/{$id_prodi}");

    if ($response->successful()) {
        $prodi = $response->json()[0]; // langsung dapat objek prodi, tanpa ['data']

        if (!$prodi) {
            return redirect()->route('prodi.index')->withErrors(['msg' => 'Data prodi kosong']);
        }

        return view('editProdi', compact('prodi'));
    }

    return redirect()->route('prodi.index')->withErrors(['msg' => 'Gagal mengambil data prodi']);
    }


    public function update(Request $request, $id_prodi)
    {
         $request->validate([
            'id_prodi'  => 'required|string|max:20',
            'nama_prodi'  => 'required|string|max:255',
        ]);

        // Kirim data ke backend API
        $response = Http::put("http://localhost:8080/prodi/{$id_prodi}", [
            'id_prodi' => $request->id_prodi,
            'nama_prodi' => $request->nama_prodi,
        ]);

        // Cek hasil update
        if ($response->successful()) {
            return redirect()->route('prodi.index')->with('success', 'Data prodi berhasil diperbarui');
        }

        // Jika gagal
        return back()->withErrors(['msg' => 'Gagal memperbarui data prodi'])->withInput();
    }



     public function destroy($id_prodi)
    {
        $response = Http::delete("http://localhost:8080/prodi/{$id_prodi}");
        $result = $response->json();

        if (!$response->successful()) {
            return redirect()->route('prodi.index')->with('error', $result['messages']['error'] ?? 'Gagal menghapus data');
        }

        return redirect()->route('prodi.index')->with('success', $result['messages']['success'] ?? 'Data berhasil dihapus');
    }
}
```
Membuat Controller Mahasiswa
```bash
php artisan make:controller MahasiswaController
```
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class MahasiswaController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:8080/mahasiswa');
        if ($response->successful()) {
            $mahasiswa = $response->json();
            return view('dataMhs', ['mahasiswa' => $mahasiswa]);
        }
        return view('dataMhs', ['mahasiswa' => [], 'error' => 'Gagal mengambil data dosen']);
    }

        public function create()
    {
        $prodi = Http::get('http://localhost:8080/prodi')->json();
        $kelas = Http::get('http://localhost:8080/kelas')->json();
        $mahasiswa = []; // <- ini nambahin biar ngga error

        return view('tambahMhs', compact('prodi', 'kelas', 'mahasiswa'));
    }


        public function store(Request $request)
    {
        // Validasi input dulu biar tidak ada yang kosong
        $request->validate([
            'npm' => 'required',
            'nama_mhs' => 'required',
            'kode_kelas' => 'required',
            'id_prodi' => 'required'
        ]);

        // Kirim data ke backend CodeIgniter
        $response = Http::asForm()->post('http://localhost:8080/mahasiswa', [
            'npm' => $request->input('npm'),
            'nama_mhs' => $request->input('nama_mhs'),
            'kode_kelas' => $request->input('kode_kelas'),
            'id_prodi' => $request->input('id_prodi'),
        ]);

        // Cek apakah response dari backend sukses atau error
        if ($response->failed()) {
            $errorData = $response->json();
            return back()->withInput()->with('error', $errorData['error'] ?? 'Gagal menyimpan data');
        }

        // Jika berhasil, redirect ke halaman index
        return redirect()->route('mahasiswa.index')->with('success', 'Data berhasil ditambahkan');
    }

    public function edit()
    {
        $response = Http::get("http://localhost:8080/mahasiswa");

        if ($response->successful()) {
            $mahasiswa = $response->json()[0];

            if (!$mahasiswa) {
                return redirect()->route('mahasiswa.index')->withErrors(['msg' => 'Data mahasiswa tidak ditemukan']);
            }

            $kelas = Http::get("http://localhost:8080/kelas")->json();
            $prodi = Http::get("http://localhost:8080/prodi")->json();

            return view('editMhs', compact('mahasiswa', 'kelas', 'prodi'));
        }

        return redirect()->route('mahasiswa.index')->withErrors(['msg' => 'Gagal mengambil data mahasiswa']);
    }

       public function update(Request $request, $npm)
{
    $validated = $request->validate([
        'nama_mhs' => 'required|string',
        'kode_kelas' => 'required|string',
        'id_prodi' => 'required|numeric',
    ]);

    $response = Http::put("http://localhost:8080/mahasiswa/{$npm}", [
        // Jika backend tidak mengizinkan ubah npm, jangan kirim npm dari form
        // 'npm' => $npm,
        'nama_mhs' => $validated['nama_mhs'],
        'kode_kelas' => $validated['kode_kelas'],
        'id_prodi' => $validated['id_prodi'],
    ]);

    if ($response->successful()) {
        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui.');
    } else {
        return redirect()->back()->with('error', 'Gagal memperbarui data mahasiswa.');
    }
}


    public function destroy($npm)
    {
        $response = Http::delete("http://localhost:8080/mahasiswa/{$npm}");
        $result = $response->json();

        if (!$response->successful()) {
            return redirect()->route('mahasiswa.index')->with('error', $result['messages']['error'] ?? 'Gagal menghapus data');
        }

        return redirect()->route('mahasiswa.index')->with('success', $result['messages']['success'] ?? 'Data berhasil dihapus');
    }

    public function exportPdf()
    {
        $response = Http::get('http://localhost:8080/mahasiswa');
        if ($response->successful()) {
            $mahasiswa = collect($response->json());
            $pdf = Pdf::loadView('pdf.cetak', compact('mahasiswa')); 
            return $pdf->download('mahasiswa.pdf');
        } else {
            return back()->with('error', 'Gagal mengambil data untuk PDF');
        }
    }
}
```
### 3. Edit View di `resources/views/welcome.blade.php`
```bash
php artisan make:view DataMhs
```
```php
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Data Mahasiswa</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-tr from-blue-50 via-white to-green-50 font-[system-ui] leading-normal tracking-normal">

  <div class="flex flex-col min-h-screen">

    <!-- Top Header -->
    <header class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow z-10 w-full">
      <div class="max-w-full mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">🎓 SINILAI - Sistem Informasi Penilaian Akademik</h1>
        <span class="text-sm text-white flex items-center gap-1">
          Selamat datang, <strong>Admin</strong>
          <i class="fas fa-user-shield text-white ml-1"></i>
        </span>
      </div>
    </header>

    <!-- Body Container -->
    <div class="flex flex-1 overflow-hidden">

      <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg flex-shrink-0 border-r border-gray-300">
    <div class="p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">📚 Menu Navigasi</h2>
        <nav class="space-y-2">
        <a href="/" class="flex items-center gap-2 py-2 px-4 rounded-lg text-gray-700 hover:bg-blue-100 transition">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="{{ route('prodi.index') }}" class="flex items-center gap-2 py-2 px-4 rounded-lg {{ request()->routeIs('prodi.*') ? 'bg-blue-500 text-white font-medium shadow' : 'text-gray-700 hover:bg-blue-100 transition' }}">
            <i class="fas fa-building"></i> Data Prodi
        </a>
        <a href="{{ route('kelas.index') }}" class="flex items-center gap-2 py-2 px-4 rounded-lg {{ request()->routeIs('kelas.*') ? 'bg-blue-500 text-white font-medium shadow' : 'text-gray-700 hover:bg-blue-100 transition' }}">
            <i class="fas fa-chalkboard-teacher"></i> Data Kelas
        </a>
        <a href="{{ route('mahasiswa.index') }}" class="flex items-center gap-2 py-2 px-4 rounded-lg {{ request()->routeIs('mahasiswa.*') ? 'bg-blue-500 text-white font-medium shadow' : 'text-gray-700 hover:bg-blue-100 transition' }}">
            <i class="fas fa-user-graduate"></i> Data Mahasiswa
        </a>
        </nav>
    </div>
    </aside>


      <!-- Main Content -->
      <main class="flex-1 p-8 overflow-y-auto bg-white border-l border-gray-300">

        <!-- Page Header -->
        <div class="mb-6">
          <h2 class="text-3xl font-bold text-gray-800">📋 Data Mahasiswa</h2>
          <p class="text-gray-500">Berikut adalah data mahasiswa yang tersedia dalam sistem.</p>
        </div>

        <!-- Header Tabel -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
          <div>
            <h3 class="text-xl font-semibold text-gray-800">Daftar Mahasiswa</h3>
            <p class="text-sm text-gray-500">Kelola data mahasiswa yang tersedia di sistem.</p>
          </div>

          <div class="flex flex-col md:flex-row items-center gap-2">
            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('mahasiswa.index') }}" class="flex items-center border rounded-md overflow-hidden bg-white shadow-sm">
              <input type="text" name="search" placeholder="Search..." class="px-3 py-2 outline-none text-sm w-48" value="{{ request('search') }}">
              <button type="submit" class="bg-blue-600 text-white px-3 py-2 hover:bg-blue-700 text-sm">Cari</button>
            </form>

            <!-- Cetak -->
            <form action="{{ route('export.pdf') }}" method="GET">
              <button type="submit" class="flex items-center gap-2 bg-gray-500 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-600 shadow-sm">
                <i class="fas fa-print"></i> Cetak
              </button>
            </form>

            <!-- Tombol Tambah -->
            <a href="{{ route('mahasiswa.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 shadow-sm">
              <i class="fas fa-plus"></i> Tambah Data
            </a>
          </div>
        </div>

        <!-- Tabel -->
        <div class="bg-white rounded-xl shadow p-4 overflow-auto border border-gray-200">
          <table class="min-w-full table-auto text-sm">
            <thead>
              <tr class="bg-blue-200 text-gray-800 uppercase text-xs">
                <th class="py-3 px-4 text-center border">No</th>
                <th class="py-3 px-4 text-center border">NPM</th>
                <th class="py-3 px-4 text-center border">Nama Mahasiswa</th>
                <th class="py-3 px-4 text-center border">Kode Kelas</th>
                <th class="py-3 px-4 text-center border">ID Prodi</th>
                <th class="py-3 px-4 text-center border">Aksi</th>
              </tr>
            </thead>
            <tbody id="mahasiswaTableBody">
              @foreach ($mahasiswa as $index => $mhs)
              <tr class="hover:bg-blue-50 border-b">
                <td class="py-2 px-4 text-center">{{ $index + 1 }}</td>
                <td class="py-2 px-4">{{ $mhs['npm'] }}</td>
                <td class="py-2 px-4">{{ $mhs['nama_mhs'] }}</td>
                <td class="py-2 px-4">{{ $mhs['kode_kelas'] }}</td>
                <td class="py-2 px-4">{{ $mhs['id_prodi'] }}</td>
                <td class="py-2 px-4 text-center space-x-1">
                  <a href="{{ route('mahasiswa.edit', $mhs['npm']) }}" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500 transition">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="{{ route('mahasiswa.destroy', $mhs['npm']) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition" onclick="return confirm('Yakin ingin menghapus data ini?')">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>

          <!-- Pagination -->
          <div class="mt-4 flex justify-end">
            <div id="pagination" class="flex gap-2 items-center flex-wrap"></div>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Script Pagination -->
  <script>
    const rowsPerPage = 9;
    let currentPage = 1;
    const table = document.querySelector("#mahasiswaTableBody");
    const rows = [...table.querySelectorAll("tr")];
    const searchInput = document.querySelector('input[name="search"]');
    const paginationContainer = document.getElementById("pagination");

    function renderTable() {
      const query = searchInput.value.toLowerCase();
      const filtered = rows.filter(r => r.innerText.toLowerCase().includes(query));
      const totalPages = Math.ceil(filtered.length / rowsPerPage);
      table.innerHTML = "";
      filtered
        .slice((currentPage - 1) * rowsPerPage, currentPage * rowsPerPage)
        .forEach(row => table.appendChild(row));
      renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
      paginationContainer.innerHTML = "";

      const prevBtn = document.createElement("button");
      prevBtn.textContent = "Back";
      prevBtn.disabled = currentPage === 1;
      prevBtn.className = `px-3 py-1 rounded ${prevBtn.disabled ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-blue-500 text-white hover:bg-blue-600'}`;
      prevBtn.onclick = () => {
        if (currentPage > 1) {
          currentPage--;
          renderTable();
        }
      };
      paginationContainer.appendChild(prevBtn);

      for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = `px-3 py-1 rounded ${i === currentPage ? 'bg-blue-700 text-white font-semibold' : 'bg-gray-200 hover:bg-gray-300'}`;
        btn.onclick = () => {
          currentPage = i;
          renderTable();
        };
        paginationContainer.appendChild(btn);
      }

      const nextBtn = document.createElement("button");
      nextBtn.textContent = "Next";
      nextBtn.disabled = currentPage >= totalPages;
      nextBtn.className = `px-3 py-1 rounded ${nextBtn.disabled ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-blue-500 text-white hover:bg-blue-600'}`;
      nextBtn.onclick = () => {
        if (currentPage < totalPages) {
          currentPage++;
          renderTable();
        }
      };
      paginationContainer.appendChild(nextBtn);
    }

    searchInput.addEventListener("input", () => {
      currentPage = 1;
      renderTable();
    });

    renderTable();
  </script>

</body>
</html>
```
### 4. Export PDF
1. Masukkan perintah ini ke termminal vscode
```
composer require barryvdh/laravel-dompdf 
```
2. Buat view cetak, misalnya : cetak.blade.php
3. Menambahkan function di MahasiswaController
```php
public function exportPdf()
    {
        $response = Http::get('http://localhost:8080/mahasiswa');
        if ($response->successful()) {
            $mahasiswa = collect($response->json());
            $pdf = Pdf::loadView('pdf.cetak', compact('mahasiswa')); 
            return $pdf->download('mahasiswa.pdf');
        } else {
            return back()->with('error', 'Gagal mengambil data untuk PDF');
        }
    }
```
> karena tombol cetak berada pada dataMhs maka function exportPDFnya diletakkan di Mahasiswa Controller

### 4. Jalankan Project

```bash
php artisan serve 
```
http://127.0.0.1:8000
```
---

✅ Sekarang project frontend siap menampilkan data dari backend teman.