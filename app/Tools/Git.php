<?php

namespace App\Tools;

class Git extends ConsoleCommand
{
    public function add(string $path): static
    {
        $this->exec("git add '{$path}'");

        return $this;
    }

    public function addAll(): static
    {
        $this->add('.');

        return $this;
    }

    public function commit(string $message): static
    {
        $this->exec("git commit -m '{$message}'");

        return $this;
    }

    public function addAndCommit(string $message, string $path = '.'): static
    {
        $this->exec("git add '{$path}' && git commit -m '{$message}'");

        return $this;
    }
}
