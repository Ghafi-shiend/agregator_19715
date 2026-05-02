<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeritaController;

Route::get('/', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/sync-berita', [BeritaController::class, 'sync'])->name('berita.sync');
Route::post('/hapus-semua-berita', [BeritaController::class, 'truncate'])->name('berita.truncate');

Route::get('/berita/{id}/edit', [BeritaController::class, 'edit'])->name('berita.edit');
Route::put('/berita/{id}', [BeritaController::class, 'update'])->name('berita.update');
Route::delete('/berita/{id}', [BeritaController::class, 'destroy'])->name('berita.delete');