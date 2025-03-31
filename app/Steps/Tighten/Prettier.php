<?php

namespace App\Steps\Tighten;

use App\Steps\Step;

class Prettier extends Step
{
    public function __invoke(): void
    {
        $this->npm->saveDev('prettier prettier-plugin-tailwindcss');

        $this->file->create('.prettierrc', json_encode([
            'plugins' => ['prettier-plugin-tailwindcss'],
            'semi' => true,
            'singleQuote' => true,
            'tabWidth' => 4,
            'trailingCommaPHP' => true,
        ], JSON_PRETTY_PRINT));

        $this->file->create('.prettierignore', "node_modules\nvendor\nstorage\nbootstrap/cache\npublic\n");
        $this->npm->addScript('format', 'prettier --write resources/');
        $this->npm->run('format');
        $this->git->addAndCommit('Install and run Prettier with Tailwind class sorting');
    }

    public function name(): string
    {
        return 'Install and run Prettier with Tailwind class sorting';
    }
}
