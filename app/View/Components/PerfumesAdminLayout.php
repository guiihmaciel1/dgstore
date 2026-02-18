<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class PerfumesAdminLayout extends Component
{
    public function render(): View
    {
        return view('perfumes.layouts.admin');
    }
}
