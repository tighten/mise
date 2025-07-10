<?php

namespace Tighten\Mise\Tools;

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
        $dependencies = $this->developmentDependencies();

        return isset($dependencies[$package]);
    }

    public function hasDependency(string $package): bool
    {
        $dependencies = array_merge(
            $this->productionDependencies(),
            $this->developmentDependencies(),
        );

        return isset($dependencies[$package]);
    }

    public function hasProductionDependency(string $package): bool
    {
        $dependencies = $this->productionDependencies();

        return isset($dependencies[$package]);
    }

    public function developmentDependencies(): mixed
    {
        return $this->composerConfiguration('require-dev');
    }

    public function productionDependencies(): mixed
    {
        return $this->composerConfiguration('require');
    }

    public function composerConfiguration(?string $section = null): array
    {
        $configuration = json_decode(Storage::get('composer.json'), true);

        return $section ? $configuration[$section] : $configuration;
    }
}
