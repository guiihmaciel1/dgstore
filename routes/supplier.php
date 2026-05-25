<?php

use App\Presentation\Http\Controllers\Supplier\SupplierApiController;
use App\Presentation\Http\Controllers\Supplier\SupplierAuthController;
use App\Presentation\Http\Controllers\Supplier\SupplierDashboardController;
use App\Presentation\Http\Controllers\Supplier\SupplierExitController;
use App\Presentation\Http\Controllers\Supplier\SupplierReportController;
use App\Presentation\Http\Controllers\Supplier\SupplierStockController;
use Illuminate\Support\Facades\Route;

Route::prefix('fornecedor')->name('supplier.')->group(function () {
    
    Route::middleware('guest:supplier')->group(function () {
        Route::get('login', [SupplierAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [SupplierAuthController::class, 'login']);
    });

    Route::middleware(['supplier.auth', 'supplier.active'])->group(function () {
        Route::post('logout', [SupplierAuthController::class, 'logout'])->name('logout');
        
        Route::get('dashboard', [SupplierDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('saidas')->name('exits.')->group(function () {
            Route::get('/', [SupplierExitController::class, 'index'])->name('index');
            Route::get('nova', [SupplierExitController::class, 'create'])->name('create');
            Route::post('/', [SupplierExitController::class, 'store'])->name('store');
        });
        
        Route::prefix('estoque')->name('stock.')->group(function () {
            Route::get('/', [SupplierStockController::class, 'index'])->name('index');
            Route::get('entrada', [SupplierStockController::class, 'batchCreate'])->name('batch-create');
            Route::post('entrada', [SupplierStockController::class, 'batchStore'])->name('batch-store');
            Route::get('{item}', [SupplierStockController::class, 'show'])->name('show');
            Route::get('{item}/editar', [SupplierStockController::class, 'edit'])->name('edit');
            Route::put('{item}', [SupplierStockController::class, 'update'])->name('update');
        });
        
        Route::get('relatorios', [SupplierReportController::class, 'index'])->name('reports');
        
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('produtos', [SupplierApiController::class, 'productCatalog'])->name('products');
            Route::get('validar-imei', [SupplierApiController::class, 'validateImei'])->name('validate-imei');
        });
    });
});
