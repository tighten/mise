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

    public function stubAll(string $path): static
    {
        $files = glob(base_path("stubs/{$path}/*"));

        foreach ($files as $file) {
            $this->stub($path . '/' . $file, $file);
        }

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

    // @todo: Add limit, so they could say "only the first occurrence"?
    public function appendAfterLine(string $path, string $search, string $content): static
    {
        $lines = explode("\n", Storage::get($path));
        $lines = array_map(function ($line) use ($search, $content) {
            if (str_contains($line, $search)) {
                return $line . "\n" . $content;
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

    // @todo: If we're going to do it like this, we probably need to be able to run some form of sorter after. It doesn't sort imports.
    // @todo: This is not a good enough solution long term (e.g. doesn't handle traits, interfaces, etc.)
    public function addImport(string $path, string $class): static
    {
        $useString = "use $class;\n";

        if (str_contains($contents = Storage::get($path), $useString)) {
            return $this;
        }

        $contents = explode("\n", $contents);

        // Find the first line that starts with `class`
        $classIndex = array_search(
            'class',
            array_map(fn ($line) => str_starts_with($line, 'class') ? substr($line, 0, 5) : null, $contents)
        );

        if ($classIndex === false) {
            throw new Exception("Class keyword not found in {$path}");
        }

        // Insert the use statement just before the class definition
        $newContents = array_merge(
            array_slice($contents, 0, $classIndex),
            [$useString],
            array_slice($contents, $classIndex)
        );

        if (! $newContents[$classIndex - 1] && str_starts_with($newContents[$classIndex - 2], 'use')) {
            // Remove line above this import entirely... but only if this is the only import
            unset($newContents[$classIndex - 1]);
        }

        Storage::put($path, implode("\n", $newContents));

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
