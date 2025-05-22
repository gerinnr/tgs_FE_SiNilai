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
