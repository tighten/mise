<?php

declare(strict_types=1);

namespace App\Tools;

use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\info;

class ConsoleCommand
{
    public function run(string $command): static
    {
        $cmd = $command;

        info("Run Tool: '{$command}'");

        Process::run($cmd);

        return $this;
    }

    public function vendorRun(string $command): void
    {
        $this->run("vendor/bin/{$command}");
    }
}
