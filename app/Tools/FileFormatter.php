<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;

class FileFormatter extends ConsoleCommand
{
    private array $rules = [];

    private string $file;

    public function __construct() {}

    public function file(string $path): static
    {
        $this->file = Storage::path($path);

        return $this;
    }

    public function importOrder(): FileFormatter
    {
        $this->rules('ordered_imports');

        return $this;
    }

    public function arraySyntax(): FileFormatter
    {
        $this->rules('array_syntax');

        return $this;
    }

    public function rules(string|array $rules): FileFormatter
    {
        is_string($rules)
            ? $this->rules[] = $rules
            : $this->rules =  array_merge($rules, $this->rules);
        return $this;
    }

    public function fix(): void
    {
        $this->exec(sprintf(
            '%s fix %s --rules=%s',
            base_path('vendor/bin/php-cs-fixer'),
            $this->file,
            implode(',', $this->rules),
        ));
    }
}
