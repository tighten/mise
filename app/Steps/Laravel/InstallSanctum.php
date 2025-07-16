<?php

namespace Tighten\Mise\Steps\Laravel;

use Tighten\Mise\Steps\Step;

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
