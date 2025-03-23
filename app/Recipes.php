<?php

namespace App;

use ReflectionClass;

class Recipes
{
    public function all(): array
    {
        return collect(config('mise.recipes'))
            ->map(function (string $recipe) {
                if (class_exists($recipe)) {
                    $reflection = new ReflectionClass($recipe);
                    if (! $reflection->isSubclassOf('App\\Recipes\\Recipe')) {
                        return false;
                    }

                    return [$recipe => $reflection->newInstanceWithoutConstructor()->name()];
                }

                return false;
            })
            ->filter()
            ->flatMap(fn ($recipe) => $recipe)->toArray();
    }
}
