<?php

namespace App\Commands;

use App\Recipes;
use LaravelZero\Framework\Commands\Command;

class ListRecipesCommand extends Command
{
    protected $signature = 'list:recipes';

    protected $description = 'List the available recipes';

    public function handle(): void
    {
        $this->newLine();
        $this->line('Mise <info>v' . config('app.version') . '</info> ');
        $this->newLine();
        $this->line('<fg=yellow>Recipes:</>');
        $this->line($this->recipes());
        $this->line('<fg=yellow>Applying recipes:</>');
        $this->line('    mise apply [recipes]');
        $this->newLine();
        $this->line('<fg=yellow>Examples:</>');
        $this->line('    mise apply tighten-basic-saas');
        $this->line('    mise apply tighten-basic-saas laravel-local-developer-tooling');
    }

    private function recipes(): string
    {
        $recipes = (new Recipes)->all();
        $padding = $recipes->keys()->max(fn ($recipe) => strlen($recipe) + 4);

        return $recipes->reduce(
            fn ($carry, $recipeClass, $key) => sprintf("%s  <info>%s</info> %s\n", $carry, sprintf("%-{$padding}s", $key), app($recipeClass)->description())
        );
    }
}
