<?php

namespace App\Recipes;

class LaravelBreeze extends Recipe
{
    public string $slug = 'laravel-breeze';

    public function __invoke(): void
    {
        // Which breeze stack would you like to install?
        // 1. Blade with Alpine
        // 2. Livewire (Volt Class API) with Alpine
        // 3. Livewire (Volt Functional API) with Alpine
        // 4. React with Inertia
        // 5. Vue with Inertia
        // 6. API only

        // Which testing framework would you like to install?
        // 1. PHPUnit
        // 2. Pest

        // Would you like to run database migrations?
        // 1. Yes
        // 2. No
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
