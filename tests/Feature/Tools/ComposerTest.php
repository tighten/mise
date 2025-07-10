<?php

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Tighten\Mise\Tools\Composer;

beforeEach(function () {
    Storage::fake();
    Process::fake();
});

it('requires dev dependencies - composer->requireDev(...)', function () {
    $composer = new Composer;

    $composer->requireDev('tightenco/duster');

    Process::assertRan('composer require tightenco/duster --dev');
});

it('requires production dependencies - composer->require(...)', function () {
    $composer = new Composer;

    $composer->require('tightenco/duster');

    Process::assertRan('composer require tightenco/duster');
});

it('removes production dependencies - composer->remove(...)', function () {
    $composer = new Composer;

    $composer->remove('tightenco/mise');

    Process::assertRan('composer remove tightenco/mise');
});

it('removes dev dependencies - composer->removeDev(...)', function () {
    $composer = new Composer;

    $composer->removeDev('tightenco/mise');

    Process::assertRan('composer remove tightenco/mise --dev');
});

it('loads composer.json - composer->getComposerConfiguration()', function () {
    $composerJson = '{"name": "tightenco/mise", "require-dev": { "tightenco/duster": "^3.2" }}';
    Storage::put('composer.json', $composerJson);

    $composer = new Composer;

    expect($composer->composerConfiguration())
        ->toBe(json_decode($composerJson, true));
});

it('gets production dependencies - composer->getProductionDependencies()', function () {
    $composerJson = '{"name": "tightenco/mise", "require": { "tightenco/tlint": "^3.2" }, "require-dev": { "tightenco/duster": "^3.2" }}';
    Storage::put('composer.json', $composerJson);

    $composer = new Composer;

    expect($composer->productionDependencies())
        ->toBe(['tightenco/tlint' => '^3.2']);
});

it('gets dev dependencies - composer->getDevDependencies()', function () {
    $composerJson = '{"name": "tightenco/mise", "require": { "tightenco/tlint": "^3.2" }, "require-dev": { "tightenco/duster": "^3.2" }}';
    Storage::put('composer.json', $composerJson);

    $composer = new Composer;
    expect($composer->developmentDependencies())
        ->toBe(['tightenco/duster' => '^3.2']);
});

it('has dev dependency - composer->hasDevDependency(...)', function () {
    $composerJson = '{"name": "tightenco/mise", "require": { "tightenco/tlint": "^3.2" }, "require-dev": { "tightenco/duster": "^3.2" }}';
    Storage::put('composer.json', $composerJson);

    $composer = new Composer;

    expect($composer->hasDevDependency('tightenco/duster'))
        ->toBeTrue();
});

it('has no dev dependency - composer->hasDevDependency(...)', function () {
    $composerJson = '{"name": "tightenco/mise", "require": { "tightenco/tlint": "^3.2" }, "require-dev": { "tightenco/duster": "^3.2" }}';
    Storage::put('composer.json', $composerJson);

    $composer = new Composer;

    expect($composer->hasDevDependency('tightenco/tlint'))
        ->toBeFalse();
});

it('has production dependency - composer->hasProductionDependency(...)', function () {
    $composerJson = '{"name": "tightenco/mise", "require": { "tightenco/tlint": "^3.2" }, "require-dev": { "tightenco/duster": "^3.2" }}';
    Storage::put('composer.json', $composerJson);

    $composer = new Composer;

    expect($composer->hasProductionDependency('tightenco/tlint'))
        ->toBeTrue();
});

it('has no production dependency - composer->hasProductionDependency(...)', function () {
    $composerJson = '{"name": "tightenco/mise", "require": { "tightenco/tlint": "^3.2" }, "require-dev": { "tightenco/duster": "^3.2" }}';
    Storage::put('composer.json', $composerJson);

    $composer = new Composer;

    expect($composer->hasProductionDependency('tightenco/duster'))
        ->toBeFalse();
});

it('has dependency - composer->hasDependency(...)', function () {
    $composerJson = '{"name": "tightenco/mise", "require": { "tightenco/tlint": "^3.2" }, "require-dev": { "tightenco/duster": "^3.2" }}';
    Storage::put('composer.json', $composerJson);

    $composer = new Composer;

    expect($composer->hasDependency('tightenco/tlint'))
        ->toBeTrue();

    expect($composer->hasDependency('tightenco/duster'))
        ->toBeTrue();
});

it('has no dependency - composer->hasDependency(...)', function () {
    $composerJson = '{"name": "tightenco/mise", "require": { "tightenco/tlint": "^3.2" }, "require-dev": { "tightenco/duster": "^3.2" }}';
    Storage::put('composer.json', $composerJson);

    $composer = new Composer;

    expect($composer->hasDependency('tightenco/mise'))
        ->toBeFalse();
});
