<?php

namespace Tighten\Mise\Steps;

use Tighten\Mise\Tools\Artisan;
use Tighten\Mise\Tools\Composer;
use Tighten\Mise\Tools\ConsoleCommand;
use Tighten\Mise\Tools\CsFixer;
use Tighten\Mise\Tools\File;
use Tighten\Mise\Tools\Git;
use Tighten\Mise\Tools\Npm;

use function Laravel\Prompts\warning;

class Step
{
    public function __construct(
        public Artisan $artisan,
        public Composer $composer,
        public Git $git,
        public ConsoleCommand $console,
        public Npm $npm,
        public File $file,
        public CsFixer $fixer,
    ) {}

    public function name(): string
    {
        return 'Step';
    }

    public function exec(string $exec): static
    {
        warning("DO {$exec}...");
        $this->console->exec($exec);

        return $this;
    }
}
