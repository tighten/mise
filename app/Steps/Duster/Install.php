<?php

namespace App\Steps\Duster;

use App\Steps\Step;

class Install extends Step
{
    public function __invoke()
    {
        $this->composer->requireDev('tightenco/duster');
        $this->git->add('.')->commit('Install Duster');
        // or $this->git->addAndCommit('Install Duster'), not sure
        
        $this->exec('./vendor/bin/duster fix');
        $this->git->add('.')->commit('Run Duster');
    }
}
