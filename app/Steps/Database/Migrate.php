<?php

namespace App\Steps\Database;

use App\Steps\Step;

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
