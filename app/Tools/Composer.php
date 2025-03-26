<?php

namespace App\Tools;

class Composer extends ConsoleCommand
{
    // @todo: slashes are incorrectly escaped in requiredev, and probably require as well
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
}
