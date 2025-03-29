<?php

declare(strict_types=1);

namespace App\Recipes;

use App\Steps\Laravel\InstallBreeze;

class LaravelBreezeStarterKit extends Recipe
{
    public string $slug = 'laravel-breeze-starter-kit';

    public function __invoke(): void
    {
        $this->step(InstallBreeze::class);
    }

    public function name(): string
    {
        return 'Install Laravel Breeze';
    }

    public function description(): string
    {
        return 'Install the Laravel Breeze starter kit.';
    }
}
