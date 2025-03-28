<?php

namespace App;

use App\Recipes\Recipe;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Recipes
{
    public function all(): Collection
    {
        return collect(File::files(app_path('Recipes')))
            ->map(fn ($file) => "App\\Recipes\\" . pathinfo($file, PATHINFO_FILENAME))
            ->filter(fn ($class) => class_exists($class) && is_subclass_of($class, Recipe::class))
            ->mapWithKeys(fn ($class) => [(new $class)->slug() => $class]);
    }

    public function allForSelect(): array
    {
        return $this->all()
            ->flatMap(function (string $recipe) {
                return [$recipe => (new $recipe)->name()];
            })
            ->toArray();
    }

    public function keys(): array
    {
        return $this->all()->keys()->toArray();
    }
}
