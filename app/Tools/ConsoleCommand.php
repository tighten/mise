<?php

namespace App\Tools;

use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\info;

class ConsoleCommand
{
    public function exec(string $command): static
    {
        $cmd = $command;
        // info("Run Tool: '{$command}'");
        Process::run($cmd);

        return $this;
    }

    public function vendorExec(string $command): void
    {
        $this->exec("vendor/bin/{$command}");
    }
}
