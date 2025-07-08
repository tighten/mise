<?php

namespace Tests\Feature;

use App\Services\LocalRecipesService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

beforeEach(function () {
    Storage::fake('local-recipes');
    Storage::fake('local');
});

it('returns empty collection when no lock file exists', function () {
    $recipes = (new LocalRecipesService)->all();

    expect($recipes)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(0);
});

it('returns recipes from lock file', function () {
    $package = [
        'key' => 'test-recipe',
        'name' => 'Test Recipe',
        'version' => '1.0.0',
        'integrity' => 'test-hash',
        'source' => ['url' => 'https://example.com/recipe.zip'],
    ];

    Storage::disk('local-recipes')->put('mise-lock.json', json_encode([
        'recipes' => [
            $package,
        ],
    ]));

    $recipes = (new LocalRecipesService)->all();

    expect($recipes)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->and($recipes->first())->toBe($package);
});

it('can find recipe by key', function () {
    Storage::disk('local-recipes')->put('mise-lock.json', json_encode([
        'recipes' => [
            [
                'key' => 'recipe-one',
                'name' => 'Recipe One',
                'version' => '1.0.0',
                'integrity' => 'hash-one',
                'source' => ['url' => 'https://example.com/one.zip'],
            ],
            [
                'key' => 'recipe-two',
                'name' => 'Recipe Two',
                'version' => '2.0.0',
                'integrity' => 'hash-two',
                'source' => ['url' => 'https://example.com/two.zip'],
            ],
        ],
    ]));

    $recipe = (new LocalRecipesService)->findByKey('recipe-two');

    expect($recipe)->not->toBeNull()
        ->and($recipe['key'])->toBe('recipe-two')
        ->and($recipe['name'])->toBe('Recipe Two');
});

it('returns null when recipe key not found', function () {
    $recipe = (new LocalRecipesService)->findByKey('non-existent');

    expect($recipe)->toBeNull();
});

it('checks if recipe exists by key', function () {
    Storage::disk('local-recipes')->put('mise-lock.json', json_encode([
        'recipes' => [
            [
                'key' => 'existing-recipe',
                'name' => 'Existing Recipe',
                'version' => '1.0.0',
                'integrity' => 'test-hash',
                'source' => ['url' => 'https://example.com/recipe.zip'],
            ],
        ],
    ]));

    $service = new LocalRecipesService;

    expect($service->exists('existing-recipe'))->toBeTrue()
        ->and($service->exists('non-existent'))->toBeFalse();
});

it('downloads and validates integrity during install', function () {
    $zip = new ZipArchive;
    $zip->open($tempZipPath = tempnam(sys_get_temp_dir(), 'test_recipe') . '.zip', ZipArchive::CREATE);
    $zip->addFromString('Recipe.php', '<?php return ["name" => "Test Recipe"];');
    $zip->close();

    $zipContent = file_get_contents($tempZipPath);
    unlink($tempZipPath);

    $package = [
        'key' => 'test-recipe',
        'name' => 'Test Recipe',
        'version' => '1.0.0',
        'integrity' => $expectedIntegrity = hash('sha512', $zipContent),
        'url' => $url = 'https://mise.tighten.com/recipe.zip',
        'namespace' => 'TestRecipe',
    ];

    Http::fake([
        $url => Http::response($zipContent, 200),
    ]);

    (new LocalRecipesService)->install($package);

    Http::assertSent(fn ($request) => $request->url() === $url);

    expect(Storage::disk('local-recipes')->exists('mise-lock.json'))->toBeTrue();
    expect(Storage::disk('local-recipes')->exists('TestRecipe/Recipe.php'))->toBeTrue();

    $lockContent = json_decode(Storage::disk('local-recipes')->get('mise-lock.json'), true);
    expect($lockContent['recipes'])
        ->toHaveCount(1)
        ->toBe([
            [
                'key' => 'test-recipe',
                'name' => 'Test Recipe',
                'version' => '1.0.0',
                'integrity' => $expectedIntegrity,
                'source' => ['url' => $url],
            ],
        ]);
});

it('throws exception when integrity verification fails', function () {
    $zip = new ZipArchive;
    $zip->open($tempZipPath = tempnam(sys_get_temp_dir(), 'test_recipe') . '.zip', ZipArchive::CREATE);
    $zip->addFromString('Recipe.php', '<?php return ["name" => "Test Recipe"];');
    $zip->close();

    $zipContent = file_get_contents($tempZipPath);
    unlink($tempZipPath);

    $package = [
        'key' => 'test-recipe',
        'name' => 'Test Recipe',
        'version' => '1.0.0',
        'integrity' => 'invalid-hash-that-wont-match',
        'url' => $url = 'https://mise.tighten.com/recipe.zip',
        'namespace' => 'TestRecipe',
    ];

    Http::fake([
        $url => Http::response($zipContent, 200),
    ]);

    expect(fn () => (new LocalRecipesService)->install($package))
        ->toThrow(Exception::class, 'Integrity verification failed. Downloaded file integrity check failed.');
});
