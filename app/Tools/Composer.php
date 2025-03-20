<?php

declare(strict_types=1);

namespace App\Tools;

class Composer extends ConsoleCommand
{
    public function requireDev(string $package): void
    {
        $this->run("composer require {$package} --dev");
    }

    public function require(string $package): void
    {
        $this->run("composer require {$package}");
    }
}
