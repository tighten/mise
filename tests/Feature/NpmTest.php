<?php

namespace Tests\Feature;

use App\Tools\Npm;
use Illuminate\Support\Facades\Process;

beforeEach(function () {
    Process::fake();
});

it('can install dependencies', function () {
    (new Npm)->install();

    Process::assertRan('npm install');
});

it('can install dev dependencies', function () {
    (new Npm)->saveDev('tailwindcss');

    Process::assertRan('npm install tailwindcss --save-dev');
});

it('can install production dependencies', function () {
    (new Npm)->save('prettier');

    Process::assertRan('npm install prettier --save');
});

it('can add scripts', function () {
    (new Npm)->addScript('format', 'prettier --write resources/');

    Process::assertRan('npm pkg set scripts.format="prettier --write resources/"');
});

it('can run scripts', function () {
    (new Npm)->run('dev');

    Process::assertRan('npm run dev');
});

it('can run ci', function () {
    (new Npm)->ci();

    Process::assertRan('npm ci');
});

it('can update dependencies', function () {
    (new Npm)->update();

    Process::assertRan('npm update');
});
