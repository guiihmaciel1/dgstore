<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Infrastructure\Repositories\EloquentCustomerRepository;
use App\Infrastructure\Repositories\EloquentProductRepository;
use App\Infrastructure\Repositories\EloquentSaleRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Mapeamento de interfaces para implementações
     */
    public array $bindings = [
        ProductRepositoryInterface::class => EloquentProductRepository::class,
        CustomerRepositoryInterface::class => EloquentCustomerRepository::class,
        SaleRepositoryInterface::class => EloquentSaleRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->bindings as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
