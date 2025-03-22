<?php

namespace App\Prompts;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\warning;

if (! function_exists('\App\Prompts\configure')) {
    function configure(Target $target, string $message): void
    {
        $messagePattern = 'Configuring %s: %s';
        match ($target) {
            Target::Recipe => intro(sprintf($messagePattern, $target->value, $message)),
            Target::Step => warning(sprintf($messagePattern, $target->value, $message)),
        };
    }
}

if (! function_exists('\App\Prompts\apply')) {
    function apply(Target $target, string $message): void
    {
        $messagePattern = 'Applying %s: %s';
        match ($target) {
            Target::Recipe => intro(sprintf($messagePattern, $target->value, $message)),
            Target::Step => warning(sprintf($messagePattern, $target->value, $message)),
        };
    }
}
