<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Sale\Models\Sale;
use App\Domain\User\Models\User;

class SalePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sale $sale): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can cancel the sale.
     */
    public function cancel(User $user, Sale $sale): bool
    {
        // Admin pode cancelar qualquer venda
        if ($user->isAdmin()) {
            return true;
        }

        // Vendedor só pode cancelar suas próprias vendas do mesmo dia
        return $sale->user_id === $user->id && $sale->sold_at->isToday();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sale $sale): bool
    {
        return $user->isAdmin() && $sale->isCancelled();
    }

    /**
     * Determine whether the user can view financial reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->isAdmin();
    }
}
