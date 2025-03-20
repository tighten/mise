<?php

declare(strict_types=1);

namespace App\Steps\Laravel;

use App\Steps\Step;

class InstallTelescope extends Step
{
    public function __invoke(): void
    {
        $this->composer->requireDev('laravel/telescope');
        $this->artisan->runCustom('telescope:install');
        $this->artisan->migrate();
        $this->git->addAndCommit('Install Laravel Telescope');
    }

    public function name(): string
    {
        return 'Installing Laravel Telescope';
    }
}
