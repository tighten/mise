<?php

namespace App\Steps;

use App\Tools\Artisan;
use App\Tools\Composer;
use App\Tools\ConsoleCommand;
use App\Tools\Git;
use Illuminate\Support\Facades\Context;

use function Laravel\Prompts\warning;

abstract class Step
{
    public function __construct(
        protected Artisan $artisan,
        protected Composer $composer,
        protected Git $git,
        protected ConsoleCommand $console,
    ) {}

    abstract public function name(): string;

    public function exec(string $exec): void
    {
        warning("DO {$exec}...");
        $this->console->run($exec);
    }

    public function configure(): void
    {
        Context::push('steps', static::class);
    }
}
