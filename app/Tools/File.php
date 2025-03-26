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

    // This could likely be refactored to use Reflection, but it seems unlikely it'll be nearly as elegant;
    // because we're only operating on fresh framework code, I hope we can assume the method signature is well-formed
    public function prependToMethod(string $path, string $method, string $content): void
    {
        $lines = collect(explode("\n", Storage::get($path)));

        $methodStartIndex = $lines->search(fn($line) => str_contains($line, "function $method("));
        $braceIndex = $lines->slice($methodStartIndex)->search(fn($line) => str_contains($line, '{'));
        $braceIndentation = 4 + strlen($lines->slice($methodStartIndex, $braceIndex)->first()) - strlen(ltrim($lines->slice($methodStartIndex, $braceIndex)->first()));
        $content = collect(explode("\n", $content))->map(fn($line) => str_repeat(' ', $braceIndentation) . $line);

        $return = $lines->splice(0, $braceIndex + 1)->concat($content)->concat($lines);

        Storage::put($path, $return->join("\n"));
    }

    // @todo: create appendToMethod

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
