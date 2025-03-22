<?php

namespace App\Commands;

use App\Recipes\Recipe;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;
use ReflectionClass;

use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;

class ApplyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature =
        'apply {preset?*}'.
        '{--no-process : prevent processes from executing}';

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
        if ($this->option('no-process')) {
            info('Dry run enabled');
            Process::fake();
        }

        if (empty($this->argument('preset'))) {
            $selected = multiselect(
                label: 'Which recipe(s) should I apply?',
                options: $this->recipes(),
            );

            foreach ($selected as $recipe) {
                /** @var Recipe $instance */
                $instance = app($recipe);
                $instance->configure();
                Context::push('recipes', $instance);
            }

            foreach (Context::get('recipes', []) as $recipe) {
                ($recipe)();
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

                    return [$recipe => $reflection->newInstanceWithoutConstructor()->description()];
                }

                return false;
            })
            ->filter()
            ->flatMap(fn ($recipe) => $recipe)->toArray();
    }
}
