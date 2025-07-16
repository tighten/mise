<?php

namespace Tighten\Mise\Steps\Files;

use Tighten\Mise\Steps\Step;

class CreateFile extends Step
{
    public function __invoke(string $path, string $content = ''): void
    {
        $this->file->create($path, $content);
    }

    public function name(): string
    {
        return 'Create file';
    }
}
