<?php

namespace App\Steps;

use App\Tools\Artisan;
use App\Tools\Composer;
use App\Tools\ConsoleCommand;
use App\Tools\File;
use App\Tools\Git;
use App\Tools\Npm;

use function Laravel\Prompts\warning;

abstract class Step
{
    public function __construct(
        protected Artisan $artisan,
        protected Composer $composer,
        protected Git $git,
        protected ConsoleCommand $console,
        protected Npm $npm,
        protected File $file,
    ) {}

    abstract public function name(): string;

    public function exec(string $exec): void
    {
        warning("DO {$exec}...");
        $this->console->exec($exec);
    }
}
