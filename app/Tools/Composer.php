<?php

declare(strict_types=1);

namespace App\Tools;

class Composer extends ConsoleCommand
{
    public function requireDev(string $string): void
    {
        $this->run("composer require $string --dev");
    }
}
