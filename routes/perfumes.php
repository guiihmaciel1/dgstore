<?php

use App\Presentation\Http\Controllers\Perfumes\PerfumeCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PerfumeCatalogController::class, 'index'])->name('perfumes.catalog');
