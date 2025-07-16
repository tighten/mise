<?php

namespace Tests\Fixtures;

use Tighten\Mise\Steps\Step;

class TestStep extends Step
{
    public function __invoke(?string $message = 'Hello World'): void
    {
        $this->console->exec("echo '{$message}'");
    }

    public function name(): string
    {
        return 'Test Step';
    }
}
