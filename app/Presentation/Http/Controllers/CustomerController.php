<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Customer\DTOs\CustomerData;
use App\Domain\Customer\Models\Customer;
use App\Domain\Customer\Services\CustomerService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\StoreCustomerRequest;
use App\Presentation\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->get('search');
        $customers = $this->customerService->list(15, $search);

        return view('customers.index', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $data = CustomerData::fromArray($request->validated());
        $customer = $this->customerService->create($data);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Customer $customer): View
    {
        $customer = $this->customerService->getWithPurchaseHistory($customer->id);

        return view('customers.show', [
            'customer' => $customer,
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', [
            'customer' => $customer,
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $data = CustomerData::fromArray($request->validated());
        $this->customerService->update($customer, $data);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->customerService->delete($customer);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }

    /**
     * Criação rápida de cliente via AJAX (retorna JSON)
     */
    public function storeQuick(StoreCustomerRequest $request): JsonResponse
    {
        $data = CustomerData::fromArray($request->validated());
        $customer = $this->customerService->create($data);

        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->formatted_phone,
            'email' => $customer->email,
        ]);
    }

    /**
     * Busca de clientes para autocomplete (API)
     */
    public function search(Request $request)
    {
        $term = $request->get('q', '');
        
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $customers = $this->customerService->search($term);

        return response()->json(
            $customers->map(fn(Customer $customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->formatted_phone,
                'email' => $customer->email,
            ])
        );
    }
}
