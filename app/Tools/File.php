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

    public function deleteLinesContaining(string $path, string $content): void
    {
        $lines = explode("\n", Storage::get($path));
        $lines = array_filter($lines, function ($line) use ($content) {
            return strpos($line, $content) === false;
        });
        Storage::put($path, implode("\n", $lines));
    }

    public function addToMethod(string $path, string $method, string $content): void
    {
        $lines = explode("\n", storage::get($path));
        // @todo: find the opening brace of the method; add the content after it.
        // Un-ideal, but finding the closing brace is likely significantly more complex.
        // simplest solution: assume they're following psr correctly, we can add a new line
        // after the first `{` line after the first line containing "function $method("
        storage::put($path, implode("\n", $lines));
    }

    public function addToJson(string $path, string $key, string $value): void
    {
        $json = json_decode(Storage::get($path), true);

        $keys = explode('.', $key);
        $current = &$json;

        foreach ($keys as $depth => $segment) {
            if ($depth === count($keys) - 1) {
                $current[$segment] = $value;
            } else {
                if (!isset($current[$segment]) || !is_array($current[$segment])) {
                    $current[$segment] = [];
                }

                $current = &$current[$segment];
            }
        }

        Storage::put($path, json_encode($json, JSON_PRETTY_PRINT));
    }
}
