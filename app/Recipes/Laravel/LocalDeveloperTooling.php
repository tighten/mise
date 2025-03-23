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
        // @todo: This feels janky. Why do we have to call this vs. it being called automatically
        configure(Target::Recipe, $this->description());

        parent::configure();
        // @todo: This feels janky. Need to dig into why we need a separate configure method.
        app(InstallTelescope::class)->configure();
    }

    public function __invoke(): void
    {
        // @todo: Same here. this feels janky.
        apply(Target::Recipe, $this->description());

        $this->step(InstallPulse::class);
        $this->step(InstallTelescope::class);
        $this->step(InstallHorizon::class);
    }

    public function name(): string
    {
        return 'Local Developer Tooling';
    }
}
