<?php

namespace App\Recipes;

abstract class Recipe
{
    public function step(string $stepName, ...$params)
    {
        // @todo Convert string step names, build step, and pass params
        echo "Perform {$stepName}.." . PHP_EOL;
    }

    public function confirm(string $label, bool $default = true)
    {
        echo "Conform: {$label}? ";
        echo $default ? '[Y\n]' : '[y\N]';
        echo PHP_EOL;
    }
}
