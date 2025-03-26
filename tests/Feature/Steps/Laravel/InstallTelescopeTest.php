<?php

declare(strict_types=1);

namespace Tests\Steps\Laravel;

use App\Steps\Laravel\InstallTelescope;
use Illuminate\Support\Facades\Context;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

it('installs Laravel Telescope', function () {

    Prompt::fake([Key::ENTER]);

    expect()->stepProcessRan(InstallTelescope::class, [
        'composer require laravel/telescope',
        'php artisan telescope:install',
        'php artisan migrate',
        "git add '.' && git commit -m 'Install Laravel Telescope'",
    ]);
    expect(Context::has('register_local_only_providers'))->toBeFalse();

});

it('installs Laravel Telescope in local only mode', function () {

    Prompt::fake([Key::DOWN, Key::ENTER]);

    expect()->stepProcessRan(InstallTelescope::class, [
        'composer require laravel/telescope --dev',
        'php artisan telescope:install',
        'php artisan migrate',
        "git add '.' && git commit -m 'Install Laravel Telescope'",
    ]);
    expect(Context::has('register_local_only_providers'))->toBeTrue();
});
