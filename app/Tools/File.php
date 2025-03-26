<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;

class File extends ConsoleCommand
{
    public function create(string $path, string $content = ''): void
    {
        Storage::put($path, $content);
    }

    public function append(string $path, string $content): void
    {
        Storage::append($path, $content);
    }

    public function rename(string $oldPath, string $newPath): void
    {
        Storage::move($oldPath, $newPath);
    }

    public function move(string $source, string $destination): void
    {
        Storage::move($source, $destination);
    }

    public function copy(string $source, string $destination): void
    {
        Storage::copy($source, $destination);
    }

    public function delete(string $path): void
    {
        Storage::delete($path);
    }
}
