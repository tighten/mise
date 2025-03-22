<?php

namespace App\Steps;

abstract class Step
{
    public $composer;
    public $git;

    // @todo load composer, git, etc.
    public function __construct()
    {
        $this->composer = new class {
            public function requireDev()
            {
                echo 'composer require dev' . PHP_EOL;
                return $this;
            }
        };
        
        $this->git = new class {
            public function add()
            {
                echo 'git add' . PHP_EOL;
                return $this;
            }

            public function commit()
            {
                echo 'git commit' . PHP_EOL;
                return $this;
            }
        };
    }

    public function exec(string $exec)
    {
        echo "DO {$exec}..." . PHP_EOL;
    }
}
