<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\B2B;

use App\Domain\B2B\Services\B2BProductService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class B2BCatalogController extends Controller
{
    public function __construct(
        private readonly B2BProductService $productService,
    ) {}

    public function index(Request $request): View
    {
        $products = $this->productService->listForCatalog(
            search: $request->get('search'),
            model: $request->get('model'),
            condition: $request->get('condition'),
        );

        $availableModels = $this->productService->getAvailableModels();

        return view('b2b.catalog.index', compact('products', 'availableModels'));
    }
}
