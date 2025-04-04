<?php

namespace Tests\Feature;

use App\Services\MiseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Config::set('app.mise-url', 'https://mise.example.com');
});

it('can fetch all recipes', function () {
    Http::fake([
        'https://mise.example.com/api/recipes' => [
            'data' => [
                ['id' => 1, 'name' => $name = 'Tighten SaaS'],
                ['id' => 2, 'name' => 'Nightwatch Play'],
            ],
        ],
    ]);

    $recipes = (new MiseService)->all();

    expect($recipes)->toBeInstanceOf(Collection::class)
        ->and($recipes)->toHaveCount(2)
        ->and($recipes[0]['name'])->toBe($name);
});

it('pull alls remote keys', function () {
    Http::fake([
        'https://mise.example.com/api/recipes' => [
            'data' => [
                ['id' => 1, 'name' => 'Tighten SaaS', 'key' => $keyOne = 'tighten-saas'],
                ['id' => 2, 'name' => 'Nightwatch Play', 'key' => $keyTwo = 'nightwatch-play'],
            ],
        ],
    ]);

    $keys = (new MiseService)->keys();

    expect($keys)->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->and($keys[0])->toBe($keyOne)
        ->and($keys[1])->toBe($keyTwo);
});

it('list alls remote recipes', function () {
    Http::fake([
        'https://mise.example.com/api/recipes' => [
            'data' => [
                ['id' => 1, 'name' => $nameOne = 'Tighten SaaS', 'key' => $keyOne = 'tighten-saas'],
                ['id' => 2, 'name' => $nameTwo = 'Nightwatch Play', 'key' => $keyTwo = 'nightwatch-play'],
            ],
        ],
    ]);

    $options = (new MiseService)->allForSelect();

    expect($options)->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->and($options[$keyOne])->toBe($nameOne)
        ->and($options[$keyTwo])->toBe($nameTwo);
});

it('can fetch a specific recipe', function () {
    $key = 'tighten-saas';

    Http::fake([
        "https://mise.example.com/api/recipes/{$key}" => Http::response([
            'data' => [
                'id' => 1,
                'name' => $name = 'Tighten SaaS',
            ],
        ]),
    ]);

    $recipe = (new MiseService)->findByKey($key);

    expect($recipe)->toBeInstanceOf(Collection::class)
        ->and($recipe['name'])->toBe($name);
});
