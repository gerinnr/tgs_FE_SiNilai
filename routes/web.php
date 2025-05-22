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
