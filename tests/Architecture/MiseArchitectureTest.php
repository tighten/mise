<?php

use Tighten\Mise\Recipes\Recipe;
use Tighten\Mise\Steps\Step;

arch('Recipes')
    ->expect('Tighten\Mise\Recipes')
    ->classes()
    ->toExtend(Recipe::class)
    ->and('Tighten\Mise')
    ->classes()
    ->not->toExtend(Recipe::class)->ignoring(['Tighten\Mise\Recipes']);

arch('Steps')
    ->expect('Tighten\Mise\Steps')
    ->classes()
    ->toExtend(Step::class)
    ->and('Tighten\Mise')
    ->classes()
    ->not->toExtend(Step::class)->ignoring(['Tighten\Mise\Steps']);
