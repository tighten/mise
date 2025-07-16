<?php

namespace Tighten\Mise\Tools;

use Illuminate\Support\Facades\Process;

class ConsoleCommand
{
    public function exec(string $command): static
    {
        Process::run($command);

        return $this;
    }

    public function vendorExec(string $command): static
    {
        $this->exec("vendor/bin/{$command}");

        return $this;
    }
}
