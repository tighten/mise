<?php

namespace App\Recipes\Breeze;

use App\Recipes\Recipe;
use App\Steps\Laravel\InstallSanctum;
use App\Steps\Step;

class ApiOnly extends Recipe
{
    public string $key = 'laravel-breeze/api-only';

    // @todo: When done with this, make sure it all is still relevant in Laravel 12.
    public function __invoke(): void
    {
        $this->step(InstallSanctum::class);

        $this->step('ModifyApp Service Provider', function (Step $step) {
            $step->file->addUse('app/Providers/AppServiceProvider.php', 'Illuminate\Auth\Notifications\ResetPassword');
            $step->file->prependToMethod(
                'app/Providers/AppServiceProvider.php',
                'boot',
                "ResetPassword::createUrlUsing(function (object \$notifiable, string \$token) {\n .   return config('app.frontend_url').\"/password-reset/\$token?email=\{\$notifiable->getEmailForPasswordReset()}\";\n});"
            );
        });

            // @todo Publish updated App/Providers/AppServiceProvider

        // @todo Publish app/Http/Middleware/EnsureEmailIsVerified
        // @todo Publish app/Http/Requests/Auth/LoginRequest

        // Modify Auth tests (@todo)
        // Publish expected routes/web.php (@todo)
        // Publish expected routes/auth.php (@todo)

        $this->step('Modify Auth controllers', function (Step $step) {
            $step->file->replaceLines(
                'app/Http/Controllers/Auth/VerifyEmailController.php',
                'redirect()->intended(',
                "return redirect()->indended(config('app.frontend_url').'/dashboard?verified=1);"
            );

            // @todo Publish new/updated versions of bunch of controllers
            // Auth/AuthenticatedSessionController
            // Auth/EmailVerificationNotificationController
            // Auth/NewPasswordController
            // Auth/PasswordResetLinkController
            // Auth/RegisteredUserController
        });

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
