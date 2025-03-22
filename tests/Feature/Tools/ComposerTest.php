<?php

declare(strict_types=1);

use App\Tools\Composer;
use Illuminate\Support\Facades\Process;
use Laravel\Prompts\Prompt;

beforeEach(function () {
    Prompt::fake();
});

test('composer->requireDev(...)', function () {
    Process::fake();

    $composer = new Composer;

    $composer->requireDev('tightenco/duster');

    Process::assertRan('composer require tightenco/duster --dev');
});

test('composer->require(...)', function () {
    Process::fake();

    $composer = new Composer;

    $composer->require('tightenco/duster');

    Process::assertRan('composer require tightenco/duster');
});
