<?php

namespace App\Recipes\Breeze;

use App\Recipes\Recipe;
use App\Steps\Laravel\InstallSanctum;
use App\Steps\Step;

// @todo: Figure out if we want to actually offer subdirectory-loaded recipes or what
// @todo: This isn't really intended to be a recipe, but just a collection of steps for other recipes to call. Do we expose this to the list/CLI?
class ApiOnly extends Recipe
{
    public string $key = 'laravel-breeze/api-only';

    public function __invoke(): void
    {
        $this->step(InstallSanctum::class);
        // @todo Run any other Sanctum install steps (in Laravel 11, included modifying app/Providers/AppServiceProvider.php)
        // @todo Publish/modify a bunch of controllers

        $this->step('Delete un-used frontend files', function (Step $step) {
            // Delete un-used frontend files
            $step->file->delete([
                'vite.config.js',
                'package.json',
                'resources/**/*',
            ]);

            $step->file->create('resources/views/.gitkeep');
        });

        // Modify Auth tests (@todo)
        // Publish expected routes/web.php (@todo)
        // Publish expected routes/auth.php (@todo)
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
