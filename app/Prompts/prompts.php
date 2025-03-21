<?php

namespace App\Prompts;

use function Laravel\Prompts\intro;

if (! function_exists('\App\Prompts\configure')) {
    /**
     * Display a warning.
     */
    function configure(Target $target, string $message): void
    {
        intro(sprintf('Configuring %s: %s', $target->value, $message));
    }
}
