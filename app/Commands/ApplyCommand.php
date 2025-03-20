<?php

namespace App\Commands;

use App\Recipes\Recipe;
use LaravelZero\Framework\Commands\Command;
use ReflectionClass;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\warning;

class ApplyCommand extends Command
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
            $selected = multiselect(
                label: 'Which recipe(s) should I apply?',
                options: $this->recipes(),
            );

            foreach ($selected as $recipe) {
                /** @var Recipe $instance */
                $instance = app($recipe);
                warning("Applying recipe: {$this->description($instance)}..");
                ($instance)();
            }
        }
    }

    private function recipes(): array
    {
        return collect(config('mise.recipes'))
            ->map(function (string $recipe) {
                if (class_exists($recipe)) {
                    $reflection = new ReflectionClass($recipe);
                    if (! $reflection->isSubclassOf('App\\Recipes\\Recipe')) {
                        return false;
                    }

                    return [$recipe => $this->description($reflection->newInstanceWithoutConstructor())];
                }

                return false;
            })
            ->filter()
            ->flatMap(fn ($recipe) => $recipe)->toArray();
    }

    private function description(Recipe $instance): string
    {
        return "{$instance->name()} by {$instance->vendor()}";
    }
}
