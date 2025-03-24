<?php

declare(strict_types=1);

use App\Steps\Laravel\InstallHorizon;
use Illuminate\Support\Facades\Process;
use Laravel\Prompts\Prompt;
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

it('it runs steps', function () {
    Process::fake();
    Prompt::fake();

    (new TestRecipe)();

    Process::assertRan("echo 'Hello World'");
    Process::assertRan("echo 'Greetings from the Test Step with Parameters'");
    Prompt::assertOutputContains('Installing: Test Step..');
});
