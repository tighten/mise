<?php

declare(strict_types=1);

namespace Tests\Steps\Laravel;

use App\Steps\Laravel\InstallTelescope;

it('Installs Laravel Telescope', function () {
    expect()->stepProcessRan(InstallTelescope::class, [
        'composer require laravel/telescope --dev',
        'php artisan telescope:install',
        'php artisan migrate',
        "git add '.' && git commit -m 'Install Laravel Telescope'",
    ]);
});
