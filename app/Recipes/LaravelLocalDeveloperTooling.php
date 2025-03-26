<?php

namespace App\Recipes;

use App\Steps\Laravel\InstallHorizon;
use App\Steps\Laravel\InstallPulse;
use App\Steps\Laravel\InstallTelescope;

class LaravelLocalDeveloperTooling extends Recipe
{
    public function __invoke(): void
    {
        $this->step(InstallPulse::class);
        $this->step(InstallTelescope::class);
        $this->step(InstallHorizon::class);
    }

    public function description(): string
    {
        return 'Install Laravel Packages: Pulse, Telescope, and Horizon';
    }

    public function name(): string
    {
        return 'Laravel Local Developer Tooling';
    }
}
