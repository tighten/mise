<?php

namespace App\Commands;

use App\Recipes;
use App\Recipes\Recipe;
use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;

class ApplyCommand extends Command
{
    protected $signature =
        'apply {recipe?*}' .
        '{--no-process : prevent processes from executing}';

    protected $description = 'Apply one or more recipes';

    public function handle(): void
    {
        if ($this->option('no-process')) {
            info('Dry run enabled');
            Process::fake();
        }

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

        if (count($missingRecipes = array_diff($selectedRecipes, $recipes->keys())) > 0) {
            error('The following recipes were not found and will be skipped');
            note(collect($missingRecipes)->map(fn ($recipe) => "  {$recipe}")->implode("\n"));
        }

        return $recipes->all()->filter(
            fn (string $recipeClass, string $key) => in_array($key, $selectedRecipes)
        )->toArray();
    }
}
