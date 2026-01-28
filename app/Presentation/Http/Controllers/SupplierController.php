<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Supplier\DTOs\SupplierData;
use App\Domain\Supplier\Models\Supplier;
use App\Domain\Supplier\Services\QuotationService;
use App\Domain\Supplier\Services\SupplierService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StoreSupplierRequest;
use App\Presentation\Http\Requests\UpdateSupplierRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierService $supplierService,
        private readonly QuotationService $quotationService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->get('search');
        $active = $request->has('active') ? $request->boolean('active') : null;
        
        $suppliers = $this->supplierService->list(15, $search, $active);

        return view('suppliers.index', [
            'suppliers' => $suppliers,
            'search' => $search,
            'active' => $active,
        ]);
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $data = SupplierData::fromArray($request->validated());
        $supplier = $this->supplierService->create($data);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Fornecedor cadastrado com sucesso!');
    }

    public function show(Supplier $supplier): View
    {
        $quotations = $this->quotationService->getForSupplier($supplier->id, 20);

        return view('suppliers.show', [
            'supplier' => $supplier,
            'quotations' => $quotations,
        ]);
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $data = SupplierData::fromArray($request->validated());
        $this->supplierService->update($supplier, $data);

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Fornecedor atualizado com sucesso!');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->supplierService->delete($supplier);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fornecedor excluído com sucesso!');
    }

    /**
     * Busca de fornecedores para autocomplete (API)
     */
    public function search(Request $request): JsonResponse
    {
        $term = $request->get('q', '');
        
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $suppliers = $this->supplierService->search($term);

        return response()->json(
            $suppliers->map(fn(Supplier $supplier) => [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'cnpj' => $supplier->formatted_cnpj,
                'phone' => $supplier->formatted_phone,
            ])
        );
    }
}
