<?php

declare(strict_types=1);

namespace App\Steps\Laravel;

use App\Steps\Step;

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
        return 'Installing Laravel Pulse';
    }
}
