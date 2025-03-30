<?php

namespace App\Recipes\Breeze;

use App\Recipes\Recipe;
use App\Steps\Laravel\InstallSanctum;
use App\Steps\Step;

// @todo: Figure out if we want to actually offer subdirectory-loaded recipes or what
class ApiOnly extends Recipe
{
    public string $slug = 'laravel-breeze/api-only';

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
                // @todo: Figure out how to use glob patterns here; currently this just
                // pushes the whole way down to unlink(), which doesn't resolve glob patterns
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
