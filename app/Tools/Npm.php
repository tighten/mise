<?php

namespace Tighten\Mise\Tools;

class Npm extends ConsoleCommand
{
    public function install(): static
    {
        $this->exec('npm install');

        return $this;
    }

    public function saveDev(string $package): static
    {
        $this->exec("npm install {$package} --save-dev");

        return $this;
    }

    public function save(string $package): static
    {
        $this->exec("npm install {$package} --save");

        return $this;
    }

    public function addScript(string $name, string $command): static
    {
        $this->exec("npm pkg set scripts.{$name}=\"{$command}\"");

        return $this;
    }

    public function run(string $script): static
    {
        $this->exec("npm run {$script}");

        return $this;
    }

    public function ci(): static
    {
        $this->exec('npm ci');

        return $this;
    }

    public function update(): static
    {
        $this->exec('npm update');

        return $this;
    }
}
