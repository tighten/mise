<?php

namespace Tighten\Mise\Recipes;

class Testing extends Recipe
{
    public string $key = 'testing';

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
