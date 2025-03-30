<?php

namespace App\Recipes;

use App\Recipes;
use App\Steps\Step;
use Illuminate\Support\Str;
use InvalidArgumentException;

use function Laravel\Prompts\warning;

abstract class Recipe
{
    abstract public function name(): string;
    abstract public function description(): string;

    public string $slug;

    public function step(string $stepName, array|callable $params = []): void
    {
        if (is_callable($params)) {
            $this->callableStep($stepName, $params);
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

        $derivedClass = sprintf('App\\Steps\\%s', implode('\\', collect(explode('/', $stepName))->map(fn (string $part) => Str::Pascal($part))->toArray()));
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
