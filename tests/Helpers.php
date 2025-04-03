<?php

declare(strict_types=1);


use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * @throws FileNotFoundException
 */
function createTestFile(string $name, string $fixtureFile): string
{
    Storage::put($name, File::get(base_path($fixtureFile)));
}
