<?php

namespace Tighten\Mise\Steps\Database;

use Tighten\Mise\Steps\Step;

class Migrate extends Step
{
    public function __invoke(): void
    {
        $this->artisan->migrate();
    }

    public function name(): string
    {
        return 'Run database migrations';
    }
}
