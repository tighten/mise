<?php

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

    protected $description = 'Run Mise initialization steps and remove Mise (if defined in ./.mise/initialize.php)';

    public function handle(): int
    {
        $miseDirectory = '.mise';

        if (! $this->runScript("{$miseDirectory}/initialize.php")) {
            return 1;
        }

        if (Storage::deleteDirectory($miseDirectory)) {
            $this->mise("Deleted {$this->heavy($miseDirectory)} directory");
        } else {
            $this->miseError("Failed deleting {$this->heavy($miseDirectory)}; please delete manually.");
        }

        $package = 'tightenco/mise';
        $composer = app(Composer::class);
        $composer->remove($package);
        if (! $composer->hasDependency($package) && Storage::missing("vendor/{$package}")) {
            $this->mise("Removed composer package {$this->heavy($package)}");
        } else {
            $this->miseError("Failed to remove composer package {$this->heavy($package)}; please remove manually.");

            return 1;
        }

        return 0;
    }

    private function runScript(string $scriptPath): bool
    {
        if (! Storage::fileExists("{$scriptPath}")) {
            $this->miseError('Could not find initialization script: ' . $this->heavy($scriptPath));

            return false;
        }

        // @todo wrap in try/catch (Mise needs better exception handling in general)
        $class = require Storage::path($scriptPath);
        $this->mise("Running {$this->heavy($scriptPath)}");
        $this->hr();
        ($class)();
        $this->hr();
        $this->mise("Completed {$this->heavy($scriptPath)}");

        return true;
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
