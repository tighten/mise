<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use App\Steps\Step;

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
