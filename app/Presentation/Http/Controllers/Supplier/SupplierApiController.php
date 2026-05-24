<?php

namespace App\Presentation\Http\Controllers\Supplier;

use App\Domain\ConsignmentStock\Services\ConsignmentStockService;
use App\Domain\ConsignmentStock\Services\ProductCatalogService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierApiController extends Controller
{
    public function __construct(
        private ProductCatalogService $catalogService,
        private ConsignmentStockService $consignmentService
    ) {}

    public function productCatalog(Request $request): JsonResponse
    {
        $term = $request->input('q', '');
        $limit = (int) $request->input('limit', 20);

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $results = $this->catalogService->searchByTerm($term, $limit);

        return response()->json($results);
    }

    public function validateImei(Request $request): JsonResponse
    {
        $imei = $request->input('imei');
        $serialNumber = $request->input('serial_number');
        $excludeId = $request->input('exclude_id');

        if (!$imei && !$serialNumber) {
            return response()->json([
                'valid' => true,
                'message' => '',
            ]);
        }

        $exists = $this->consignmentService->imeiOrSerialExists(
            $imei,
            $serialNumber,
            $excludeId
        );

        if ($exists) {
            $identifier = $imei ?? $serialNumber;
            return response()->json([
                'valid' => false,
                'message' => "IMEI/Serial {$identifier} já existe no sistema.",
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'IMEI/Serial disponível.',
        ]);
    }
}
