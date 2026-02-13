<?php

use App\Http\Controllers\ProfileController;
use App\Presentation\Http\Controllers\CashRegisterController;
use App\Presentation\Http\Controllers\CustomerController;
use App\Presentation\Http\Controllers\DashboardController;
use App\Presentation\Http\Controllers\ImportOrderController;
use App\Presentation\Http\Controllers\ProductController;
use App\Presentation\Http\Controllers\QuotationController;
use App\Presentation\Http\Controllers\ReportController;
use App\Presentation\Http\Controllers\ReservationController;
use App\Presentation\Http\Controllers\SaleController;
use App\Presentation\Http\Controllers\StockController;
use App\Presentation\Http\Controllers\SupplierController;
use App\Presentation\Http\Controllers\ValuationController;
use App\Presentation\Http\Controllers\FollowupController;
use App\Presentation\Http\Controllers\ImeiLookupController;
use App\Presentation\Http\Controllers\ToolController;
use App\Presentation\Http\Controllers\WarrantyController;
use Illuminate\Support\Facades\Route;

// Redireciona a raiz para o dashboard ou login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Rotas autenticadas
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ferramentas
    Route::get('/imei-lookup', [ImeiLookupController::class, 'index'])->name('imei-lookup');
    Route::view('/checklist-seminovo', 'tools.checklist')->name('tools.checklist');
    Route::get('/tabela-precos', [ToolController::class, 'priceTable'])->name('tools.price-table');
    Route::view('/mensagens-whatsapp', 'tools.whatsapp-messages')->name('tools.whatsapp-messages');
    Route::view('/ficha-tecnica', 'tools.specs')->name('tools.specs');

    // Follow-ups
    Route::resource('followups', FollowupController::class)->except(['edit', 'show', 'create']);
    Route::post('followups/{followup}/complete', [FollowupController::class, 'complete'])->name('followups.complete');
    Route::post('followups/{followup}/cancel', [FollowupController::class, 'cancel'])->name('followups.cancel');

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
    Route::get('/quotations/import', [QuotationController::class, 'importForm'])->name('quotations.import');
    Route::post('/quotations/import-preview', [QuotationController::class, 'importPreview'])->name('quotations.import-preview');
    Route::post('/quotations/import', [QuotationController::class, 'importStore'])->name('quotations.import-store');
    Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy');
    Route::post('/quotations/bulk-destroy', [QuotationController::class, 'bulkDestroy'])->name('quotations.bulk-destroy');
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

    // Garantias
    Route::get('/warranties', [WarrantyController::class, 'index'])->name('warranties.index');
    Route::get('/warranties/{warranty}', [WarrantyController::class, 'show'])->name('warranties.show');
    Route::post('/warranties/{warranty}/claims', [WarrantyController::class, 'storeClaim'])->name('warranties.claims.store');
    Route::patch('/warranties/claims/{claim}', [WarrantyController::class, 'updateClaim'])->name('warranties.claims.update');

    // Pedidos de Importação
    Route::get('/imports', [ImportOrderController::class, 'index'])->name('imports.index');
    Route::get('/imports/create', [ImportOrderController::class, 'create'])->name('imports.create');
    Route::post('/imports', [ImportOrderController::class, 'store'])->name('imports.store');
    Route::get('/imports/{import}', [ImportOrderController::class, 'show'])->name('imports.show');
    Route::patch('/imports/{import}/status', [ImportOrderController::class, 'updateStatus'])->name('imports.status');
    Route::get('/imports/{import}/receive', [ImportOrderController::class, 'receive'])->name('imports.receive');
    Route::post('/imports/{import}/receive', [ImportOrderController::class, 'confirmReceive'])->name('imports.confirm-receive');
    Route::post('/imports/{import}/cancel', [ImportOrderController::class, 'cancel'])->name('imports.cancel');

    // Reservas
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/search-customers', [ReservationController::class, 'searchCustomers'])->name('reservations.search-customers');
    Route::get('/reservations/search-products', [ReservationController::class, 'searchProducts'])->name('reservations.search-products');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/payments', [ReservationController::class, 'storePayment'])->name('reservations.payments.store');
    Route::get('/reservations/{reservation}/convert', [ReservationController::class, 'convert'])->name('reservations.convert');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Fluxo de Caixa
    Route::get('/cash-register', [CashRegisterController::class, 'index'])->name('cash-register.index');
    Route::post('/cash-register/open', [CashRegisterController::class, 'open'])->name('cash-register.open');
    Route::post('/cash-register/{register}/close', [CashRegisterController::class, 'close'])->name('cash-register.close');
    Route::post('/cash-register/{register}/entry', [CashRegisterController::class, 'addEntry'])->name('cash-register.entry');

    // Avaliação de Seminovos
    Route::get('/valuations', [ValuationController::class, 'index'])->name('valuations.index');
    Route::get('/api/valuations/price', [ValuationController::class, 'getPrice'])->name('valuations.price');
    Route::post('/api/valuations/evaluate', [ValuationController::class, 'evaluate'])->name('valuations.evaluate');

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
