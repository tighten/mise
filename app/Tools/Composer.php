<?php

namespace App\Tools;

class Composer extends ConsoleCommand
{
    public function requireDev(string $package): void
    {
        $this->exec("composer require {$package} --dev");
    }

    public function require(string $package): void
    {
        $this->exec("composer require {$package}");
    }
}
