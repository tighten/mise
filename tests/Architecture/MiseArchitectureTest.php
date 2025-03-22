<?php

declare(strict_types=1);

use App\Recipes\Recipe;
use App\Steps\Step;

arch('Recipes')
    ->expect('App\Recipes')
    ->classes()
    ->toExtend(Recipe::class)
    ->and('App')
    ->classes()
    ->not->toExtend(Recipe::class)->ignoring(['App\Recipes']);

arch('Steps')
    ->expect('App\Steps')
    ->classes()
    ->toExtend(Step::class)
    ->and('App')
    ->classes()
    ->not->toExtend(Step::class)->ignoring(['App\Steps']);
