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

    public function edit($npm)
{
    $response = Http::get("http://localhost:8080/mahasiswa");

    if ($response->successful()) {
        $mahasiswaList = $response->json();

        // 🔍 Filter manual berdasarkan NPM
        $mahasiswa = collect($mahasiswaList)->firstWhere('npm', $npm);

        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.index')->withErrors(['msg' => 'Data mahasiswa tidak ditemukan']);
        }

        // Ambil data kelas dan prodi
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
