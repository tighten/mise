<?php

namespace App\Commands;

use App\Recipes;
use App\Recipes\Recipe;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;
use ReflectionClass;

use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;

class ApplyCommand extends Command
{
    protected $signature =
        'apply {preset?*}'.
        '{--no-process : prevent processes from executing}';

    protected $description = 'Apply one or more presets';

    public function handle(): void
    {
        if ($this->option('no-process')) {
            info('Dry run enabled');
            Process::fake();
        }

        if (empty($this->argument('preset'))) {
            $selected = multiselect(
                label: 'Which recipe(s) should I apply?',
                options: (new Recipes)->all(),
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
}
