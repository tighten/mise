<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Recipes\Recipe;

class TestRecipe extends Recipe
{
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
