<?php

namespace App\Tools;

class Npm extends ConsoleCommand
{
    public function install(): void
    {
        $this->exec('npm install');
    }

    public function saveDev(string $package): void
    {
        $this->exec("npm install {$package} --save-dev");
    }

    public function save(string $package): void
    {
        $this->exec("npm install {$package} --save");
    }

    public function addScript(string $name, string $command): void
    {
        $this->exec("npm pkg set scripts.{$name}=\"{$command}\"");
    }

    public function run(string $script): void
    {
        $this->exec("npm run {$script}");
    }

    public function ci(): void
    {
        $this->exec('npm ci');
    }

    public function update(): void
    {
        $this->exec('npm update');
    }
}
