<?php

namespace App\Recipes\Breeze;

use App\Recipes\Recipe;
use App\Steps\Database\Migrate;
use App\Steps\Files\CreateFile;
use App\Steps\Files\DeleteFiles;
use App\Steps\Laravel\InstallSanctum;
use App\Steps\Files\PublishStubs;
use App\Steps\Git\AddAndCommit;
use App\Steps\Step;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

class ApiOnly extends Recipe
{
    public string $key = 'laravel-breeze/api-only';

    // @todo: When done with this, make sure it all is still relevant in Laravel 12.
    public function __invoke(): void
    {
        $testingFramework = select('Which testing framework do you want to use?', [
            'pest' => 'Pest',
            'phpunit' => 'PHPUnit',
        ]);

        $this->step(InstallSanctum::class);

        $this->step(PublishStubs::class, 'breeze/api-only/' . $testingFramework);
        $this->step(PublishStubs::class, 'breeze/api-only/shared');
        $this->step(DeleteFiles::class, [
            'vite.config.js',
            'package.json',
            'resources/**/*',
            'package-lock.json',
            'node_modules/**/*',
        ]);
        $this->step(CreateFile::class, 'resources/views/.gitkeep');
        $this->step('Update .env', function (Step $step) {
            $step->file->appendAfterLine('.env', 'APP_URL=', 'FRONTEND_URL=http://localhost:3000');
            $step->file->appendAfterLine('.env.example', 'APP_URL=', 'FRONTEND_URL=http://localhost:3000');
        });

        $this->step('Modify bootstrap.php', function (Step $step) {
            $step->file->appendAfterLine(
                'bootstrap/app.php',
                '->withMiddleware(',
                "    \$middleware->api(prepend: [\n        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,\n    ]);\n\n    \$middleware->alias([\n        'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,\n    ]);"
            );
        });

        $this->step('Modify App Service Provider', function (Step $step) {
            $step->file->addImports('app/Providers/AppServiceProvider.php', 'Illuminate\Auth\Notifications\ResetPassword');
            $step->file->prependToMethod(
                'app/Providers/AppServiceProvider.php',
                'boot',
                "ResetPassword::createUrlUsing(function (object \$notifiable, string \$token) {\n    return config('app.frontend_url').\"/password-reset/\$token?email=\{\$notifiable->getEmailForPasswordReset()}\";\n});"
            );
            $step->formatter->fix('app/Providers/AppServiceProvider.php', ['single_import_per_statement', 'ordered_imports', 'single_line_after_imports']);
        });

        $this->step('Modify existing Auth controllers', function (Step $step) {
            $step->file->replaceLines(
                'app/Http/Controllers/Auth/VerifyEmailController.php',
                'redirect()->intended(',
                "return redirect()->intended(config('app.frontend_url').'/dashboard?verified=1);"
            );

            $step->file->delete('tests/Feature/Auth/PasswordConfirmationTest.php');
        });

        $this->step(AddAndCommit::class, 'Configure for API-only');

        if (confirm('Would you like to run database migrations?')) {
            $this->step(Migrate::class);
        }
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
