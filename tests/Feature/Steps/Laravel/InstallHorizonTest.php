<?php

namespace Tests\Steps\Laravel;

use Tighten\Mise\Steps\Laravel\InstallHorizon;

it('Installs Laravel Horizon', function () {
    expect()->stepProcessRan(InstallHorizon::class, [
        'composer require laravel/horizon',
        'php artisan horizon:install',
        'php artisan migrate',
        "git add '.' && git commit -m 'Install Laravel Horizon'",
    ]);
});
