<?php

declare(strict_types=1);

use App\Recipes\Recipe;

return new class extends Recipe
{
    public function name(): string
    {
        return 'Initialize';
    }

    public function description(): string
    {
        return 'Initialize the project';
    }

    public function __invoke(): void
    {
        \Laravel\Prompts\info('Initializing the project...');
    }
};
