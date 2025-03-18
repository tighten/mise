<?php

declare(strict_types=1);

use App\Tools\Git;
use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;

test('git->add("path/to/my-file")', function () {
    Process::fake();

    $git = new Git;

    $git->add('path/to/my-file');

    Process::assertRan('git add path/to/my-file');
});

test('git->addAll()', function () {
    Process::fake();

    $git = new Git;

    $git->addAll();

    Process::assertRan('git add .');
});

test('git->addAll()->commit("My commit message")', function () {
    Process::fake();

    $git = new Git;

    $git->addAll()->commit('My commit message');

    Process::assertRan('git add .');
    Process::assertRan("git commit -m 'My commit message'");
});

test('git->addAndCommit("My commit message")', function () {
    Process::fake();

    $git = new Git;

    $git->addAndCommit('My commit message');

    Process::assertRan('git add .');
    Process::assertRan("git commit -m 'My commit message'");
});
