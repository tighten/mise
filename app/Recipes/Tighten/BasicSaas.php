<?php

namespace App\Recipes\Tighten;

use App\Prompts\Target;
use App\Recipes\Recipe;
use App\Steps\Duster\Install;
use App\Steps\Duster\Install as DusterInstall;

use function App\Prompts\apply;

class BasicSaas extends Recipe
{
    public function __invoke(): void
    {
        apply(Target::Recipe, $this->description());
        $this->step(DusterInstall::class);

        // $this->step('duster/ci', someParameterHereOrWhatever: true);
        // if ($this->confirm(label: 'Do you want to install our frontend tooling?')) {
        //     $this->step('tighten/prettier');
        // }
    }

    public function name(): string
    {
        return 'Basic Saas';
    }

    public function vendor(): string
    {
        return 'Tighten Co.';
    }
}
