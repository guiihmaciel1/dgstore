<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Models\PerfumeCustomer;
use App\Domain\Perfumes\Services\PerfumeCustomerService;
use App\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Perfumes\StorePerfumeCustomerRequest;
use App\Presentation\Http\Requests\Perfumes\UpdatePerfumeCustomerRequest;
use Illuminate\Http\Request;

class AdminPerfumeCustomerController extends Controller
{
    public function __construct(
        private PerfumeCustomerService $customerService
    ) {}

    public function index(Request $request)
    {
        $query = PerfumeCustomer::query();

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        $customers = $query->withCount(['sales', 'reservations'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.perfumes.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.perfumes.customers.create');
    }

    public function store(StorePerfumeCustomerRequest $request)
    {
        $this->customerService->create($request->validated());

        return redirect()->route('admin.perfumes.customers.index')
            ->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(PerfumeCustomer $customer)
    {
        $customer->load([
            'sales' => fn ($q) => $q->latest()->take(10),
            'sales.items',
            'reservations' => fn ($q) => $q->latest()->take(5),
        ]);

        $totalPurchases = $customer->sales()->count();
        $totalSpent = $customer->sales()->sum('total');
        $lastPurchase = $customer->sales()->latest('sold_at')->first()?->sold_at;
        $activeReservations = $customer->reservations()->where('status', 'active')->count();

        return view('admin.perfumes.customers.show', compact(
            'customer',
            'totalPurchases',
            'totalSpent',
            'lastPurchase',
            'activeReservations'
        ));
    }

    public function edit(PerfumeCustomer $customer)
    {
        return view('admin.perfumes.customers.edit', compact('customer'));
    }

    public function update(UpdatePerfumeCustomerRequest $request, PerfumeCustomer $customer)
    {
        $this->customerService->update($customer, $request->validated());

        return redirect()->route('admin.perfumes.customers.show', $customer)
            ->with('success', 'Cliente atualizado com sucesso.');
    }

    public function destroy(PerfumeCustomer $customer)
    {
        if ($customer->sales()->exists()) {
            return back()->with('error', 'Não é possível excluir um cliente que possui vendas.');
        }

        $this->customerService->delete($customer);

        return redirect()->route('admin.perfumes.customers.index')
            ->with('success', 'Cliente excluído com sucesso.');
    }
}
