<?php

namespace App\Tools;

use App\Tools\PhpParser\Visitors\AddImportVisitor;
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
        $stubPath = base_path("stubs/{$stub}");

        if (! file_exists($stubPath)) {
            throw new Exception("Stub {$stub} does not exist.");
        }

        $contents = file_get_contents(base_path("stubs/{$stub}"));

        Storage::put($destination, $contents);

        return $this;
    }

    public function stubAll(string $path): static
    {
        $files = Storage::disk('mise')->allFiles("stubs/{$path}");

        foreach ($files as $file) {
            $this->stub(
                str_replace('stubs/', '', $file),
                str_replace('stubs/' . $path . '/', '', $file)
            );
        }

        return $this;
    }

    public function deleteLinesContaining(string $path, string $content): static
    {
        $lines = explode("\n", Storage::get($path));
        $lines = array_filter($lines, function ($line) use ($content) {
            return ! str_contains($line, $content);
        });
        Storage::put($path, implode("\n", $lines));

        return $this;
    }

    public function replaceLines(string $path, string $search, string $replace, ?int $limit = null): static
    {
        $limitCount = 0;
        $lines = explode("\n", Storage::get($path));
        $lines = array_map(function ($line) use ($search, $replace, &$limitCount, $limit) {
            // If it matches, replace it (as long as we haven't hit the limit); otherwise, return the line unchanged
            if (str_contains($line, $search)) {
                if (! is_null($limit) && $limitCount >= $limit) {
                    return $line;
                }

                $limitCount++;

                $indent = strlen($line) - strlen(ltrim($line));

                return $this->indentAllLines($replace, $indent);
            }

            return $line;
        }, $lines);

        Storage::put($path, implode("\n", $lines));

        return $this;
    }

    public function appendAfterLine(string $path, string $search, string $content, ?int $limit = null): static
    {
        $limitCount = 0;
        $lines = explode("\n", Storage::get($path));
        $lines = array_map(function ($line) use ($search, $content, &$limitCount, $limit) {
            if (str_contains($line, $search)) {
                if (! is_null($limit) && $limitCount >= $limit) {
                    return $line;
                }

                $limitCount++;

                $indent = strlen($line) - strlen(ltrim($line));

                return $line . "\n" . $this->indentAllLines($content, $indent);
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

    public function addImports(string $path, string|array $classes): static
    {
        (new PhpParser)->edit($path, [new AddImportVisitor($classes)]);
        (new CsFixer)->fix($path, ['single_import_per_statement', 'ordered_imports', 'single_line_after_imports']);

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

    protected function indentAllLines(array|string $content, $indentCount): array|string
    {
        $return = is_array($content) ? 'array' : 'string';
        $content = is_array($content) ?: explode("\n", $content);
        $content = array_map(fn ($line) => str_repeat(' ', $indentCount) . $line, $content);

        return $return === 'array' ? $content : implode("\n", $content);
    }
}
