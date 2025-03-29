<?php

namespace App\Steps\Laravel;

use App\Steps\Step;

class InstallSanctum extends Step
{
    public function __invoke(): void
    {
        $this->artisan->runCustom('install:api');
        $this->git->addAndCommit('Install Laravel Sanctum');
    }

    public function name(): string
    {
        return 'Laravel Sanctum';
    }
}
