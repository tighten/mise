<?php

declare(strict_types=1);

use App\Tools\ConsoleCommand;
use Illuminate\Support\Facades\Process;

test('console->run(...)', function () {
    Process::fake();

    $consoleCommand = new ConsoleCommand;

    $consoleCommand->run('command --random --flags');

    Process::assertRan('command --random --flags');
});

test('console->vendorRun(...)', function () {
    Process::fake();

    $consoleCommand = new ConsoleCommand;

    $consoleCommand->vendorRun('command --random --flags');

    Process::assertRan('vendor/bin/command --random --flags');
});
