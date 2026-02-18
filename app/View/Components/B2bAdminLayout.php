<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class B2bAdminLayout extends Component
{
    public function render(): View
    {
        return view('b2b.layouts.admin');
    }
}
