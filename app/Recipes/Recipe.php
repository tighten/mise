<?php

namespace App\Recipes;

use App\Steps\Step;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\warning;

abstract class Recipe
{
    public function step(string $stepName, ...$params): void
    {
        /** @var Step $step */
        $step = app($this->resolveClass($stepName));
        warning("Installing: {$step->name()}..");
        Context::push('steps', $step::class);
        ($step)();
    }

    public function confirm(string $label, bool $default = true): bool
    {
        return confirm($label, default: $default);
    }

    public function configure(): void {}

    abstract public function name(): string;

    abstract public function vendor(): string;

    private function resolveClass(string $stepName): string
    {
        if (class_exists($stepName)) {
            return $stepName;
        }
        $derivedClass = sprintf('App\\Steps\\%s', implode('\\', collect(explode('/', $stepName))->map(fn (string $part) => Str::title($part))->toArray()));
        if (class_exists($derivedClass)) {
            return $derivedClass;
        }

        throw new \InvalidArgumentException("Unable to resolve class for '{$stepName}'.");
    }

    public function description(): string
    {
        return "{$this->name()} by {$this->vendor()}";
    }
}
