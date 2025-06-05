<?php

namespace App\Commands;

use App\Tools\File;
use Exception;
use Illuminate\Support\Facades\Storage;
use Laravel\Prompts\Concerns\Colors;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

class AddInitializeCommand extends Command
{
    use Colors;

    protected $signature = 'add:init {--force : overwrite existing files}';

    protected $description = 'Add the Mise post-installation file for use in building starter kits';

    /** @throws Exception */
    public function handle(): int
    {
        $createFile = true;
        $filePath = '.mise/initialize.php';

        $fileExists = Storage::fileExists($filePath);

        if ($fileExists && ! $this->option('force')) {
            error("File {$filePath} is already present.");
            $createFile = confirm('Do you want to overwrite it?', false);
        }

        if ($createFile) {
            Storage::makeDirectory('.mise');
            app(File::class)->stub('mise/initialize.php', $filePath);
            info(sprintf('%s setup file %s', $fileExists ? 'Replaced' : 'Created', $filePath));
        }

        return 0;
    }
}
