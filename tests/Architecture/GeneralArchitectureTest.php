<?php

arch()->preset()->php();
arch()->preset()->security();
arch()->preset()->zero();

expect('App')
    ->toUseStrictEquality()
    ->not->toUse(['sleep', 'usleep']);
