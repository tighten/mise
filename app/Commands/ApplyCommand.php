<?php

namespace App\Commands;

use App\Recipes;
use App\Recipes\Recipe;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;

class ApplyCommand extends Command
{
    protected $signature =
        'apply {recipe?*}'.
        '{--no-process : prevent processes from executing}';

    protected $description = 'Apply one or more recipes';

    public function handle(): void
    {
        if ($this->option('no-process')) {
            info('Dry run enabled');
            Process::fake();
        }

        if (empty($this->argument('recipe'))) {
            $selected = multiselect(
                label: 'Which recipe(s) should I apply?',
                options: (new Recipes)->all(),
            );

            foreach ($selected as $recipe) {
                $this->runRecipe($recipe);
            }
        }

        // @todo... what if recipe *is* passed? Seems like it's not being handled.
    }

    public function runRecipe(string $recipe): void
    {
        $instance = app($recipe);
        Context::push('recipes', $instance);
        $this->header($instance);
        ($instance)();
    }

    public function header(Recipe $recipe)
    {
        info('Applying recipe: ' . $recipe->name());
    }
}
