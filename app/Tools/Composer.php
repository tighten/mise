<?php

namespace App\Tools;

use Illuminate\Support\Facades\Storage;

class Composer extends ConsoleCommand
{
    public function requireDev(string $package): static
    {
        $this->exec("composer require {$package} --dev");

        return $this;
    }

    public function require(string $package): static
    {
        $this->exec("composer require {$package}");

        return $this;
    }

    public function remove(string $package): static
    {
        $this->exec("composer remove {$package}");

        return $this;
    }

    public function removeDev(string $string): static
    {
        $this->exec("composer remove {$string} --dev");

        return $this;
    }

    public function hasDevDependency(string $package): bool
    {
        $dependencies = $this->getDevelopmentDependencies();

        return isset($dependencies[$package]);
    }

    public function hasDependency(string $package): bool
    {
        $dependencies = array_merge(
            $this->getProductionDependencies(),
            $this->getDevelopmentDependencies(),
        );

        return isset($dependencies[$package]);
    }

    public function hasProductionDependency(string $package): bool
    {
        $dependencies = $this->getProductionDependencies();

        return isset($dependencies[$package]);
    }

    public function getDevelopmentDependencies(): mixed
    {
        return $this->getComposerConfiguration()['require-dev'];
    }

    public function getProductionDependencies(): mixed
    {
        return $this->getComposerConfiguration()['require'];
    }

    public function getComposerConfiguration(): array
    {
        return json_decode(Storage::get('composer.json'), true);
    }
}
