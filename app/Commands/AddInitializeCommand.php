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

    private string $relativeFilePath;
    private string $filePath;
    private string $relativeBasePath;

    /** @throws Exception */
    public function handle(): int
    {
        $baseDirectory = '.mise';
        $relativeFilePath = "{$baseDirectory}/Initialize.php";
        $filePath = Storage::path($relativeFilePath);

        $createFile = true;

        Storage::makeDirectory($baseDirectory);

        $fileExists = Storage::fileExists($relativeFilePath);

        if ($fileExists && ! $this->option('force')) {
            error("File {$filePath} is allready present.");
            $createFile = confirm('Do you want to overwrite it?', false);
        }

        if ($createFile) {
            app(File::class)->stub('mise/Initialize.php', $relativeFilePath);
            info(sprintf('%s setup file %s', $fileExists ? 'Replaced' : 'Created', $filePath));
        }

        return 0;
    }
}
