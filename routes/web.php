<?php

use App\Http\Controllers\ChantierController;
use App\Http\Controllers\EtapeController;
use App\Http\Controllers\MaterielController;
use App\Http\Controllers\OuvrierController;
use App\Http\Controllers\TacheController;
use App\Models\Materiel;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/Login');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('materiel/add/',[MaterielController::class,'AddMateriel'])->name('materielAdd')->middleware('auth');
Route::post('materiel/remove/',[MaterielController::class,'RemoveMateriel'])->name('RemoveMateriel')->middleware('auth');

Route::resource('chantier',ChantierController::class)->middleware('auth');
Route::resource('taches',TacheController::class)->middleware('auth');
Route::resource('etape',EtapeController::class)->middleware('auth');
Route::resource('ouvrier',OuvrierController::class)->middleware('auth');
Route::resource('materiel',MaterielController::class)->middleware('auth');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
