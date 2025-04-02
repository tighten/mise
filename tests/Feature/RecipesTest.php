<?php

use App\Recipes;
use App\Recipes\Recipe;
use Illuminate\Support\Collection;
use Illuminate\Support\ItemNotFoundException;

it('returns all recipes as collection of Recipe instances', function () {
    $recipes = (new Recipes)->all();

    expect($recipes)->toBeInstanceOf(Collection::class);

    foreach ($recipes as $recipe) {
        expect($recipe)->toBeInstanceOf(Recipe::class);
    }
});

it('returns all valid recipe classes as collection', function () {
    $recipes = (new Recipes)->allKeysAndClasses();

    expect($recipes)->toBeInstanceOf(Collection::class);

    foreach ($recipes as $key => $class) {
        expect($key)->toBeString();
        expect($class)->toBeString();

        expect(new $class)->toBeInstanceOf(Recipe::class);
    }
});

it('returns all valid recipes for usage in prompts', function () {
    $recipes = (new Recipes)->allForSelect();

    expect($recipes)->toBeArray();

    foreach ($recipes as $class => $name) {
        expect($class)->toBeString();
        expect($name)->toBeString();

        expect(new $class)->toBeInstanceOf(Recipe::class);
    }
});

it('returns all valid recipe keys', function () {
    $keys = (new Recipes)->keys();

    expect($keys)->toBeInstanceOf(Collection::class);

    foreach ($keys as $key) {
        expect($key)->toBeString();
    }
});

it('can find a recipe by its key', function () {
    $recipes = new Recipes;

    // Get the first recipe key for testing
    $firstKey = $recipes->keys()->first();

    expect($recipes->findByKey($firstKey))->toBeInstanceOf(Recipe::class);
});

it('throws exception when finding invalid recipe key', function () {
    expect(fn () => (new Recipes)->findByKey('invalid-key'))
        ->toThrow(ItemNotFoundException::class);
});


