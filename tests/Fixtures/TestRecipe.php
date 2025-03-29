<?php

namespace Tests\Fixtures;

use App\Recipes\Recipe;

class TestRecipe extends Recipe
{
    public static $slug = 'test-recipe';

    public function __invoke(): void
    {
        $this->step(TestStep::class);
        $this->step(TestStep::class, 'Greetings from the Test Step with Parameters');
    }

    public function name(): string
    {
        return 'Test Recipe';
    }

    public function vendorPackage(): string
    {
        return 'tighten/test-recipe';
    }

    public function description(): string
    {
        return 'A test fixture';
    }
}
