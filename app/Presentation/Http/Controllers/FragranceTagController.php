<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Fragrance\Models\FragranceTag;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FragranceTagController extends Controller
{
    public function index(): View
    {
        $tags = FragranceTag::ordered()
            ->withCount('products')
            ->get();

        return view('fragrances.tags.index', compact('tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:50'],
            'color'      => ['required', 'string', 'max:7'],
            'icon'       => ['nullable', 'string', 'max:10'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        FragranceTag::create($data);

        return back()->with('success', "Tag \"{$data['name']}\" criada com sucesso.");
    }

    public function update(Request $request, FragranceTag $tag): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:50'],
            'color'      => ['required', 'string', 'max:7'],
            'icon'       => ['nullable', 'string', 'max:10'],
            'sort_order' => ['integer', 'min:0'],
            'active'     => ['boolean'],
        ]);

        $tag->update($data);

        return back()->with('success', "Tag \"{$tag->name}\" atualizada.");
    }

    public function destroy(FragranceTag $tag): RedirectResponse
    {
        $name = $tag->name;
        $tag->delete();

        return back()->with('success', "Tag \"{$name}\" removida.");
    }
}
