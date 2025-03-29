<?php

declare(strict_types=1);

namespace App\Steps\Laravel;

use App\Steps\Step;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;

class InstallBreeze extends Step
{
    public function __invoke(): void
    {
        $stack = select('Which Breeze stack would you like to install?', [
            'blade' => 'Blade',
            'livewire' => 'Livewire',
            'livewire-functional' => 'Livewire Functional',
            'react' => 'React',
            'vue' => 'Vue',
            'api' => 'Api',
        ]);
        $options = '';
        if (in_array($stack, ['react', 'vue'])) {
            $options .= confirm('Would you like to use Inertia SSR support?', false) ? ' --ssr' : '';
            $options .= confirm('Would you like to use TypeScript with Inertia?', false) ? ' --typescript' : '';
        }
        $options .= confirm('Would you like to use ESLint and Prettier to lint your code?', false) ? ' --eslint' : '';
        $options .= confirm('Would you like dark mode support?', false) ? ' --dark' : '';
        $options .= confirm('Would you like to use the Pest testing framework?', false) ? ' --pest' : '';

        $progress = progress(label: 'Composer require Breeze', steps: 3);
        $progress->start();
        spin(
            callback: fn () => $this->composer->requireDev('laravel/breeze'),
            message: 'composer require laravel/breeze --dev'
        );
        $progress->advance();

        $progress->label('Installing Breeze with configured options');
        spin(
            callback: fn () => $this->artisan->runCustom("breeze:install{$options} -- {$stack}"),
            message: "php artisan breeze:install{$options} -- {$stack}"
        );
        $progress->advance();

        $progress->label('Run migrations');
        spin(
            callback: fn () => $this->artisan->migrate(),
            message: 'php artisan migrate'
        );
        $progress->advance();

        $progress->label('Commit changes');
        spin(
            callback: fn () => $this->git->addAndCommit('Install Laravel Breeze'),
            message: 'git add . && git commit -m "Install Laravel Breeze"'
        );
        $progress->advance();
        $progress->finish();
    }

    public function name(): string
    {
        return 'Laravel Breeze';
    }
}
