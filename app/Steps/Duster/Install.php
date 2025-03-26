<?php

namespace App\Steps\Duster;

use App\Steps\Step;

class Install extends Step
{
    public function __invoke(): void
    {
        $this->composer->requireDev('tightenco/duster');
        $this->git->addAll()->commit('Install Duster');
        $this->exec('./vendor/bin/duster fix');
        $this->git->addAll()->commit('Run Duster');
    }

    public function name(): string
    {
        return 'Install and run Duster';
    }
}
