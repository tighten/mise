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

    public function delete(array|string $path): static
    {
        return $this->globEach($path, fn ($file) => Storage::delete($file));
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

    public function addToJson(string $path, string $key, string|array $value): static
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

        Storage::put($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $this;
    }

    /**
     * Allow passing globbing patterns or arrays of globbing patterns.
     */
    protected function globEach(array|string $path, callable $callback): static
    {
        $path = is_array($path) ? $path : [$path];

        foreach ($path as $eachPath) {
            // Checking for glob-targeted strings; we may have to expand this to support more complex glob patterns
            if (str_contains($eachPath, '*')) {
                foreach (glob($eachPath) as $file) {
                    $callback($file);
                }
            } else {
                $callback($eachPath);
            }
        }

        return $this;
    }
}
