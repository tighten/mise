<?php

declare(strict_types=1);

use App\Recipes\Recipe;

use function Laravel\Prompts\info;

return new class extends Recipe
{
    public function __invoke(): void
    {
        info('Initializing the project...');
    }

    public function name(): string
    {
        return 'Initialize';
    }

    public function description(): string
    {
        return 'Initialize the project';
    }
};
