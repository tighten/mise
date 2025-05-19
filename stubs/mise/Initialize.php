<?php

declare(strict_types=1);

use App\Recipes\Recipe;

class Initialize extends Recipe
{
    public function name(): string
    {
        return 'Initialize';
    }

    public function description(): string
    {
        return 'Initialize the project';
    }

    public function __invoke()
    {
        // TODO: add your custom set-up and configuration code here.
    }
}
