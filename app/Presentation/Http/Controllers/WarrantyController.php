<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Warranty\Enums\WarrantyClaimStatus;
use App\Domain\Warranty\Enums\WarrantyClaimType;
use App\Domain\Warranty\Models\Warranty;
use App\Domain\Warranty\Models\WarrantyClaim;
use App\Domain\Warranty\Services\WarrantyService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarrantyController extends Controller
{
    public function __construct(
        private readonly WarrantyService $warrantyService
    ) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'sort' => $request->get('sort', 'created_at'),
            'direction' => $request->get('direction', 'desc'),
        ];

        $warranties = $this->warrantyService->list(15, $filters);

        $stats = [
            'expiring_soon' => $this->warrantyService->countExpiringSoon(30),
            'open_claims' => $this->warrantyService->countOpenClaims(),
        ];

        return view('warranties.index', [
            'warranties' => $warranties,
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    public function show(Warranty $warranty): View
    {
        $warranty->load(['saleItem.sale.customer', 'saleItem.product', 'claims.user']);

        return view('warranties.show', [
            'warranty' => $warranty,
            'claimTypes' => WarrantyClaimType::cases(),
        ]);
    }

    public function storeClaim(Request $request, Warranty $warranty): RedirectResponse
    {
        $request->validate([
            'type' => ['required', 'in:supplier,customer'],
            'reason' => ['required', 'string', 'min:10'],
        ]);

        try {
            $this->warrantyService->openClaim(
                $warranty,
                auth()->id(),
                WarrantyClaimType::from($request->type),
                $request->reason
            );

            return redirect()
                ->route('warranties.show', $warranty)
                ->with('success', 'Acionamento de garantia registrado com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    public function updateClaim(Request $request, WarrantyClaim $claim): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:opened,in_progress,resolved,denied'],
            'resolution' => ['nullable', 'string'],
        ]);

        try {
            $status = WarrantyClaimStatus::from($request->status);
            
            if ($status->isClosed() && empty($request->resolution)) {
                return redirect()
                    ->back()
                    ->with('error', 'É necessário informar a resolução ao fechar o acionamento.');
            }

            $this->warrantyService->updateClaimStatus(
                $claim,
                $status,
                $request->resolution
            );

            return redirect()
                ->route('warranties.show', $claim->warranty_id)
                ->with('success', 'Status do acionamento atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
