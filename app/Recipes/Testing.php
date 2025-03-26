<?php

namespace App\Recipes;

use App\Recipes\Recipe;

class Testing extends Recipe
{
    public function __invoke(): void
    {
        $this->step('tighten/prettier');
    }

    public function description(): string
    {
        return 'For use in testing recipes.';
    }

    public function name(): string
    {
        return 'Testing';
    }
}
