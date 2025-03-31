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

        $this->step('Modify App Service Provider', function (Step $step) {
            $step->file->addImport('app/Providers/AppServiceProvider.php', 'Illuminate\Auth\Notifications\ResetPassword');
            $step->file->prependToMethod(
                'app/Providers/AppServiceProvider.php',
                'boot',
                "ResetPassword::createUrlUsing(function (object \$notifiable, string \$token) {\n .   return config('app.frontend_url').\"/password-reset/\$token?email=\{\$notifiable->getEmailForPasswordReset()}\";\n});"
            );
        });

        $this->step('Publish middleware, requests, and routes', function (Step $step) {
            $step->file->stub('breeze/api-only/EnsureEmailIsVerified.php', 'app/Http/Middleware/EnsureEmailIsVerified.php');
            $step->file->stub('breeze/api-only/LoginRequest.php', 'app/Http/Requests/Auth/LoginRequest.php');
            $step->file->stub('breeze/api-only/routes/web.php', 'routes/web.php');
            $step->file->stub('breeze/api-only/routes/auth.php', 'routes/auth.php');
        });

        $this->step('Modify Auth controllers and tests', function (Step $step) {
            $step->file->replaceLines(
                'app/Http/Controllers/Auth/VerifyEmailController.php',
                'redirect()->intended(',
                "return redirect()->indended(config('app.frontend_url').'/dashboard?verified=1);"
            );

            $step->file->stub('breeze/api-only/controllers/AuthenticatedSessionController.php', 'app/Http/Controllers/Auth/AuthenticatedSessionController.php');
            $step->file->stub('breeze/api-only/controllers/EmailVerificationNotificationController.php', 'app/Http/Controllers/Auth/EmailVerificationNotificationController.php');
            $step->file->stub('breeze/api-only/controllers/NewPasswordController.php', 'app/Http/Controllers/Auth/NewPasswordController.php');
            $step->file->stub('breeze/api-only/controllers/PasswordResetLinkController.php', 'app/Http/Controllers/Auth/PasswordResetLinkController.php');
            $step->file->stub('breeze/api-only/controllers/RegisteredUserController.php', 'app/Http/Controllers/Auth/RegisteredUserController.php');

            $step->file->delete('tests/Feature/Auth/PasswordConfirmationTest.php');

            $step->file->stub('breeze/api-only/tests/AuthenticationTest.php', 'tests/Feature/Auth/AuthenticationTest.php');
            $step->file->stub('breeze/api-only/tests/EmailVerificationTest.php', 'tests/Feature/Auth/EmailVerificationTest.php');
            $step->file->stub('breeze/api-only/tests/PasswordResetTest.php', 'tests/Feature/Auth/PasswordResetTest.php');
            $step->file->stub('breeze/api-only/tests/RegistrationTest.php', 'tests/Feature/Auth/RegistrationTest.php');
            $step->file->stub('breeze/api-only/tests/ExampleTest.php', 'tests/Unit/ExampleTest.php');
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
