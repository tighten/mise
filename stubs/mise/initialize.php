<?php

declare(strict_types=1);

use Tighten\Mise\Recipes\Recipe;

use function Laravel\Prompts\info;

return new class extends Recipe
{
    public function __invoke(): void
    {
        info('Initializing the project...');

        // Define initialization steps here
    }

    public function name(): string
    {
        return 'Initialize';
    }

    public function description(): string
    {
        return 'Use Mise and pre-defined steps to initialize the project, then delete Mise and the steps for a clean install.';
    }
};
