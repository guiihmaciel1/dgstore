<?php

namespace App\Providers;

use App\Domain\CRM\Models\Deal;
use App\Domain\Customer\Models\Customer;
use App\Domain\Product\Models\Product;
use App\Domain\Sale\Models\Sale;
use App\Domain\Valuation\Services\DgifipeApiClient;
use App\Policies\CustomerPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SalePolicy;
use Illuminate\Support\Facades\DB;
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
        $this->app->singleton(DgifipeApiClient::class, function () {
            return new DgifipeApiClient(
                baseUrl: config('services.dgifipe.base_url', 'https://ifipe.dgstorerp.com.br'),
                token: config('services.dgifipe.token', ''),
                timeout: (int) config('services.dgifipe.timeout', 10),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::prohibitDestructiveCommands();

        // Registra as Policies
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);

        // Compartilha contagem de deals abertos com a navigation
        View::composer('layouts.navigation', function ($view) {
            $openDealsCount = 0;

            if (auth()->check()) {
                $openDealsCount = Deal::open()->count();
            }

            $view->with('openDealsCount', $openDealsCount);
        });
    }
}
