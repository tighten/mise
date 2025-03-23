<?php

declare(strict_types=1);

namespace App\Recipes\Laravel;

use App\Recipes\Recipe;
use App\Steps\Laravel\InstallHorizon;
use App\Steps\Laravel\InstallPulse;
use App\Steps\Laravel\InstallTelescope;

class LocalDeveloperTooling extends Recipe
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
        return 'Local Developer Tooling';
    }

    public function vendorPackage(): string
    {
        return 'laravel/local-developer-tooling';
    }
}
