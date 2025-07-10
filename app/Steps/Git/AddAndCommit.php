<?php

namespace Tighten\Mise\Steps\Git;

use Tighten\Mise\Steps\Step;

class AddAndCommit extends Step
{
    public function __invoke(string $message): void
    {
        $this->git->addAndCommit($message);
    }

    public function name(): string
    {
        return 'Add and commit to Git';
    }
}
