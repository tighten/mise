<?php

namespace App\Commands;

use App\Services\MiseService;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;

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

        $remoteRecipes = $this->selectRemoteRecipes();

        foreach ($remoteRecipes as $recipe) {
            $this->install($recipe);
        }

        if (confirm('Do you want to run the recipe now?')) {
            $this->call('apply', [
                'recipe' => $remoteRecipes,
                '--no-process' => $this->option('no-process'),
            ]);
        }
    }

    protected function selectRemoteRecipes(): array
    {
        $selectedRemoteRecipes = $this->argument('recipe');

        if (empty($selectedRemoteRecipes)) {
            return multiselect(
                label: 'Which recipe(s) should I pull?',
                options: app(MiseService::class)->allForSelect(),
            );
        }

        if (count($missingRecipes = array_diff($selectedRemoteRecipes, app(MiseService::class)->keys())) > 0) {
            error('The following keys were not found and will be skipped');
            note(collect($missingRecipes)->map(fn ($key) => "  {$key}")->implode("\n"));
        }

        return app(MiseService::class)->keys()->filter(
            fn (string $key) => in_array($key, $selectedRemoteRecipes)
        )->toArray();
    }

    private function install($key)
    {
        info('Installing recipe: ' . $key);

        $data = app(MiseService::class)->findByKey($key);

        Storage::drive('local-recipes')->put($data['class'] . '.php', $data['file']);

        collect($data['steps'])->each(function ($step) {
            Storage::drive('local-recipes')->put($step['class'] . '.php', $step['file']);
        });
    }
}
