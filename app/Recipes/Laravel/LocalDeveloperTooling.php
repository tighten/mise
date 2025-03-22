<?php

declare(strict_types=1);

namespace App\Recipes\Laravel;

use App\Prompts\Target;
use App\Recipes\Recipe;
use App\Steps\Laravel\InstallHorizon;
use App\Steps\Laravel\InstallPulse;
use App\Steps\Laravel\InstallTelescope;

use function App\Prompts\apply;
use function App\Prompts\configure;

class LocalDeveloperTooling extends Recipe
{
    public function configure(): void
    {
        configure(Target::Recipe, $this->description());
        parent::configure();
        app(InstallTelescope::class)->configure();
    }

    public function __invoke(): void
    {
        apply(Target::Recipe, $this->description());
        $this->step(InstallPulse::class);
        $this->step(InstallTelescope::class);
        $this->step(InstallHorizon::class);
    }

    public function name(): string
    {
        return 'Local Developer Tooling';
    }

    public function vendor(): string
    {
        return 'Laravel';
    }
}
