<?php

declare(strict_types=1);

namespace App\Prompts;

enum Target: string
{
    case Step = 'Step';
    case Recipe = 'Recipe';
}
