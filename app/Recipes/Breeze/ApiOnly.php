<?php

namespace App\Recipes\Breeze;

use App\Recipes\Recipe;
use App\Steps\Laravel\InstallSanctum;
// @todo: Figure out if we want to actually offer subdirectory-loaded recipes or what
class ApiOnly extends Recipe
{
    public string $slug = 'laravel-breeze/api-only';

    public function __invoke(): void
    {
        $this->step(InstallSanctum::class);
        // Run any other Sanctum install steps (in Laravel 11, included modifying app/Providers/AppServiceProvider.php)
        // Publish/modify a bunch of controllers

        // @todo: Update the step command to allow this syntax
        $this->step('Delete un-used frontend files', function () {
            // Delete un-used frontend files
            /*
            - vite.config.js
            - package.json
            - resources/*
            */

            // Add resources/views/.gitkeep file
        });

        // Modify Auth tests
        // Publish expected rotues/web.php
        // Publish expected routes/auth.php
    }

    public function description(): string
    {
        return 'Install Breeze with API-only stack.';
    }

    public function name(): string
    {
        return 'Laravel Breeze: API-only';
    }
}
