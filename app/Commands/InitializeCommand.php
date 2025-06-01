<?php

declare(strict_types=1);

namespace App\Commands;

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
        $scriptPath = '.mise/Initialize.php';

        if (! $this->runScript($scriptPath)) {
            return 1;
        }

        if (Storage::deleteDirectory('.mise')) {
            info('Deleted: ' . $scriptPath);
        }

        return 0;
    }

    private function runScript(string $scriptPath): bool
    {
        $displayScriptPath = $this->bold($this->black($scriptPath));
        if (Storage::fileExists($scriptPath)) {
            // @todo wrap in try/catch
            $class = require Storage::path($scriptPath);
            info('Running: ' . $displayScriptPath);
            ($class)();

            return true;
        }
        error('Could not find initialization script: ' . $displayScriptPath);

        return false;
    }
}
