<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;

class File extends ConsoleCommand
{
    public function create(string $path, string $content = ''): static
    {
        Storage::put($path, $content);

        return $this;
    }

    public function append(string $path, string $content): static
    {
        Storage::append($path, $content);

        return $this;
    }

    public function rename(string $oldPath, string $newPath): static
    {
        Storage::move($oldPath, $newPath);

        return $this;
    }

    public function move(string $source, string $destination): static
    {
        Storage::move($source, $destination);

        return $this;
    }

    public function copy(string $source, string $destination): static
    {
        Storage::copy($source, $destination);

        return $this;
    }

    public function delete(string $path): static
    {
        Storage::delete($path);

        return $this;
    }

    public function deleteLinesContaining(string $path, string $content): static
    {
        $lines = explode("\n", Storage::get($path));
        $lines = array_filter($lines, function ($line) use ($content) {
            return strpos($line, $content) === false;
        });
        Storage::put($path, implode("\n", $lines));

        return $this;
    }

    // This could likely be refactored to use Reflection, but it seems unlikely it'll be nearly as elegant;
    // because we're only operating on fresh framework code, I hope we can assume the method signature is well-formed
    public function prependToMethod(string $path, string $method, string $content): static
    {
        $lines = collect(explode("\n", Storage::get($path)));

        $methodStartIndex = $lines->search(fn ($line) => str_contains($line, "function {$method}("));
        $braceIndex = $lines->slice($methodStartIndex)->search(fn ($line) => str_contains($line, '{'));
        $braceIndentation = 4 + strlen($lines->slice($methodStartIndex, $braceIndex)->first()) - strlen(ltrim($lines->slice($methodStartIndex, $braceIndex)->first()));
        $content = collect(explode("\n", $content))->map(fn ($line) => str_repeat(' ', $braceIndentation) . $line);

        $return = $lines->splice(0, $braceIndex + 1)->concat($content)->concat($lines);

        Storage::put($path, $return->join("\n"));

        return $this;
    }

    // @todo: create appendToMethod

    // @todo: Slashes being added to keys in json files, should not be
    public function addToJson(string $path, string $key, string $value): static
    {
        $json = json_decode(Storage::get($path), true);

        $keys = explode('.', $key);
        $current = &$json;

        foreach ($keys as $depth => $segment) {
            if ($depth === count($keys) - 1) {
                $current[$segment] = $value;
            } else {
                if (! isset($current[$segment]) || ! is_array($current[$segment])) {
                    $current[$segment] = [];
                }

                $current = &$current[$segment];
            }
        }

        Storage::put($path, json_encode($json, JSON_PRETTY_PRINT));

        return $this;
    }
}
