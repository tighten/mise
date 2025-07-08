<?php

namespace App\Commands;

use App\Services\LocalRecipesService;
use App\Services\MiseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Process;
use LaravelZero\Framework\Commands\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multisearch;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;

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

        $recipeDiff = $this->diffRecipes($remoteRecipes);

        $recipesToInstall = $this->handleConflicts($recipeDiff);

        if (empty($recipesToInstall)) {
            info('No recipes to install.');

            return;
        }

        foreach ($recipesToInstall as $recipe) {
            $this->install($recipe);
        }

        if (confirm(sprintf('Should I apply %s?', implode(', ', $recipesToInstall)))) {
            $this->call('apply', [
                'recipe' => $recipesToInstall,
                '--no-process' => $this->option('no-process'),
            ]);
        }
    }

    private function selectRemoteRecipes(): array
    {
        $selectedRemoteRecipes = $this->argument('recipe');

        if (empty($selectedRemoteRecipes)) {
            $options = app(MiseService::class)->allForSelect();

            return multisearch(
                'Which recipe(s) should I pull?',
                fn (string $value) => strlen($value) > 0
                    ? $options->filter(fn ($option) => str_contains($option, $value))->all()
                    : $options->all(),
            );
        }

        if (count($missingRecipes = array_diff($selectedRemoteRecipes, app(MiseService::class)->keys()->toArray())) > 0) {
            error('The following keys were not found and will be skipped');
            note(collect($missingRecipes)->map(fn ($key) => "  {$key}")->implode("\n"));
        }

        return app(MiseService::class)->keys()->filter(
            fn (string $key) => in_array($key, $selectedRemoteRecipes)
        )->toArray();
    }

    private function diffRecipes(array $recipeKeys): Collection
    {
        $miseService = app(MiseService::class)->all();

        return collect($recipeKeys)->map(function ($key) use ($miseService) {
            $remoteRecipe = $miseService->first(fn ($recipe) => $recipe['key'] === $key);

            $status = 'new';

            if (app(LocalRecipesService::class)->exists($key)) {
                $localRecipe = app(LocalRecipesService::class)->findByKey($key);

                $status = $localRecipe['integrity'] === $remoteRecipe['integrity'] && $localRecipe['version'] === $remoteRecipe['version']
                    ? 'unchanged'
                    : 'updated';
            }

            return [
                'key' => $key,
                'status' => $status,
            ];
        });
    }

    private function handleConflicts(Collection $recipeAnalysis): array
    {
        $this->showConflictSummary($recipeAnalysis);

        $existingRecipes = $recipeAnalysis->whereIn('status', ['unchanged', 'updated']);

        if ($existingRecipes->isEmpty()) {
            return $recipeAnalysis->pluck('key')->toArray();
        }

        return $this->resolveConflictsInteractively($recipeAnalysis);
    }

    private function showConflictSummary(Collection $recipeAnalysis): void
    {
        if ($recipeAnalysis->isEmpty()) {
            return;
        }

        info('Checking for conflicts...');

        $tableData = $recipeAnalysis->map(function ($recipe) {
            $actionText = match ($recipe['status']) {
                'new' => 'Will install',
                'unchanged' => 'No changes',
                'updated' => 'Updated available',
            };

            return [
                $recipe['key'],
                ucfirst($recipe['status']),
                $actionText,
            ];
        })->toArray();

        table(['Recipe', 'Status', 'Action'], $tableData);
    }

    private function resolveConflictsInteractively(Collection $recipeAnalysis): array
    {
        $choice = select(
            label: 'How should I handle existing recipes?',
            options: [
                'skip-unchanged' => 'Skip unchanged recipes (recommended)',
                'overwrite-all' => 'Overwrite all existing recipes',
            ],
            default: 'skip-unchanged'
        );

        return match ($choice) {
            'skip-unchanged' => $recipeAnalysis->whereIn('status', ['new', 'updated'])->pluck('key')->toArray(),
            'overwrite-all' => $recipeAnalysis->pluck('key')->toArray(),
        };
    }

    private function install($key)
    {
        info('Installing recipe: ' . $key);

        $data = app(MiseService::class)->findByKey($key);

        app(LocalRecipesService::class)->install([
            'key' => $key,
            'name' => $data->get('name'),
            'namespace' => $data->get('namespace'),
            'version' => $data->get('version'),
            'url' => $data->get('download_url'),
            'integrity' => $data->get('integrity'),
        ]);
    }
}
