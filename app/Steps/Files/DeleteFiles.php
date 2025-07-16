<?php

namespace Tighten\Mise\Steps\Files;

use Tighten\Mise\Steps\Step;

class DeleteFiles extends Step
{
    public function __invoke(array|string $path): void
    {
        $this->file->delete($path);
    }

    public function name(): string
    {
        return 'Delete file(s)';
    }
}
