<?php

namespace Tighten\Mise\Tools;

use Illuminate\Support\Facades\Context;

class Git extends ConsoleCommand
{
    public function add(string $path): static
    {
        if (Context::get('no-git')) {
            return $this;
        }

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
        if (Context::get('no-git')) {
            return $this;
        }

        $this->exec("git commit -m '{$message}'");

        return $this;
    }

    public function addAndCommit(string $message, string $path = '.'): static
    {
        if (Context::get('no-git')) {
            return $this;
        }

        $this->exec("git add '{$path}' && git commit -m '{$message}'");

        return $this;
    }
}
