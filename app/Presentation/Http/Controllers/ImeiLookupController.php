<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ImeiLookupController extends Controller
{
    public function index(): View
    {
        return view('imei-lookup.index');
    }
}
