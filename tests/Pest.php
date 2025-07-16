<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Illuminate\Support\Facades\Process;
use Tighten\Mise\Tools\Artisan;
use Tighten\Mise\Tools\Composer;
use Tighten\Mise\Tools\ConsoleCommand;
use Tighten\Mise\Tools\File;
use Tighten\Mise\Tools\Git;
use Tighten\Mise\Tools\Npm;

uses(Tests\TestCase::class)->beforeEach(function () {
    Process::preventStrayProcesses();
})->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('stepProcessRan', function (string $step, array $commands) {

    Process::fake();

    app($step, [
        new Artisan,
        new Composer,
        new Git,
        new ConsoleCommand,
        new Npm,
        new File,
    ])();

    foreach ($commands as $command) {
        Process::assertRan($command);
    }
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

pest()->presets()->custom('zero', function (array $userNamespaces) {
    return [
        expect($userNamespaces)->toBeArray(),
        expect('Tighten\Mise\Traits')->toBeTraits(),

        expect('Tighten\Mise\Concerns')
            ->toBeTraits(),

        expect('Tighten\Mise\Features')
            ->toBeClasses()
            ->ignoring('Tighten\Mise\Features\Concerns'),

        expect('Tighten\Mise\Features')
            ->toHaveMethod('resolve'),

        expect('Tighten\Mise\Exceptions')
            ->classes()
            ->toImplement('Throwable')
            ->ignoring('Tighten\Mise\Exceptions\Handler'),

        expect('Tighten\Mise')
            ->classes()
            ->not->toImplement(Throwable::class)
            ->ignoring('Tighten\Mise\Exceptions')
            ->ignoring('Tighten\Mise\Tools\PhpParser\Exceptions'),

        expect('Tighten\Mise\Commands')
            ->classes()
            ->toHaveSuffix('Command'),

        expect(['Tighten\Mise\Commands', 'Tighten\Mise\DevelopmentCommands'])
            ->classes()
            ->toExtend(\LaravelZero\Framework\Commands\Command::class),

        expect(['Tighten\Mise\Commands', 'Tighten\Mise\DevelopmentCommands'])
            ->classes()
            ->toHaveMethod('handle'),

        expect('Tighten\Mise')
            ->classes()
            ->not->toExtend('Illuminate\Console\Command')
            ->ignoring(['Tighten\Mise\Commands', 'Tighten\Mise\DevelopmentCommands']),

        expect('Tighten\Mise\Listeners')
            ->toHaveMethod('handle'),

        expect('Tighten\Mise\Notifications')
            ->toExtend('Illuminate\Notifications\Notification'),

        expect('Tighten\Mise\Providers')
            ->toHaveSuffix('ServiceProvider'),

        expect('Tighten\Mise\Providers')
            ->toExtend('Illuminate\Support\ServiceProvider'),

        expect('Tighten\Mise\Providers')
            ->not->toBeUsed(),

        expect('Tighten\Mise')
            ->classes()
            ->not->toExtend('Illuminate\Support\ServiceProvider')
            ->ignoring('Tighten\Mise\Providers'),

        expect('Tighten\Mise')
            ->classes()
            ->not->toHaveSuffix('ServiceProvider')
            ->ignoring('Tighten\Mise\Providers'),

        expect([
            'dd',
            'ddd',
            'dump',
            'env',
            'exit',
            'ray',
        ])->not->toBeUsed(),
    ];
});
