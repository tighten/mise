<?php

namespace Tighten\Mise\Steps\Laravel;

use Tighten\Mise\Steps\Step;

class InstallPulse extends Step
{
    public function __invoke(): void
    {
        $this->composer->require('laravel/pulse');
        $this->artisan->vendorPublish('Laravel\Pulse\PulseServiceProvider');
        $this->artisan->migrate();
        $this->git->addAndCommit('Install Laravel Pulse');
    }

    public function name(): string
    {
        return 'Laravel Pulse';
    }
}
