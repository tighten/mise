<?php

namespace App\Tools;

class Composer extends ConsoleCommand
{
    public function requireDev(string $package): static
    {
        $this->exec("composer require {$package} --dev");

        return $this;
    }

    public function require(string $package): static
    {
        $this->exec("composer require {$package}");

        return $this;
    }

    public function remove(string $package): static
    {
        $this->exec("composer remove {$package}");

        return $this;
    }
}
