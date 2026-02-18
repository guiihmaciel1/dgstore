<?php

use App\Presentation\Http\Controllers\B2B\B2BAuthController;
use App\Presentation\Http\Controllers\B2B\B2BCatalogController;
use App\Presentation\Http\Controllers\B2B\B2BCartController;
use App\Presentation\Http\Controllers\B2B\B2BOrderController;
use Illuminate\Support\Facades\Route;

// Rotas públicas B2B (guest)
Route::middleware('guest:b2b')->group(function () {
    Route::get('/login', [B2BAuthController::class, 'showLogin'])->name('b2b.login');
    Route::post('/login', [B2BAuthController::class, 'login'])->name('b2b.login.submit');
    Route::get('/register', [B2BAuthController::class, 'showRegister'])->name('b2b.register');
    Route::post('/register', [B2BAuthController::class, 'register'])->name('b2b.register.submit');
    Route::get('/register/success', [B2BAuthController::class, 'registerSuccess'])->name('b2b.register.success');
});

// Rota de logout (precisa estar autenticado)
Route::post('/logout', [B2BAuthController::class, 'logout'])->name('b2b.logout')->middleware('b2b.auth');

// Rota de aprovação pendente (autenticado mas não necessariamente aprovado)
Route::get('/pending', [B2BAuthController::class, 'pending'])->name('b2b.pending')->middleware('b2b.auth');

// Rotas protegidas B2B (autenticado + aprovado)
Route::middleware(['b2b.auth', 'b2b.approved'])->group(function () {
    // Catálogo
    Route::get('/catalog', [B2BCatalogController::class, 'index'])->name('b2b.catalog');

    // Carrinho
    Route::get('/cart', [B2BCartController::class, 'index'])->name('b2b.cart');
    Route::post('/cart/add', [B2BCartController::class, 'add'])->name('b2b.cart.add');
    Route::put('/cart/update', [B2BCartController::class, 'update'])->name('b2b.cart.update');
    Route::delete('/cart/remove', [B2BCartController::class, 'remove'])->name('b2b.cart.remove');

    // Pedidos
    Route::get('/orders', [B2BOrderController::class, 'index'])->name('b2b.orders');
    Route::post('/orders', [B2BOrderController::class, 'store'])->name('b2b.orders.store');
    Route::get('/orders/{order}', [B2BOrderController::class, 'show'])->name('b2b.orders.show');
    Route::post('/orders/{order}/repeat', [B2BOrderController::class, 'repeat'])->name('b2b.orders.repeat');

    // Pagamento PIX
    Route::get('/orders/{order}/pix', [B2BOrderController::class, 'pix'])->name('b2b.orders.pix');
    Route::post('/orders/{order}/simulate-payment', [B2BOrderController::class, 'simulatePayment'])->name('b2b.orders.simulate-payment');
});
