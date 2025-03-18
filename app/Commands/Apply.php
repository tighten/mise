<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\warning;

class Apply extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apply {preset?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply one or more presets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (empty($this->argument('preset'))) {
            $this->info('Available recipes:');

            $recipesPath = app_path('Recipes');

            $files = File::allFiles($recipesPath);

            $recipes = collect($files)
                ->filter(function ($file) {
                    // Include only PHP files
                    if ($file->getExtension() !== 'php') {
                        return false;
                    }

                    // Exclude abstract classes
                    $content = file_get_contents($file->getPathname());
                    if (preg_match('/abstract\s+class/i', $content)) {
                        return false;
                    }

                    return true;
                })
                ->map(function ($file) use ($recipesPath) {
                    // Get the relative path from the recipes directory
                    $relativePath = File::dirname(
                        str_replace($recipesPath.'/', '', $file->getPathname())
                    );

                    // Get filename without extension
                    $filename = $file->getFilenameWithoutExtension();

                    // If it's in the root recipes directory, just return the filename
                    if ($relativePath === '.') {
                        return $filename;
                    }

                    // Otherwise return parent directory with filename
                    return sprintf('%s\\%s', $relativePath, $filename);
                })
                ->sort()
                ->values();

            $selected = multiselect(
                label: 'Which recipe(s) should I apply?',
                options: $recipes
            );

            foreach ($selected as $recipe) {
                warning("Applying recipe: {$recipe} ..");
                app("App\\Recipes\\{$recipe}")();

            }

            return;
        }
    }

    /**
     * Define the command's schedule.
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
