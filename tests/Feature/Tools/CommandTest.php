<?php

use Illuminate\Support\Facades\Process;
use Tighten\Mise\Tools\ConsoleCommand;

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
