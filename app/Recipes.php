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

    public function allKeysAndClasses(): Collection
    {
        return $this->all()->mapWithKeys(fn ($recipe) => [$recipe->slug => $recipe::class]);
    }

    public function allForSelect(): array
    {
        return $this->all()
            ->flatMap(function (Recipe $recipe) {
                return [$recipe::class => $recipe->name()];
            })
            ->toArray();
    }

    public function findByKey(string $key): Recipe
    {
        return $this->all()->firstOrFail(fn ($instance) => $instance->slug === $key);
    }

    public function keys(): Collection
    {
        return $this->allKeysAndClasses()->keys();
    }

    protected function allInPath(string $path): Collection
    {
        return collect(File::allFiles($path))
            ->map(fn ($file) => 'App\\Recipes\\' . str_replace('/', '\\',
                trim(str_replace([$path, '.php'], '', $file->getPathname()), '/')
            ))
            ->filter(fn ($class) => class_exists($class) && is_subclass_of($class, Recipe::class))
            ->map(fn ($class) => (new $class));
    }

    protected function loadFilesInPath(string $path): void
    {
        foreach (glob($path . '/*.php') as $file) {
            require_once $file;
        }
    }
}
