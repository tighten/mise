<?php

namespace Tighten\Mise\Recipes;

use function Laravel\Prompts\select;

class LaravelBreeze extends Recipe
{
    public string $key = 'laravel-breeze';

    public function __invoke(): void
    {
        $stack = select('Which breeze stack would you like to install?', [
            'blade' => 'Blade with Alpine (not functional yet)',
            'livewire' => 'Livewire (Volt Class API) with Alpine (not functional yet)',
            'livewire-functional' => 'Livewire (Volt Functional API) with Alpine (not functional yet)',
            'react' => 'React with Inertia (not functional yet)',
            'vue' => 'Vue with Inertia (not functional yet)',
            'api-only' => 'API only',
        ]);

        $this->recipe('laravel-breeze/' . $stack);
    }

    public function description(): string
    {
        return 'Run through the Breeze setup process.';
    }

    public function name(): string
    {
        return 'Laravel Breeze';
    }
}
