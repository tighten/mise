<?php

namespace App\Tools;

use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\info;

class ConsoleCommand
{
    public function exec(string $command): static
    {
        $cmd = $command;
        // info(" > '{$command}'");
        Process::run($cmd);

        return $this;
    }

    public function vendorExec(string $command): static
    {
        $this->exec("vendor/bin/{$command}");

        return $this;
    }
}
