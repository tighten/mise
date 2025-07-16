<?php

arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->zero();

expect('Tighten\Mise')
    ->toUseStrictEquality()
    ->not->toUse(['sleep', 'usleep']);
