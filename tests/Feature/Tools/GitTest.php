<?php

declare(strict_types=1);

use App\Tools\Git;
use Illuminate\Support\Facades\Process;
use Laravel\Prompts\Prompt;

beforeEach(function () {
    Prompt::fake();
});

test('git->add("path/to/my-file")', function () {
    Process::fake();
    $path = 'path/to/my-file';

    $git = new Git;
    $git->add($path);

    Process::assertRan("git add '{$path}'");
});

test('git->addAll()', function () {
    Process::fake();

    $git = new Git;
    $git->addAll();

    Process::assertRan("git add '.'");
});

test('git->addAll()->commit("My commit message")', function () {
    Process::fake();
    $message = 'My commit message';

    $git = new Git;
    $git->addAll()->commit($message);

    Process::assertRan("git add '.'");
    Process::assertRan("git commit -m '{$message}'");
});

test('git->addAndCommit("My commit message")', function () {
    Process::fake();
    $message = 'My commit message';

    $git = new Git;
    $git->addAndCommit($message);

    Process::assertRan("git add '.' && git commit -m '{$message}'");
});

test("git->addAndCommit('My commit message', '/a/custom/path')", function () {
    Process::fake();
    $message = 'My commit message';
    $path = '/a/custom/path';

    $git = new Git;
    $git->addAndCommit($message, $path);

    Process::assertRan("git add '{$path}' && git commit -m '{$message}'");
});
