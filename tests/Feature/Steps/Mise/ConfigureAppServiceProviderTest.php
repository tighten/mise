<?php

declare(strict_types=1);

namespace Tests\Steps\Laravel;

use App\Steps\Mise\ConfigureAppServiceProvider;
use Illuminate\Support\Facades\Storage;
use Mockery;

it('configures \App\Providers\AppServiceProvider', function () {

    Storage::fake('local');

    Storage::shouldReceive('delete')->with('app/Providers/AppServiceProvider.php');
    Storage::shouldReceive('put')->withArgs([
        'app/Providers/AppServiceProvider.php',
        Mockery::type('string'),
    ]);

    expect()->stepProcessRan(ConfigureAppServiceProvider::class, [
        'vendor/bin/pint app/Providers/AppServiceProvider.php',
        "git add '.' && git commit -m 'Configure App\Providers\AppServiceProvider'",
    ]);
});
