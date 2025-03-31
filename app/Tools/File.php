<?php

namespace App\Tools;

use Exception;
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

    public function stub(string $stub, string $destination): static
    {
        if (! file_exists(base_path("stubs/{$stub}"))) {
            throw new Exception("Stub {$stub} does not exist.");
        }

        $contents = file_get_contents(base_path("stubs/{$stub}"));

        Storage::put($destination, $contents);

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

    // @todo: Add limit
    // @todo: Match indentation
    public function replaceLines(string $path, string $search, string $replace): static
    {
        $lines = explode("\n", Storage::get($path));
        $lines = array_map(function ($line) use ($search, $replace) {
            // If it matches, replace it; otherwise, return the line unchanged
            if (str_contains($line, $search)) {
                return $replace;
            }

            return $line;
        }, $lines);

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

    // @todo: Can we just add it, and then rely on code tooling to re-sort? We don't currently do that, but it could make some coding easier, both this and indentation and other things.
    public function addUse(string $path, string $class): static
    {
        // @todo
        // If we don't worry about sorting, we can:
        // A. Build the string for the import
        // B. Ensure that string doesn't already exist in the file
        // C. Ensure a competing import doesn't already exist; if it does... i feel like building the aliased version will be unhelpful, so maybe just throw an error?
        // D. Append the import to the file, maybe just one line above the Class/whatever definition?

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
