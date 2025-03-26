<?php

namespace App\Steps\Laravel;

use App\Steps\Step;

use function Laravel\Prompts\select;

class InstallTelescope extends Step
{
    public function __invoke(): void
    {
        $prod = select(label: 'Should Telescope be available in Production?', options: [
            true => 'Yes',
            false => 'No',
        ]);

        if ($prod) {
            $this->composer->require('laravel/telescope');
            $this->artisan->runCustom('telescope:install');
            $this->artisan->migrate();
            $this->git->addAndCommit('Install Laravel Telescope');
        } else {
            $this->composer->requireDev('laravel/telescope');
            $this->artisan->runCustom('telescope:install');
            $this->file->deleteLinesContaining('bootstrap/providers.php', 'TelescopeServiceProvider::class');
            $this->file->prependToMethod('app/Providers/AppServiceProvider.php', 'register', $this->manuallyRegisterTelescope());
            $this->file->addToJson('composer.json', 'extra.laravel.dont-discover', 'laravel/telescope');
            $this->artisan->migrate();
            $this->git->addAndCommit('Install Laravel Telescope -- local only');
        }
    }

    public function name(): string
    {
        return 'Laravel Telescope';
    }

    protected function manuallyRegisterTelescope(): string
    {
        return <<<EOT
        if (\$this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            \$this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            \$this->app->register(TelescopeServiceProvider::class);
        }
        EOT;
    }
}
