<?php

declare(strict_types=1);

namespace Tests\Steps\Laravel;

use App\Steps\Laravel\InstallPulse;

it('Installs Laravel Pulse', function () {
    expect()->stepProcessRan(InstallPulse::class, [
        'composer require laravel/pulse',
        "php artisan vendor:publish --provider='Laravel\Pulse\PulseServiceProvider'",
        'php artisan migrate',
        "git add '.' && git commit -m 'Install Laravel Pulse'",
    ]);
});
