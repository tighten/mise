<?php

namespace Tighten\Mise\Tools;

use Illuminate\Support\Facades\Storage;

class CsFixer extends ConsoleCommand
{
    public function fix($path, string|array $rules): void
    {
        $file = Storage::path($path);

        $rulesList = implode(',', is_string($rules) ? [$rules] : $rules);

        $this->exec(sprintf(
            '%s fix %s --rules=%s',
            base_path('vendor/bin/php-cs-fixer'),
            $file,
            $rulesList,
        ));
    }
}
