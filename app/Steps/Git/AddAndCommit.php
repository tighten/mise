<?php

namespace App\Steps\Git;

use App\Steps\Step;

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
