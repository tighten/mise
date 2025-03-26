<?php

namespace App\Steps\Tighten;

use App\Steps\Step;

class Prettier extends Step
{
    public function name(): string
    {
        return 'Set up Prettier with Tailwind class sorting';
    }

    public function run(): void
    {
        // Install required npm packages
        $this->npm->saveDev('prettier prettier-plugin-tailwindcss');

        // Create .prettierrc file
        $this->file->create('.prettierrc', '{"plugins": ["prettier-plugin-tailwindcss"], "semi": true, "singleQuote": true, "tabWidth": 4, "trailingCommaPHP": true}');

        // Create .prettierignore file
        $this->file->create('.prettierignore', "node_modules\nvendor\nstorage\nbootstrap/cache\npublic\n");

        // Add npm scripts to package.json
        $this->npm->addScript('format', 'prettier --write resources/');

        // Format all files
        // $this->npm->run('format');
    }
}
