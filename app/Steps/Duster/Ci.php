<?php

namespace Tighten\Mise\Steps\Duster;

use Tighten\Mise\Steps\Step;

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
