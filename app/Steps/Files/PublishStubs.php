<?php

namespace Tighten\Mise\Steps\Files;

use Tighten\Mise\Steps\Step;

class PublishStubs extends Step
{
    public function __invoke(string $path): void
    {
        $this->file->stubAll($path);
    }

    public function name(): string
    {
        return 'Publish stubs from a given path';
    }
}
