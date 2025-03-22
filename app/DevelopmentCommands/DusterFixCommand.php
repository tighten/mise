<?php

declare(strict_types=1);

namespace App\DevelopmentCommands;

use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;

class DusterFixCommand extends Command
{
    protected $signature = 'duster:fix';

    protected $description = 'Run Duster to fix application code style';

    public function handle(): void
    {
        Process::tty()->run('./vendor/bin/duster fix');
    }
}
