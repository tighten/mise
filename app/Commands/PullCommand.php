<?php

namespace App\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\info;
use function Laravel\Prompts\select;

class PullCommand extends Command
{
    protected $signature =
        'pull {recipe?*}' .
        '{--no-process : prevent processes from executing}';

    protected $description = 'Pull a recipe from mise.dev';

    public function handle(): void
    {
        if ($this->option('no-process')) {
            info('Dry run enabled');
            Process::fake();
        }
        // @todo: Pull more than one recipe at a time
        $recipe = $this->pullRecipe();
        $this->intallRecipe($recipe);

        // @todo: Should we prompt the user to continue
        // if (! confirm('Do you want to run the recipe now?', true)) {
        //     info('Recipe pulled, but not run.');
        // }
    }

    public function intallRecipe(string $recipe): void
    {
        info('Installing recipe: ' . $recipe);

        $this->pullFromRecipeFromApi($recipe);
    }

    protected function pullRecipe(): string
    {
        $slug = $this->argument('recipe');

        if (empty($slug)) {
            return select(
                label: 'Which recipe(s) should I pull?',
                options: $this->pullFromListFromApi(),
            );
        }

        return $slug;
    }

    private function pullFromListFromApi()
    {
        // @todo: cleanup api config
        $data = Http::get('http://mise-app.test/api/recipes')
            ->throw()
            ->json('data');

        return collect($data)->pluck('name', 'slug')->all();
    }

    private function pullFromRecipeFromApi($string)
    {
        $data = Http::get('http://mise-app.test/api/recipes/' . $string)
            ->throw()
            ->json('data');

        Storage::drive('local-recipes')->put($data['class'] . '.php', $data['file']);

        collect($data['steps'])->each(function ($step) {
            Storage::drive('local-recipes')->put($step['class'] . '.php', $step['file']);
        });
    }
}
