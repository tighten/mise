<?php

namespace Tighten\Mise\Tools;

class Artisan extends ConsoleCommand
{
    public function vendorPublish(string $provider): static
    {
        $this->exec("php artisan vendor:publish --provider='{$provider}'");

        return $this;
    }

    public function migrate(): static
    {
        $this->exec('php artisan migrate');

        return $this;
    }

    public function runCustom(string $command): static
    {
        return $this->exec("php artisan {$command}");
    }
}
