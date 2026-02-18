<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Admin\Perfumes;

use App\Domain\Perfumes\Services\PerfumeImportService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminPerfumeImportController extends Controller
{
    public function index()
    {
        return view('admin.perfumes.import');
    }

    public function store(Request $request, PerfumeImportService $importService)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $result = $importService->importFromPdf($request->file('pdf_file'));

        return redirect()->route('admin.perfumes.import')
            ->with('success', "Importação concluída: {$result['created']} criados, {$result['updated']} atualizados, {$result['skipped']} ignorados.");
    }
}
