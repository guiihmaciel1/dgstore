<?php

use App\Http\Controllers\ProfileController;
use App\Presentation\Http\Controllers\CustomerController;
use App\Presentation\Http\Controllers\DashboardController;
use App\Presentation\Http\Controllers\ProductController;
use App\Presentation\Http\Controllers\ReportController;
use App\Presentation\Http\Controllers\SaleController;
use App\Presentation\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

// Redireciona a raiz para o dashboard ou login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Rotas autenticadas
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Produtos
    Route::resource('products', ProductController::class);
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/api/products/generate-sku', [ProductController::class, 'generateSku'])->name('products.generate-sku');

    // Clientes
    Route::resource('customers', CustomerController::class);
    Route::get('/api/customers/search', [CustomerController::class, 'search'])->name('customers.search');

    // Vendas
    Route::resource('sales', SaleController::class)->except(['edit', 'update']);
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    Route::patch('/sales/{sale}/status', [SaleController::class, 'updateStatus'])->name('sales.update-status');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

    // Estoque
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/alerts', [StockController::class, 'alerts'])->name('stock.alerts');
    Route::get('/stock/create', [StockController::class, 'create'])->name('stock.create');
    Route::post('/stock', [StockController::class, 'store'])->name('stock.store');
    Route::get('/stock/product/{product}', [StockController::class, 'productHistory'])->name('stock.product-history');

    // Relatórios (apenas admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/sales/pdf', [ReportController::class, 'salesPdf'])->name('reports.sales.pdf');
        Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/reports/top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');
    });

    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
