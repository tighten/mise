<?php

use App\Recipes;
use App\Recipes\Recipe;
use Illuminate\Support\Collection;

it('returns all valid recipe classes as collection', function () {
    $recipes = (new Recipes)->allKeysAndClasses();

    expect($recipes)->toBeInstanceOf(Collection::class);

    foreach ($recipes as $slug => $class) {
        expect($slug)->toBeString();
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

    expect($keys)->toBeArray();

    foreach ($keys as $key) {
        expect($key)->toBeString();
    }
});

// @todo: Add tests for findByKey, all, allKeysAndClasses, allForSelect, others?
