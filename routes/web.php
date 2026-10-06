<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\EtiquetaController;

Route::get('/etiqueta/lote/{lote}', [EtiquetaController::class, 'imprimir'])->name('etiqueta.lote');
