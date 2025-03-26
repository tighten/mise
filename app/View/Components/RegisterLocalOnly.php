<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Context;
use Illuminate\View\Component;

class RegisterLocalOnly extends Component
{
    public function render(): View
    {
        return view('components.register-local-only', [
            'register_local_only_providers' => Context::get('register_local_only_providers'),
        ]);
    }
}
