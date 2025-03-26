<?php

namespace App\Steps\Duster;

use App\Steps\Step;

class Ci extends Step
{
    public function __invoke(): void
    {
        $this->console->vendorExec('duster github-actions');
        $this->git->addAndCommit("Add Duster's GitHub Actions workflow");
    }

    public function name(): string
    {
        return "Add Duster's GitHub Actions workflow";
    }
}
