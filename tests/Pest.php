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

use App\Tools\Artisan;
use App\Tools\Composer;
use App\Tools\ConsoleCommand;
use App\Tools\Git;
use Illuminate\Support\Facades\Process;

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
        expect('App\Traits')->toBeTraits(),

        expect('App\Concerns')
            ->toBeTraits(),

        expect('App\Features')
            ->toBeClasses()
            ->ignoring('App\Features\Concerns'),

        expect('App\Features')
            ->toHaveMethod('resolve'),

        expect('App\Exceptions')
            ->classes()
            ->toImplement('Throwable')
            ->ignoring('App\Exceptions\Handler'),

        expect('App')
            ->classes()
            ->not->toImplement(Throwable::class)
            ->ignoring('App\Exceptions'),

        expect('App\Commands')
            ->classes()
            ->toHaveSuffix('Command'),

        expect(['App\Commands', 'App\DevelopmentCommands'])
            ->classes()
            ->toExtend(\LaravelZero\Framework\Commands\Command::class),

        expect(['App\Commands', 'App\DevelopmentCommands'])
            ->classes()
            ->toHaveMethod('handle'),

        expect('App')
            ->classes()
            ->not->toExtend('Illuminate\Console\Command')
            ->ignoring(['App\Commands', 'App\DevelopmentCommands']),

        expect('App\Listeners')
            ->toHaveMethod('handle'),

        expect('App\Notifications')
            ->toExtend('Illuminate\Notifications\Notification'),

        expect('App\Providers')
            ->toHaveSuffix('ServiceProvider'),

        expect('App\Providers')
            ->toExtend('Illuminate\Support\ServiceProvider'),

        expect('App\Providers')
            ->not->toBeUsed(),

        expect('App')
            ->classes()
            ->not->toExtend('Illuminate\Support\ServiceProvider')
            ->ignoring('App\Providers'),

        expect('App')
            ->classes()
            ->not->toHaveSuffix('ServiceProvider')
            ->ignoring(['App\Providers', 'App\Steps\Mise\ConfigureAppServiceProvider']),

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
