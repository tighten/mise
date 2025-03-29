<?php

namespace App\Recipes;

use Illuminate\Support\Str;
use InvalidArgumentException;

use function Laravel\Prompts\warning;

abstract class Recipe
{
    public static $slug;

    abstract public function name(): string;

    public function step(string $stepName, ...$params): void
    {
        $step = app($this->resolveStep($stepName));

        warning("Installing: {$step->name()}..");

        ($step)(...$params);
    }

    abstract public function description(): string;

    public function header(): void
    {
        info('Applying recipe: ' . $this->name());
    }

    public function resolveStep(string $stepName): string
    {
        if (class_exists($stepName)) {
            return $stepName;
        }

        $derivedClass = sprintf('App\\Steps\\%s', implode('\\', collect(explode('/', $stepName))->map(fn (string $part) => Str::Pascal($part))->toArray()));
        if (class_exists($derivedClass)) {
            return $derivedClass;
        }

        throw new InvalidArgumentException("Unable to resolve class for '{$stepName}'.");
    }
}
