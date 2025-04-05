<?php

namespace App\Steps;

use App\Tools\Artisan;
use App\Tools\Composer;
use App\Tools\ConsoleCommand;
use App\Tools\File;
use App\Tools\FileFormatter;
use App\Tools\Git;
use App\Tools\Npm;

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
        public FileFormatter $formatter,
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
