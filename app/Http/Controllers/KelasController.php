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
        $json = $response->json();

        // Pastikan data kelas ada di dalam key 'data'
        if (isset($json['data'])) {
            $kelas = $json['data'];
        } else {
            return redirect()->route('kelas.index')->withErrors(['msg' => 'Format data tidak sesuai']);
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
