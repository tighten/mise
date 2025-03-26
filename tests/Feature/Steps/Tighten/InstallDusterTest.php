<?php

namespace Tests\Steps\Laravel;

use App\Steps\Duster\Install;
use Laravel\Prompts\Prompt;

it('Installs Tighten Duster', function () {
    Prompt::fake();

    expect()->stepProcessRan(Install::class, [
        'composer require tightenco/duster --dev',
        "git add '.' && git commit -m 'Install Duster'",
        './vendor/bin/duster fix',
        "git add '.' && git commit -m 'Run Duster'",
    ]);
});
