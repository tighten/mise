<?php

namespace Tighten\Mise\Recipes;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Tighten\Mise\Recipes;
use Tighten\Mise\Steps\Step;

use function Laravel\Prompts\warning;

abstract class Recipe
{
    public string $key;

    abstract public function name(): string;

    abstract public function description(): string;

    public function step(string $stepName, ...$params): void
    {
        if (isset($params[0]) && is_callable($params[0])) {
            $this->callableStep($stepName, $params[0]);

            return;
        }

        $step = app($this->resolveStep($stepName));

        warning("Installing: {$step->name()}..");

        ($step)(...$params);
    }

    public function recipe(string $recipeName): void
    {
        (new Recipes)->findByKey($recipeName)();
    }

    public function callableStep(string $stepName, $callback): void
    {
        warning("Installing: {$stepName}..");

        // Create a fake step object, and pass it to the callback
        $callback(app(Step::class));
    }

    public function resolveStep(string $stepName): string
    {
        if (class_exists($stepName)) {
            return $stepName;
        }

        $derivedClass = sprintf('Tighten\\Mise\\Steps\\%s', implode('\\', collect(explode('/', $stepName))->map(fn (string $part) => Str::Pascal($part))->toArray()));
        if (class_exists($derivedClass)) {
            return $derivedClass;
        }

        throw new InvalidArgumentException("Unable to resolve class for '{$stepName}'.");
    }

    public function header(): void
    {
        info('Applying recipe: ' . $this->name());
    }
}
