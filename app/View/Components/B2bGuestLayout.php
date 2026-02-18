<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class B2bGuestLayout extends Component
{
    public function render(): View
    {
        return view('b2b.layouts.guest');
    }
}
