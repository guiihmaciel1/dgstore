<?php

namespace App\Providers;

use App\Domain\Customer\Models\Customer;
use App\Domain\Followup\Models\Followup;
use App\Domain\Product\Models\Product;
use App\Domain\Sale\Models\Sale;
use App\Policies\CustomerPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SalePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registra as Policies
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);

        // Compartilha contagem de follow-ups pendentes com a navigation
        View::composer('layouts.navigation', function ($view) {
            $pendingFollowups = 0;
            if (auth()->check()) {
                $pendingFollowups = Followup::where('user_id', auth()->id())
                    ->where('status', 'pending')
                    ->where('due_date', '<=', today())
                    ->count();
            }
            $view->with('pendingFollowups', $pendingFollowups);
        });
    }
}
