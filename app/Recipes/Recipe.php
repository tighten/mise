<?php

namespace App\Recipes;

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use InvalidArgumentException;

use function Laravel\Prompts\warning;

abstract class Recipe
{
    abstract public function name(): string;

    // @todo: Test this
    public function step(string $stepName, ...$params): void
    {
        $step = app($this->resolveClass($stepName));

        warning("Installing: {$step->name()}..");
        Context::push('steps', $step::class);

        ($step)(...$params);
    }

    public function description(): string
    {
        return "{$this->name()}";
    }

    public function header(): void
    {
        info('Applying recipe: ' . $this->name());
    }

    private function resolveClass(string $stepName): string
    {
        if (class_exists($stepName)) {
            return $stepName;
        }

        // @todo test this
        $derivedClass = sprintf('App\\Steps\\%s', implode('\\', collect(explode('/', $stepName))->map(fn (string $part) => Str::title($part))->toArray()));

        if (class_exists($derivedClass)) {
            return $derivedClass;
        }

        throw new InvalidArgumentException("Unable to resolve class for '{$stepName}'.");
    }
}
