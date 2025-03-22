<?php

declare(strict_types=1);

namespace App\DevelopmentCommands;

use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;

class DusterLintCommand extends Command
{
    protected $signature = 'duster:lint';

    protected $description = 'Run Duster to lint application code style';

    public function handle(): void
    {
        Process::tty()->run('./vendor/bin/duster lint');
    }
}
