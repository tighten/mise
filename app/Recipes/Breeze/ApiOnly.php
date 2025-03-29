<?php

namespace App\Recipes\Breeze;

use App\Recipes\Recipe;

// @todo: Figure out if we want to actually offer subdirectory-loaded recipes or what
class ApiOnly extends Recipe
{
    public string $slug = 'laravel-breeze/api-only';

    public function __invoke(): void
    {
        // Require laravel/sanctum
        // Publish config/sanctum.php and config/cors.php if installing Sanctum doesn't
        // Run any other Sanctum install steps (in Laravel 11, that was modifying bootstrap/app.php and app/Providers/AppServiceProvider.php)
        // Publish/modify a bunch of controllers
        // Delete un-used frontend files
        /*
        - vite.config.js
        - package.json
        - resources/*
        */
        // Add resoures/views/.gitkeep file
        // Modify Auth tests
        // Publish expected rotues/web.php
        // Publish expected routes/auth.php
        // Publish expected routes/api.php
        // Publish Personal access tokens migration
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
