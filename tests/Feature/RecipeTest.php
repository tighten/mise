<?php

declare(strict_types=1);

use App\Steps\Laravel\InstallHorizon;
use Tests\Fixtures\TestRecipe;

it('resolves step classes', function () {
    $testRecipe = new TestRecipe;
    collect([
        InstallHorizon::class,
        'laravel/install-horizon',
        'laravel/Install_Horizon',
        'laravel/Install Horizon',
        'laravel/InstallHorizon',
        'Laravel/InstallHorizon',
    ])->each(
        fn ($recipe) => expect($testRecipe->resolveStep($recipe))->toEqual(InstallHorizon::class)
    );
});
