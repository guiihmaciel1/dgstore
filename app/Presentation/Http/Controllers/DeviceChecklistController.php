<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Checklist\Models\DeviceChecklist;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceChecklistController extends Controller
{
    public function index(Request $request): View
    {
        $query = DeviceChecklist::with('user', 'product', 'tradeIn')
            ->latest();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $checklists = $query->paginate(20)->withQueryString();

        return view('checklists.index', compact('checklists'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'device_info' => 'nullable|array',
            'sections' => 'required|array',
        ]);

        $sections = $validated['sections'];
        $totalItems = 0;
        $passedItems = 0;
        $failedItems = 0;

        foreach ($sections as $section) {
            foreach ($section['items'] ?? [] as $item) {
                $totalItems++;
                if (($item['status'] ?? '') === 'ok') $passedItems++;
                if (($item['status'] ?? '') === 'fail') $failedItems++;
            }
        }

        $status = 'incomplete';
        if ($totalItems > 0 && ($passedItems + $failedItems) === $totalItems) {
            $status = $failedItems === 0 ? 'approved' : 'failed';
        }

        $checklist = DeviceChecklist::create([
            'name' => $validated['name'],
            'device_info' => $validated['device_info'],
            'sections' => $sections,
            'total_items' => $totalItems,
            'passed_items' => $passedItems,
            'failed_items' => $failedItems,
            'status' => $status,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'id' => $checklist->id,
            'redirect' => route('checklists.index'),
        ]);
    }

    public function show(DeviceChecklist $checklist): View
    {
        $checklist->load('user', 'product', 'tradeIn');
        return view('checklists.show', compact('checklist'));
    }

    public function destroy(DeviceChecklist $checklist): RedirectResponse
    {
        if ($checklist->isLinked()) {
            return back()->with('error', 'Não é possível excluir um checklist vinculado a um produto ou trade-in.');
        }

        $checklist->delete();

        return redirect()
            ->route('checklists.index')
            ->with('success', 'Checklist excluído com sucesso.');
    }

    public function apiSearch(Request $request): JsonResponse
    {
        $search = $request->get('q', '');

        $results = DeviceChecklist::where('name', 'like', "%{$search}%")
            ->latest()
            ->limit(15)
            ->get(['id', 'name', 'status', 'passed_items', 'total_items', 'failed_items'])
            ->map(fn (DeviceChecklist $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'status' => $c->status,
                'summary' => $c->summary_label,
            ]);

        return response()->json($results);
    }
}
