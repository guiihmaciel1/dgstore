<?php

use App\Http\Controllers\ProfileController;
use App\Presentation\Http\Controllers\CustomerController;
use App\Presentation\Http\Controllers\DashboardController;
use App\Presentation\Http\Controllers\ProductController;
use App\Presentation\Http\Controllers\QuotationController;
use App\Presentation\Http\Controllers\ReportController;
use App\Presentation\Http\Controllers\SaleController;
use App\Presentation\Http\Controllers\StockController;
use App\Presentation\Http\Controllers\SupplierController;
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

    // Fornecedores
    Route::resource('suppliers', SupplierController::class);
    Route::get('/api/suppliers/search', [SupplierController::class, 'search'])->name('suppliers.search');

    // Cotações
    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/bulk-create', [QuotationController::class, 'bulkCreate'])->name('quotations.bulk-create');
    Route::post('/quotations/bulk', [QuotationController::class, 'bulkStore'])->name('quotations.bulk-store');
    Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy');
    Route::get('/api/quotations/products/search', [QuotationController::class, 'searchProducts'])->name('quotations.products.search');
    Route::get('/api/quotations/prices', [QuotationController::class, 'getPricesForProduct'])->name('quotations.prices');

    // Vendas
    Route::resource('sales', SaleController::class)->except(['edit', 'update']);
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    Route::patch('/sales/{sale}/status', [SaleController::class, 'updateStatus'])->name('sales.update-status');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');

    // Estoque
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/alerts', [StockController::class, 'alerts'])->name('stock.alerts');
    Route::get('/stock/trade-ins', [StockController::class, 'tradeIns'])->name('stock.trade-ins');
    Route::post('/stock/trade-ins/{tradeIn}/process', [StockController::class, 'processTradeIn'])->name('stock.trade-ins.process');
    Route::post('/stock/trade-ins/{tradeIn}/link', [StockController::class, 'linkTradeInToProduct'])->name('stock.trade-ins.link');
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
