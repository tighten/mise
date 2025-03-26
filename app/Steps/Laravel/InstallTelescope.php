<?php

declare(strict_types=1);

namespace App\Steps\Laravel;

use App\Steps\Step;
use Illuminate\Support\Facades\Context;

use function Laravel\Prompts\select;

class InstallTelescope extends Step
{
    public function __invoke(): void
    {
        $this->shouldInstallLocally()
            ? $this->localOnlyInstall()
            : $this->composer->require('laravel/telescope');

        $this->artisan->runCustom('telescope:install');
        $this->artisan->migrate();
        $this->git->addAndCommit('Install Laravel Telescope');
    }

    public function name(): string
    {
        return 'Laravel Telescope';
    }

    private function shouldInstallLocally(): bool
    {
        return ! select(label: 'Should Telescope be available in Production?', options: [
            true => 'Yes',
            false => 'No',
        ]);
    }

    private function localOnlyInstall(): void
    {
        $this->composer->requireDev('laravel/telescope');
        Context::push('register_local_only_providers', '
            if (class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
                $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
                $this->app->register(TelescopeServiceProvider::class);
            }',
        );
    }
}
