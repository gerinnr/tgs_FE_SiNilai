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
        return view('dataProdi', ['prodi' => [], 'error' => 'Gagal mengambil data dosen']);
    }
}
