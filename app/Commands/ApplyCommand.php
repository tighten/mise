<?php

namespace Tighten\Mise\Commands;

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;
use Tighten\Mise\Recipes;
use Tighten\Mise\Recipes\Recipe;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;

class ApplyCommand extends Command
{
    protected $signature =
        'apply {recipe?*}' .
        '{--no-process : prevent processes from executing}' .
        '{--no-git : prevent processes from executing}';

    protected $description = 'Apply one or more recipes';

    public function handle(): void
    {
        if ($this->option('no-process')) {
            info('Dry run enabled');
            Process::fake();
        }

        Context::add('no-git', $this->option('no-git'));

        foreach ($this->selectedRecipes() as $recipe) {
            $this->runRecipe($recipe);
        }
    }

    public function runRecipe(string $recipe): void
    {
        $instance = app($recipe);
        $this->header($instance);
        ($instance)();
    }

    public function header(Recipe $recipe): void
    {
        info('Applying recipe: ' . $recipe->name());
    }

    protected function selectedRecipes(): array
    {
        $recipes = new Recipes;
        $selectedRecipes = $this->argument('recipe');

        if (empty($selectedRecipes)) {
            return multiselect(
                label: 'Which recipe(s) should I apply?',
                options: $recipes->allForSelect(),
            );
        }

        if (count($missingRecipes = array_diff($selectedRecipes, $recipes->keys()->toArray())) > 0) {
            error('The following recipes were not found and will be skipped');
            note(collect($missingRecipes)->map(fn ($recipe) => "  {$recipe}")->implode("\n"));
        }

        return $recipes->allKeysAndClasses()->filter(
            fn (string $recipeClass, string $key) => in_array($key, $selectedRecipes)
        )->toArray();
    }
}
