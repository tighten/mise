<?php

declare(strict_types=1);

namespace App\Commands;

use App\Tools\Composer;
use Illuminate\Support\Facades\Storage;
use Laravel\Prompts\Concerns\Colors;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\note;
use function Termwind\terminal;

class InitializeCommand extends Command
{
    use Colors;

    protected $signature = 'initialize';

    protected $description = 'runs ./mise/Initialize.php (if present) <fg=red>@todo: fix wording 🔧</>';

    public function handle(): int
    {
        $miseDirectory = '.mise';
        $scriptPath = "{$miseDirectory}/initialize.php";

        if (! $this->runScript($scriptPath)) {
            return 1;
        }

        if (Storage::deleteDirectory($miseDirectory)) {
            $this->mise("Deleted {$this->heavy($miseDirectory)} directory");
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
            // @todo wrap in try/catch (Mise needs beter exception handling in general)
            $class = require Storage::path($scriptPath);
            $this->mise("Running {$this->heavy($scriptPath)}");
            $this->hr();
            ($class)();
            $this->hr();
            $this->mise("Completed {$this->heavy($scriptPath)}");

            return true;
        }
        $this->miseError('Could not find initialization script: ' . $this->heavy($scriptPath));

        return false;
    }

    private function mise(string $text): void
    {

        $label = '[MISE]';
        note(($this->output->isDecorated() ? $this->green($label) : $label) . " {$text}");
    }

    private function heavy(string $text): string
    {
        return $this->output->isDecorated() ? $this->bold($this->black($text)) : $text;
    }

    private function miseError(string $text): void
    {
        $label = '[MISE]';
        note(($this->output->isDecorated() ? $this->red($label) : $label) . " {$text}");
    }

    private function hr(): void
    {
        note(str_repeat('─', terminal()->width() - 1));
    }
}
