<?php

declare(strict_types=1);

namespace App\Commands;

use App\Tools\File;
use Illuminate\Support\Facades\Storage;
use Laravel\Prompts\Concerns\Colors;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

class InitializeCommand extends Command
{
    use Colors;

    protected $signature = 'initialize';

    protected $description = 'runs ./mise/Initialize.php (if present) <fg=red>@todo: fix wording 🔧</>';

    public function handle(): int
    {
        $initializeScript = '.mise/Initialize.php';
        $initializeScriptPath = $this->bold($this->black(Storage::path($initializeScript)));
        if (File::fileExists($initializeScript)) {
            $class = require $initializeScript;
            info('Running: ' . $initializeScriptPath);
            ($class)();

            return 0;
        }
        error('Could not find initialization script: ' . $initializeScriptPath);

        return 1;
    }
}
