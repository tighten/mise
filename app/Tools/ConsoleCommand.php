<?php

namespace Tighten\Mise\Tools;

use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Process;

class ConsoleCommand
{
    private ProcessResult $result;

    public function exec(string $command): static
    {
        $this->result = Process::run($command)->throw();

        return $this;
    }

    public function vendorExec(string $command): static
    {
        $this->exec("vendor/bin/{$command}");

        return $this;
    }

    public function result(): ProcessResult
    {
        return $this->result;
    }
}
