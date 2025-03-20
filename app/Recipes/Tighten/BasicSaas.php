<?php

namespace App\Recipes\Tighten;

use App\Recipes\Recipe;

class BasicSaas extends Recipe
{
    public function __invoke(): void
    {
        $this->step('duster/install');
        $this->step('duster/ci', someParameterHereOrWhatever: true);

        if ($this->confirm(label: 'Do you want to install our frontend tooling?')) {
            $this->step('tighten/prettier');
        }
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
