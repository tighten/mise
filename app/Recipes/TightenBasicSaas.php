<?php

namespace Tighten\Mise\Recipes;

use function Laravel\Prompts\confirm;

class TightenBasicSaas extends Recipe
{
    public string $key = 'tighten-basic-saas';

    public function __invoke(): void
    {
        $this->step('duster/install');

        if (confirm(label: 'Do you want to add Duster\'s GitHub Actions workflow?')) {
            $this->step('duster/ci');
        }

        if (confirm(label: 'Do you want to install our Prettier config?')) {
            $this->step('tighten/prettier');
        }
    }

    public function description(): string
    {
        return 'Install Tighten\'s default stack for a SaaS application.';
    }

    public function name(): string
    {
        return "Tighten's Basic SaaS Starter";
    }
}
