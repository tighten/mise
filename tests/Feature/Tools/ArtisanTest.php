<?php

use App\Tools\Artisan;
use Illuminate\Support\Facades\Process;

test('artisan->vendorPublish(...)', function () {
    Process::fake();
    $artisan = new Artisan;

    $artisan->vendorPublish('Laravel\Pulse\PulseServiceProvider');

    Process::assertRan("php artisan vendor:publish --provider='Laravel\Pulse\PulseServiceProvider'");
});

test('artisan->migrate()', function () {
    Process::fake();
    $artisan = new Artisan;

    $artisan->migrate();

    Process::assertRan('php artisan migrate');
});

test('artisan->runCustom(...)', function () {
    Process::fake();
    $artisan = new Artisan;

    $artisan->runCustom('telescope:install');

    Process::assertRan('php artisan telescope:install');
});
