<?php

namespace Tests\Steps\Laravel;

use App\Steps\Laravel\InstallTelescope;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

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

    // @todo: Assert against the other things we expect the dev version to do
});
