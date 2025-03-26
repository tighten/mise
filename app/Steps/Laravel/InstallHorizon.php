<?php

namespace App\Steps\Laravel;

use App\Steps\Step;

class InstallHorizon extends Step
{
    public function __invoke(): void
    {
        $this->composer->require('laravel/horizon');
        $this->artisan->runCustom('horizon:install');
        $this->artisan->migrate();
        $this->git->addAndCommit('Install Laravel Horizon');
    }

    public function name(): string
    {
        return 'Laravel Horizon';
    }
}
