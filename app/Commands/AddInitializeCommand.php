<?php

declare(strict_types=1);

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

    protected $signature = 'add:init-command {--force : overwrite existing files}';

    protected $description = 'Add the Mise post-installation files';

    /** @throws Exception */
    public function handle(): int
    {
        $createFile = true;
        $filePath = '.mise/initialize.php';

        Storage::makeDirectory('.mise');

        $fileExists = Storage::fileExists($filePath);
        if ($fileExists && ! $this->option('force')) {
            error("File {$filePath} is allready present.");
            $createFile = confirm('Do you want to overwrite it?', false);
        }

        if ($createFile) {
            app(File::class)->stub('mise/initialize.php', $filePath);
            info(sprintf('%s setup file %s', $fileExists ? 'Replaced' : 'Created', $filePath));
        }

        return 0;
    }
}
