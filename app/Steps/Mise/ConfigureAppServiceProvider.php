<?php

declare(strict_types=1);

namespace App\Steps\Mise;

use App\Steps\Step;
use Throwable;

class ConfigureAppServiceProvider extends Step
{
    /** @throws Throwable */
    public function __invoke(): void
    {
        $this->file->delete('app/Providers/AppServiceProvider.php');
        $this->file->create('app/Providers/AppServiceProvider.php', view('app-service-provider')->render());
        $this->console->vendorExec('pint app/Providers/AppServiceProvider.php');
        $this->git->addAndCommit('Configure App\Providers\AppServiceProvider');
    }

    public function name(): string
    {
        return 'Configure App\Providers\AppServiceProvider';
    }
}
