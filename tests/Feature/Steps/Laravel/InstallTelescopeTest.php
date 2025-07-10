<?php

namespace Tests\Steps\Laravel;

use Illuminate\Support\Facades\Storage;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;
use Tighten\Mise\Steps\Laravel\InstallTelescope;

beforeEach(function () {
    Storage::fake();
});

it('Installs Laravel Telescope -- production', function () {
    Prompt::fake([Key::ENTER]);

    expect()->stepProcessRan(InstallTelescope::class, [
        'composer require laravel/telescope',
        'php artisan telescope:install',
        'php artisan migrate',
        "git add '.' && git commit -m 'Install Laravel Telescope'",
    ]);
});

it('Installs Laravel Telescope -- dev', function () {
    Prompt::fake([Key::DOWN_ARROW, Key::ENTER]);

    expect()->stepProcessRan(InstallTelescope::class, [
        'composer require laravel/telescope --dev',
        'php artisan telescope:install',
        'php artisan migrate',
        "git add '.' && git commit -m 'Install Laravel Telescope -- local only'",
    ]);

    // ... it does other things but doesn't totally seem like it's worth testing
    // that certain calls are made.
});
