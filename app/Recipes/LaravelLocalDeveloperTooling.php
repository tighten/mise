<?php

namespace Tighten\Mise\Recipes;

use Tighten\Mise\Steps\Laravel\InstallHorizon;
use Tighten\Mise\Steps\Laravel\InstallPulse;
use Tighten\Mise\Steps\Laravel\InstallTelescope;

class LaravelLocalDeveloperTooling extends Recipe
{
    public string $key = 'laravel-local-developer-tooling';

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
