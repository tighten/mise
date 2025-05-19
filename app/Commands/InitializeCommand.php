<?php

declare(strict_types=1);

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class InitializeCommand extends Command
{
    protected $signature = 'initialize';

    protected $description = 'if present, runs the post install script (./mise/Initialize.php)';

    public function handle(): void {}
}
