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

        $this->step('Publish stub files', function (Step $step) {
            $step->file->stubAll('breeze/api-only');
        });

        $this->step('Modify App Service Provider', function (Step $step) {
            $step->file->addImport('app/Providers/AppServiceProvider.php', 'Illuminate\Auth\Notifications\ResetPassword');
            $step->file->prependToMethod(
                'app/Providers/AppServiceProvider.php',
                'boot',
                "ResetPassword::createUrlUsing(function (object \$notifiable, string \$token) {\n .   return config('app.frontend_url').\"/password-reset/\$token?email=\{\$notifiable->getEmailForPasswordReset()}\";\n});"
            );
        });

        $this->step('Modify existing Auth controllers', function (Step $step) {
            $step->file->replaceLines(
                'app/Http/Controllers/Auth/VerifyEmailController.php',
                'redirect()->intended(',
                "return redirect()->indended(config('app.frontend_url').'/dashboard?verified=1);"
            );

            $step->file->delete('tests/Feature/Auth/PasswordConfirmationTest.php');
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
