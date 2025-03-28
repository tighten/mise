<?php

namespace App;

use App\Recipes\Recipe;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class Recipes
{
    public function all(): Collection
    {
        $customRecipesDir = $_SERVER['HOME'] . '/.mise/Recipes';

        if (is_dir($customRecipesDir)) {
            $this->loadFilesInPath($customRecipesDir);
        }

        return $this->allInPath(app_path('Recipes'))->when(is_dir($customRecipesDir), function (Collection $recipes) use ($customRecipesDir) {
            return $recipes->merge($this->allInPath($customRecipesDir));
        });
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

    protected function allInPath(string $path): Collection
    {
        return collect(File::files($path))
            ->map(fn ($file) => 'App\\Recipes\\' . pathinfo($file, PATHINFO_FILENAME))
            ->filter(fn ($class) => class_exists($class) && is_subclass_of($class, Recipe::class))
            ->mapWithKeys(fn ($class) => [(new $class)->slug => $class]);
    }

    protected function loadFilesInPath(string $path): void
    {
        foreach (glob($path . '/*.php') as $file) {
            require_once $file;
        }
    }
}
