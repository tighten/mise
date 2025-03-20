<?php

namespace App\Recipes;

use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\warning;

abstract class Recipe
{
    public function step(string $stepName, ...$params): void
    {
        // @todo Convert string step names, build step, and pass params

        $relativeClass = implode('\\', collect(explode('/', $stepName))->map(fn (string $part) => Str::title($part))->toArray());
        $actualClass = "App\\Steps\\{$relativeClass}";
        if (class_exists($actualClass)) {
            warning("Run step: {$relativeClass}..");
            app($actualClass)();
        } else {
            error("Unable to perform '{$actualClass}'. Step not found.");
        }
    }

    public function confirm(string $label, bool $default = true): bool
    {
        return confirm($label, default: $default);
    }
}
