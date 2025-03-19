<?php

namespace App\Commands;

use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use ReflectionClass;

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
    public function handle(): void
    {

        if (empty($this->argument('preset'))) {
            $recipesPath = app_path('Recipes');
            $recipes = collect(File::allFiles($recipesPath))
                ->filter(function ($file) {
                    return $file->getExtension() === 'php';
                })
                ->map(function ($file) use ($recipesPath) {
                    // Convert file path to namespace format
                    $relativePath = str_replace([$recipesPath.'/', '.php'], '', $file->getPathname());
                    $className = 'App\\Recipes\\'.str_replace('/', '\\', $relativePath);

                    if (class_exists($className)) {
                        $reflection = new ReflectionClass($className);

                        if ($reflection->isAbstract()) {
                            return false;
                        }

                        if (! $reflection->isSubclassOf('App\\Recipes\\Recipe')) {
                            return false;
                        }

                        return $className;
                    }

                    return false;
                })
                ->filter()
                ->sort()
                ->values();

            $selected = multiselect(
                label: 'Which recipe(s) should I apply?',
                options: $recipes
            );

            foreach ($selected as $recipe) {
                warning("Applying recipe: {$recipe}..");
                app($recipe)();

            }
        }
    }
}
