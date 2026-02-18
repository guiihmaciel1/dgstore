<?php

use App\Http\Controllers\ProfileController;
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
use App\Presentation\Http\Controllers\ImeiLookupController;
use App\Presentation\Http\Controllers\ToolController;
use App\Presentation\Http\Controllers\WarrantyController;
use App\Presentation\Http\Controllers\CrmController;
use App\Presentation\Http\Controllers\FinanceController;
use App\Presentation\Http\Controllers\Admin\AdminB2BProductController;
use App\Presentation\Http\Controllers\Admin\AdminB2BOrderController;
use App\Presentation\Http\Controllers\Admin\AdminB2BRetailerController;
use App\Presentation\Http\Controllers\Admin\AdminB2BSettingController;
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

    // Follow-ups (legado - redireciona para CRM)
    Route::get('followups', fn() => redirect()->route('crm.board'))->name('followups.index');

    // CRM - Pipeline de Vendas
    Route::get('/crm', [CrmController::class, 'board'])->name('crm.board');
    Route::get('/crm/history', [CrmController::class, 'history'])->name('crm.history');
    Route::post('/crm/deals', [CrmController::class, 'store'])->name('crm.deals.store');
    Route::get('/crm/deals/{deal}', [CrmController::class, 'show'])->name('crm.show');
    Route::put('/crm/deals/{deal}', [CrmController::class, 'update'])->name('crm.deals.update');
    Route::delete('/crm/deals/{deal}', [CrmController::class, 'destroy'])->name('crm.deals.destroy');
    Route::post('/crm/deals/{deal}/move', [CrmController::class, 'moveStage'])->name('crm.deals.move');
    Route::post('/crm/deals/{deal}/win', [CrmController::class, 'win'])->name('crm.deals.win');
    Route::post('/crm/deals/{deal}/lose', [CrmController::class, 'lose'])->name('crm.deals.lose');
    Route::post('/crm/deals/{deal}/reopen', [CrmController::class, 'reopen'])->name('crm.deals.reopen');
    Route::post('/crm/deals/{deal}/activities', [CrmController::class, 'storeActivity'])->name('crm.deals.activities.store');
    Route::post('/api/crm/deals/{deal}/ai-message', [CrmController::class, 'aiSuggestMessage'])->name('crm.deals.ai-message');
    Route::post('/api/crm/deals/{deal}/ai-analysis', [CrmController::class, 'aiAnalyzeDeal'])->name('crm.deals.ai-analysis');

    // Produtos
    Route::resource('products', ProductController::class);
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/api/products/generate-sku', [ProductController::class, 'generateSku'])->name('products.generate-sku');
    Route::post('/api/products/store-quick', [ProductController::class, 'storeQuick'])->name('products.store-quick');

    // Clientes
    Route::resource('customers', CustomerController::class);
    Route::get('/api/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::post('/api/customers/store-quick', [CustomerController::class, 'storeQuick'])->name('customers.store-quick');

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
    Route::post('/api/quotations/ai-analysis', [QuotationController::class, 'aiAnalysis'])->name('quotations.ai-analysis');
    Route::post('/api/quotations/ai-suggestion', [QuotationController::class, 'aiPurchaseSuggestion'])->name('quotations.ai-suggestion');

    // Vendas
    Route::resource('sales', SaleController::class);
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
    Route::post('/api/stock/store-quick', [StockController::class, 'storeQuick'])->name('stock.store-quick');
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
    Route::get('/api/imports/items/search', [ImportOrderController::class, 'searchItems'])->name('imports.items.search');
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
    Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
    Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::post('/reservations/{reservation}/payments', [ReservationController::class, 'storePayment'])->name('reservations.payments.store');
    Route::delete('/reservations/{reservation}/payments/{payment}', [ReservationController::class, 'destroyPayment'])->name('reservations.payments.destroy');
    Route::get('/reservations/{reservation}/convert', [ReservationController::class, 'convert'])->name('reservations.convert');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Financeiro
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/payables', [FinanceController::class, 'payables'])->name('finance.payables');
    Route::get('/finance/receivables', [FinanceController::class, 'receivables'])->name('finance.receivables');
    Route::get('/finance/accounts', [FinanceController::class, 'accounts'])->name('finance.accounts');
    Route::post('/finance/accounts', [FinanceController::class, 'storeAccount'])->name('finance.accounts.store');
    Route::post('/finance/transfers', [FinanceController::class, 'storeTransfer'])->name('finance.transfers.store');
    Route::get('/finance/categories', [FinanceController::class, 'categories'])->name('finance.categories');
    Route::post('/finance/categories', [FinanceController::class, 'storeCategory'])->name('finance.categories.store');
    Route::delete('/finance/categories/{category}', [FinanceController::class, 'destroyCategory'])->name('finance.categories.destroy');
    Route::post('/finance/transactions', [FinanceController::class, 'storeTransaction'])->name('finance.transactions.store');
    Route::post('/finance/transactions/{transaction}/pay', [FinanceController::class, 'payTransaction'])->name('finance.transactions.pay');
    Route::post('/finance/transactions/{transaction}/cancel', [FinanceController::class, 'cancelTransaction'])->name('finance.transactions.cancel');

    // Avaliação de Seminovos
    Route::get('/valuations', [ValuationController::class, 'index'])->name('valuations.index');
    Route::get('/api/valuations/price', [ValuationController::class, 'getPrice'])->name('valuations.price');
    Route::post('/api/valuations/evaluate', [ValuationController::class, 'evaluate'])->name('valuations.evaluate');

    // Admin B2B (apenas admin)
    Route::middleware('role:admin')->prefix('admin/b2b')->group(function () {
        // Produtos B2B
        Route::get('/products', [AdminB2BProductController::class, 'index'])->name('admin.b2b.products.index');
        Route::get('/products/create', [AdminB2BProductController::class, 'create'])->name('admin.b2b.products.create');
        Route::post('/products', [AdminB2BProductController::class, 'store'])->name('admin.b2b.products.store');
        Route::get('/products/{product}/edit', [AdminB2BProductController::class, 'edit'])->name('admin.b2b.products.edit');
        Route::put('/products/{product}', [AdminB2BProductController::class, 'update'])->name('admin.b2b.products.update');
        Route::delete('/products/{product}', [AdminB2BProductController::class, 'destroy'])->name('admin.b2b.products.destroy');

        // Pedidos B2B
        Route::get('/orders', [AdminB2BOrderController::class, 'index'])->name('admin.b2b.orders.index');
        Route::get('/orders/{order}', [AdminB2BOrderController::class, 'show'])->name('admin.b2b.orders.show');
        Route::patch('/orders/{order}/status', [AdminB2BOrderController::class, 'updateStatus'])->name('admin.b2b.orders.status');

        // Lojistas B2B
        Route::get('/retailers', [AdminB2BRetailerController::class, 'index'])->name('admin.b2b.retailers.index');
        Route::get('/retailers/{retailer}', [AdminB2BRetailerController::class, 'show'])->name('admin.b2b.retailers.show');
        Route::patch('/retailers/{retailer}/status', [AdminB2BRetailerController::class, 'updateStatus'])->name('admin.b2b.retailers.status');

        // Configurações B2B
        Route::get('/settings', [AdminB2BSettingController::class, 'index'])->name('admin.b2b.settings.index');
        Route::put('/settings', [AdminB2BSettingController::class, 'update'])->name('admin.b2b.settings.update');
    });

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

// Rotas públicas (sem autenticação)
Route::view('/compare', 'public.specs-compare')->name('public.specs-compare');

require __DIR__.'/auth.php';
