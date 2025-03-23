<?php

declare(strict_types=1);

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class ListRecipeCommand extends Command
{
    protected $signature = 'list:recipes';

    protected $description = 'List the available recipes';

    public function handle(): void
    {
        $recipes = collect(config('mise.recipes'));

        $padding = $recipes->keys()->max(fn ($recipe) => strlen($recipe) + 4);

        $recipeList = $recipes->reduce(function (string $carry, string $recipeClass, $key) use ($padding) {
            $recipe = app($recipeClass);

            return sprintf($carry . "  <info>%-{$padding}s</info> %s\n", $key, $recipe->description());
        }, '');

        $this->newLine();
        $this->line(sprintf('Mise <info>v%s</info>', config('app.version')));
        $this->newLine();
        $this->line('<fg=yellow>Recipies:</>');
        $this->line($recipeList);
        $this->line('<fg=yellow>Usage:</>');
        $this->line('    mise apply [recipies]');
        $this->newLine();
        $this->line('<fg=yellow>Examples:</>');
        $this->line('    mise apply tighten/basic-sass');
        $this->line('    mise apply tighten/basic-sass laravel/local-developer-tooling');
    }
}
