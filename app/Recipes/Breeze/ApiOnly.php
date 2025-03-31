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
        // @todo: Any of these that are Sanctum-specific, move to InstallSanctum

        // @todo Publish updated App/Providers/AppServiceProvider

        // @todo Publish new/updated versions of bunch of controllers
        // Auth/VerifyEmailController
        // Auth/AuthenticatedSessionController
        // Auth/EmailVerificationNotificationController
        // Auth/NewPasswordController
        // Auth/PasswordResetLinkController
        // Auth/RegisteredUserController

        // @todo Publish app/Http/Middleware/EnsureEmailIsVerified
        // @todo Publish app/Http/Requests/Auth/LoginRequest

        // Modify Auth tests (@todo)
        // Publish expected routes/web.php (@todo)
        // Publish expected routes/auth.php (@todo)

        $this->step('Delete un-used frontend files', function (Step $step) {
            $step->file->delete([
                'vite.config.js',
                'package.json',
                'resources/**/*',
            ]);

            $step->file->create('resources/views/.gitkeep');
        });
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
