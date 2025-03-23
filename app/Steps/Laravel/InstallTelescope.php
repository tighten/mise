<?php

declare(strict_types=1);

namespace App\Steps\Laravel;

use App\Steps\Step;

use function Laravel\Prompts\select;

class InstallTelescope extends Step
{
    public function __invoke(): void
    {
        $this->composer->requireDev('laravel/telescope');
        $this->artisan->runCustom('telescope:install');
        $this->artisan->migrate();
        $this->git->addAndCommit('Install Laravel Telescope');
    }

    public function name(): string
    {
        return 'Laravel Telescope';
    }

    public function configure(): void
    {
        // @todo: Is this being stored anywhere? And is there any reason not to just do it inline?
        // ... I guess the thing it offers is the ability to ask your recipe questions all at once,
        // but I don't know if that's worth the technical complexity it adds.
        select(label: 'Should Telescope be available in Production?', options: [
            true => 'Yes',
            false => 'No',
        ]);

        parent::configure();
    }
}
