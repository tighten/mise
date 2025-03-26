<?php

use App\Tools\ConsoleCommand;
use Illuminate\Support\Facades\Process;

test('console->exec(...)', function () {
    Process::fake();

    $consoleCommand = new ConsoleCommand;

    $consoleCommand->exec('command --random --flags');

    Process::assertRan('command --random --flags');
});

test('console->vendorExec(...)', function () {
    Process::fake();

    $consoleCommand = new ConsoleCommand;

    $consoleCommand->vendorExec('command --random --flags');

    Process::assertRan('vendor/bin/command --random --flags');
});
