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
use App\Presentation\Http\Controllers\MarketingController;
use App\Presentation\Http\Controllers\ScheduleController;
use App\Presentation\Http\Controllers\CardFeeController;
use App\Presentation\Http\Controllers\ConsignmentStockController;
use App\Presentation\Http\Controllers\NegotiationController;
use App\Presentation\Http\Controllers\Admin\AdminB2BDashboardController;
use App\Presentation\Http\Controllers\Admin\AdminB2BProductController;
use App\Presentation\Http\Controllers\Admin\AdminB2BReportController;
use App\Presentation\Http\Controllers\Admin\AdminB2BOrderController;
use App\Presentation\Http\Controllers\Admin\AdminB2BRetailerController;
use App\Presentation\Http\Controllers\Admin\AdminB2BSettingController;
use App\Presentation\Http\Controllers\Admin\AdminUserController;
use App\Presentation\Http\Controllers\Admin\CommissionController;
use App\Presentation\Http\Controllers\Admin\TimeClockAdminController;
use App\Presentation\Http\Controllers\InternDashboardController;
use App\Presentation\Http\Controllers\TimeClockController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumeDashboardController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumeProductController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumeRetailerController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumeSampleController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumeOrderController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumePaymentController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumeReportController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumeSettingController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumeImportController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumeCustomerController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumeReservationController;
use App\Presentation\Http\Controllers\Admin\Perfumes\AdminPerfumeSaleController;
use Illuminate\Support\Facades\Route;

// Redireciona a raiz para o dashboard ou login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('auth')->get('/keepalive', fn () => response()->json(['ok' => true]))->name('keepalive');

// Rotas autenticadas
Route::middleware(['auth', 'verified'])->group(function () {

    // ----------------------------------------------------------------
    // Perfil — acessível por todos os roles autenticados
    // ----------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ----------------------------------------------------------------
    // Estagiária — dashboard e ponto (role intern)
    // ----------------------------------------------------------------
    Route::middleware('role:intern')->group(function () {
        Route::get('/intern/dashboard', [InternDashboardController::class, 'index'])->name('intern.dashboard');
        Route::post('/intern/time-clock/punch', [TimeClockController::class, 'punch'])->name('intern.time-clock.punch');
    });

    // ----------------------------------------------------------------
    // DG Store — admin_geral, seller e intern
    // ----------------------------------------------------------------
    Route::middleware('role:admin_geral,seller,intern')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Ferramentas
        Route::get('/imei-lookup', [ImeiLookupController::class, 'index'])->name('imei-lookup');
        Route::view('/checklist-seminovo', 'tools.checklist')->name('tools.checklist');
        Route::get('/tabela-precos', [ToolController::class, 'priceTable'])->name('tools.price-table');
        Route::view('/mensagens-whatsapp', 'tools.whatsapp-messages')->name('tools.whatsapp-messages');
        Route::view('/ficha-tecnica', 'tools.specs')->name('tools.specs');
        Route::view('/treinamento-vendas', 'tools.sales-training')->name('tools.sales-training');
        Route::get('/calculadora-stone', [ToolController::class, 'stoneCalculator'])->name('tools.stone-calculator');
        Route::get('/simulador-negociacao', [NegotiationController::class, 'index'])->name('tools.negotiation-simulator');
        Route::post('/api/negotiation/evaluate', [NegotiationController::class, 'evaluate'])->name('negotiation.evaluate');

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
        Route::get('/products/labels/batch', [ProductController::class, 'labelBatch'])->name('products.label-batch');
        Route::resource('products', ProductController::class);
        Route::get('/products/{product}/label', [ProductController::class, 'label'])->name('products.label');
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
        Route::post('/sales/{sale}/followup', [SaleController::class, 'followup'])->name('sales.followup');

        // API - Calculadora de Taxas de Cartão
        Route::post('/api/card-fees/calculate', [CardFeeController::class, 'calculate'])->name('card-fees.calculate');
        Route::post('/api/card-fees/calculate-all', [CardFeeController::class, 'calculateAll'])->name('card-fees.calculate-all');
        Route::post('/api/card-fees/calculate-with-down-payment', [CardFeeController::class, 'calculateWithDownPayment'])->name('card-fees.calculate-with-down-payment');
        Route::post('/api/card-fees/calculate-with-trade-in', [CardFeeController::class, 'calculateWithTradeIn'])->name('card-fees.calculate-with-trade-in');

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

        // Estoque Consignado (Fornecedor Interno)
        Route::get('/stock/consignment', [ConsignmentStockController::class, 'index'])->name('stock.consignment.index');
        Route::get('/stock/consignment/create', [ConsignmentStockController::class, 'create'])->name('stock.consignment.create');
        Route::post('/stock/consignment', [ConsignmentStockController::class, 'store'])->name('stock.consignment.store');
        Route::post('/stock/consignment/store-confirmed', [ConsignmentStockController::class, 'storeConfirmed'])->name('stock.consignment.store-confirmed');
        Route::get('/stock/consignment/{item}/edit', [ConsignmentStockController::class, 'edit'])->name('stock.consignment.edit');
        Route::put('/stock/consignment/{item}', [ConsignmentStockController::class, 'update'])->name('stock.consignment.update');
        Route::post('/stock/consignment/{item}/return', [ConsignmentStockController::class, 'returnItem'])->name('stock.consignment.return');
        Route::get('/stock/consignment/report', [ConsignmentStockController::class, 'report'])->name('stock.consignment.report');
        Route::get('/api/consignment/search', [ConsignmentStockController::class, 'search'])->name('stock.consignment.search');

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

        // Agenda
        Route::prefix('schedule')->name('schedule.')->group(function () {
            Route::get('/', [ScheduleController::class, 'index'])->name('index');
            Route::post('/', [ScheduleController::class, 'store'])->name('store');
            Route::put('/{appointment}', [ScheduleController::class, 'update'])->name('update');
            Route::patch('/{appointment}/status', [ScheduleController::class, 'updateStatus'])->name('update-status');
            Route::delete('/{appointment}', [ScheduleController::class, 'destroy'])->name('destroy');
            Route::get('/available-slots', [ScheduleController::class, 'availableSlots'])->name('available-slots');
            Route::get('/{appointment}/whatsapp/{type}', [ScheduleController::class, 'whatsappMessage'])->name('whatsapp-message');
        });

        // Marketing
        Route::prefix('marketing')->name('marketing.')->group(function () {
            Route::get('/', [MarketingController::class, 'index'])->name('index');
            Route::post('/prices', [MarketingController::class, 'storePrices'])->name('prices.store');
            Route::post('/creatives', [MarketingController::class, 'storeCreative'])->name('creatives.store');
            Route::get('/creatives/{creative}/image', [MarketingController::class, 'showCreativeImage'])->name('creatives.image');
            Route::get('/creatives/{creative}/download', [MarketingController::class, 'downloadCreativeImage'])->name('creatives.download');
            Route::delete('/creatives/{creative}', [MarketingController::class, 'deleteCreative'])->name('creatives.destroy');
            Route::post('/used-listings', [MarketingController::class, 'storeUsedListing'])->name('used-listings.store');
            Route::delete('/used-listings/{listing}', [MarketingController::class, 'deleteUsedListing'])->name('used-listings.destroy');
            Route::post('/resale-items', [MarketingController::class, 'storeResaleItem'])->name('resale-items.store');
            Route::post('/resale-items/{item}/toggle', [MarketingController::class, 'toggleResaleVisibility'])->name('resale-items.toggle');
            Route::post('/price-images', [MarketingController::class, 'storePriceImage'])->name('price-images.store');
            Route::delete('/price-images/{image}', [MarketingController::class, 'deletePriceImage'])->name('price-images.destroy');
            Route::post('/price-images/reorder', [MarketingController::class, 'reorderPriceImages'])->name('price-images.reorder');
        });

        // Avaliação de Seminovos
        Route::get('/valuations', [ValuationController::class, 'index'])->name('valuations.index');
        Route::get('/api/valuations/price', [ValuationController::class, 'getPrice'])->name('valuations.price');
        Route::post('/api/valuations/evaluate', [ValuationController::class, 'evaluate'])->name('valuations.evaluate');

        // ----------------------------------------------------------------
        // Exclusivo admin_geral: Relatórios DG Store e Gestão de Usuários
        // ----------------------------------------------------------------
        Route::middleware('role:admin_geral')->group(function () {
            // Relatórios
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
            Route::get('/reports/sales/pdf', [ReportController::class, 'salesPdf'])->name('reports.sales.pdf');
            Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
            Route::get('/reports/top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');

            // Gestão de Usuários
            Route::prefix('admin/users')->group(function () {
                Route::get('/', [AdminUserController::class, 'index'])->name('admin.users.index');
                Route::get('/create', [AdminUserController::class, 'create'])->name('admin.users.create');
                Route::post('/', [AdminUserController::class, 'store'])->name('admin.users.store');
                Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
                Route::put('/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
            });

            // Gestão de Comissões
            Route::prefix('admin/commissions')->group(function () {
                Route::get('/', [CommissionController::class, 'index'])->name('admin.commissions.index');
                Route::put('/{user}/rate', [CommissionController::class, 'updateRate'])->name('admin.commissions.update-rate');
                Route::post('/withdrawals', [CommissionController::class, 'storeWithdrawal'])->name('admin.commissions.withdrawals.store');
                Route::patch('/withdrawals/{withdrawal}/approve', [CommissionController::class, 'approveWithdrawal'])->name('admin.commissions.withdrawals.approve');
                Route::patch('/withdrawals/{withdrawal}/reject', [CommissionController::class, 'rejectWithdrawal'])->name('admin.commissions.withdrawals.reject');
            });

            // Registro de Ponto (Admin)
            Route::get('/admin/time-clock', [TimeClockAdminController::class, 'index'])->name('admin.time-clock.index');
        });
    });

    // ----------------------------------------------------------------
    // B2B Admin — admin_geral e admin_b2b
    // ----------------------------------------------------------------
    Route::middleware('role:admin_geral,admin_b2b')->prefix('admin/b2b')->group(function () {
        // Dashboard B2B
        Route::get('/', AdminB2BDashboardController::class)->name('admin.b2b.dashboard');

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
        Route::get('/retailers/create', [AdminB2BRetailerController::class, 'create'])->name('admin.b2b.retailers.create');
        Route::post('/retailers', [AdminB2BRetailerController::class, 'store'])->name('admin.b2b.retailers.store');
        Route::get('/retailers/{retailer}', [AdminB2BRetailerController::class, 'show'])->name('admin.b2b.retailers.show');
        Route::get('/retailers/{retailer}/edit', [AdminB2BRetailerController::class, 'edit'])->name('admin.b2b.retailers.edit');
        Route::put('/retailers/{retailer}', [AdminB2BRetailerController::class, 'update'])->name('admin.b2b.retailers.update');
        Route::patch('/retailers/{retailer}/status', [AdminB2BRetailerController::class, 'updateStatus'])->name('admin.b2b.retailers.status');

        // Relatórios B2B
        Route::get('/reports', [AdminB2BReportController::class, 'index'])->name('admin.b2b.reports.index');

        // Configurações B2B
        Route::get('/settings', [AdminB2BSettingController::class, 'index'])->name('admin.b2b.settings.index');
        Route::put('/settings', [AdminB2BSettingController::class, 'update'])->name('admin.b2b.settings.update');
    });

    // ----------------------------------------------------------------
    // Perfumes Admin — admin_geral e admin_perfumes
    // ----------------------------------------------------------------
    Route::middleware('role:admin_geral,admin_perfumes')->prefix('admin/perfumes')->group(function () {
        Route::get('/', AdminPerfumeDashboardController::class)->name('admin.perfumes.dashboard');

        // Produtos
        Route::get('/products', [AdminPerfumeProductController::class, 'index'])->name('admin.perfumes.products.index');
        Route::get('/products/create', [AdminPerfumeProductController::class, 'create'])->name('admin.perfumes.products.create');
        Route::post('/products', [AdminPerfumeProductController::class, 'store'])->name('admin.perfumes.products.store');
        Route::get('/products/{product}/edit', [AdminPerfumeProductController::class, 'edit'])->name('admin.perfumes.products.edit');
        Route::put('/products/{product}', [AdminPerfumeProductController::class, 'update'])->name('admin.perfumes.products.update');
        Route::delete('/products/{product}', [AdminPerfumeProductController::class, 'destroy'])->name('admin.perfumes.products.destroy');

        // Lojistas
        Route::get('/retailers', [AdminPerfumeRetailerController::class, 'index'])->name('admin.perfumes.retailers.index');
        Route::get('/retailers/create', [AdminPerfumeRetailerController::class, 'create'])->name('admin.perfumes.retailers.create');
        Route::post('/retailers', [AdminPerfumeRetailerController::class, 'store'])->name('admin.perfumes.retailers.store');
        Route::get('/retailers/{retailer}', [AdminPerfumeRetailerController::class, 'show'])->name('admin.perfumes.retailers.show');
        Route::get('/retailers/{retailer}/edit', [AdminPerfumeRetailerController::class, 'edit'])->name('admin.perfumes.retailers.edit');
        Route::put('/retailers/{retailer}', [AdminPerfumeRetailerController::class, 'update'])->name('admin.perfumes.retailers.update');

        // Amostras
        Route::get('/samples', [AdminPerfumeSampleController::class, 'index'])->name('admin.perfumes.samples.index');
        Route::get('/samples/create', [AdminPerfumeSampleController::class, 'create'])->name('admin.perfumes.samples.create');
        Route::post('/samples', [AdminPerfumeSampleController::class, 'store'])->name('admin.perfumes.samples.store');
        Route::patch('/samples/{sample}/return', [AdminPerfumeSampleController::class, 'markReturned'])->name('admin.perfumes.samples.return');

        // Pedidos
        Route::get('/orders', [AdminPerfumeOrderController::class, 'index'])->name('admin.perfumes.orders.index');
        Route::get('/orders/create', [AdminPerfumeOrderController::class, 'create'])->name('admin.perfumes.orders.create');
        Route::post('/orders', [AdminPerfumeOrderController::class, 'store'])->name('admin.perfumes.orders.store');
        Route::get('/orders/{order}', [AdminPerfumeOrderController::class, 'show'])->name('admin.perfumes.orders.show');
        Route::patch('/orders/{order}/status', [AdminPerfumeOrderController::class, 'updateStatus'])->name('admin.perfumes.orders.status');

        // Pagamentos
        Route::post('/orders/{order}/payments', [AdminPerfumePaymentController::class, 'store'])->name('admin.perfumes.payments.store');
        Route::delete('/payments/{payment}', [AdminPerfumePaymentController::class, 'destroy'])->name('admin.perfumes.payments.destroy');

        // Importação PDF
        Route::get('/import', [AdminPerfumeImportController::class, 'index'])->name('admin.perfumes.import');
        Route::post('/import', [AdminPerfumeImportController::class, 'store'])->name('admin.perfumes.import.store');
        Route::get('/import/progress', [AdminPerfumeImportController::class, 'progress'])->name('admin.perfumes.import.progress');
        Route::delete('/import/clear', [AdminPerfumeImportController::class, 'clear'])->name('admin.perfumes.import.clear');

        // Relatórios
        Route::get('/reports', [AdminPerfumeReportController::class, 'index'])->name('admin.perfumes.reports.index');

        // Configurações
        Route::get('/settings', [AdminPerfumeSettingController::class, 'index'])->name('admin.perfumes.settings.index');
        Route::put('/settings', [AdminPerfumeSettingController::class, 'update'])->name('admin.perfumes.settings.update');
        Route::put('/settings/dollar-rate', [AdminPerfumeSettingController::class, 'updateDollarRate'])->name('admin.perfumes.settings.dollar-rate');

        // ===== B2C - Varejo =====

        // Clientes
        Route::get('/customers', [AdminPerfumeCustomerController::class, 'index'])->name('admin.perfumes.customers.index');
        Route::get('/customers/create', [AdminPerfumeCustomerController::class, 'create'])->name('admin.perfumes.customers.create');
        Route::post('/customers', [AdminPerfumeCustomerController::class, 'store'])->name('admin.perfumes.customers.store');
        Route::get('/customers/{customer}', [AdminPerfumeCustomerController::class, 'show'])->name('admin.perfumes.customers.show');
        Route::get('/customers/{customer}/edit', [AdminPerfumeCustomerController::class, 'edit'])->name('admin.perfumes.customers.edit');
        Route::put('/customers/{customer}', [AdminPerfumeCustomerController::class, 'update'])->name('admin.perfumes.customers.update');
        Route::delete('/customers/{customer}', [AdminPerfumeCustomerController::class, 'destroy'])->name('admin.perfumes.customers.destroy');

        // Encomendas (Reservations)
        Route::get('/reservations', [AdminPerfumeReservationController::class, 'index'])->name('admin.perfumes.reservations.index');
        Route::get('/reservations/create', [AdminPerfumeReservationController::class, 'create'])->name('admin.perfumes.reservations.create');
        Route::post('/reservations', [AdminPerfumeReservationController::class, 'store'])->name('admin.perfumes.reservations.store');
        Route::get('/reservations/{reservation}', [AdminPerfumeReservationController::class, 'show'])->name('admin.perfumes.reservations.show');
        Route::put('/reservations/{reservation}', [AdminPerfumeReservationController::class, 'update'])->name('admin.perfumes.reservations.update');
        Route::post('/reservations/{reservation}/payments', [AdminPerfumeReservationController::class, 'storePayment'])->name('admin.perfumes.reservation-payments.store');
        Route::delete('/reservations/{reservation}/payments/{payment}', [AdminPerfumeReservationController::class, 'destroyPayment'])->name('admin.perfumes.reservation-payments.destroy');
        Route::post('/reservations/{reservation}/convert', [AdminPerfumeReservationController::class, 'convert'])->name('admin.perfumes.reservations.convert');
        Route::delete('/reservations/{reservation}', [AdminPerfumeReservationController::class, 'cancel'])->name('admin.perfumes.reservations.cancel');

        // Vendas B2C
        Route::get('/sales', [AdminPerfumeSaleController::class, 'index'])->name('admin.perfumes.sales.index');
        Route::get('/sales/create', [AdminPerfumeSaleController::class, 'create'])->name('admin.perfumes.sales.create');
        Route::post('/sales', [AdminPerfumeSaleController::class, 'store'])->name('admin.perfumes.sales.store');
        Route::get('/sales/{sale}', [AdminPerfumeSaleController::class, 'show'])->name('admin.perfumes.sales.show');
        Route::delete('/sales/{sale}', [AdminPerfumeSaleController::class, 'cancel'])->name('admin.perfumes.sales.cancel');
    });
});

// Rotas públicas (sem autenticação)
Route::view('/compare', 'public.specs-compare')->name('public.specs-compare');

require __DIR__.'/auth.php';
