<?php

declare(strict_types=1);

namespace App\Commands;

use App\Tools\Composer;
use Illuminate\Support\Facades\Storage;
use Laravel\Prompts\Concerns\Colors;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\note;

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

        if (Storage::delete($scriptPath)) {
            $this->mise("Deleted {$this->heavy($scriptPath)}");
        }

        $package = 'tightenco/mise';
        app(Composer::class)->remove($package);
        if (Storage::missing("vendor/{$package}")) {
            $this->mise("Removed {$this->heavy($package)}");

            return 0;
        }

        $this->miseError("Failed to remove {$this->heavy($scriptPath)}");

        return 1;
    }

    private function runScript(string $scriptPath): bool
    {
        if (Storage::fileExists("{$scriptPath}")) {
            // @todo wrap in try/catch
            $class = require Storage::path($scriptPath);
            $this->mise("Running {$this->heavy($scriptPath)}");
            ($class)();

            return true;
        }
        $this->miseError('Could not find initialization script: ' . $this->heavy($scriptPath));

        return false;
    }

    private function mise(string $text): void
    {
        note($this->green('[MISE]') . " {$text}");
    }

    private function heavy(string $text): string
    {
        return $this->bold($this->black($text));
    }

    private function miseError(string $text): void
    {
        note($this->red('[MISE]') . " {$text}");
    }
}
