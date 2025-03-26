<?php

namespace Tests\Steps\Laravel;

use App\Steps\Laravel\InstallPulse;
use Laravel\Prompts\Prompt;

it('Installs Laravel Pulse', function () {
    Prompt::fake();

    expect()->stepProcessRan(InstallPulse::class, [
        'composer require laravel/pulse',
        "php artisan vendor:publish --provider='Laravel\Pulse\PulseServiceProvider'",
        'php artisan migrate',
        "git add '.' && git commit -m 'Install Laravel Pulse'",
    ]);
});
