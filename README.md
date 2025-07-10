![Mise](mise-banner.png?version=1)

# Mise ("meez")

> [!WARNING]  
> This tool is in alpha.

A CLI tool to automatically apply preset steps to new Laravel applications, using concepts like composable recipes built
from individual steps.

**Use cases:**

- Individual dev runs pre-made Mise recipes to kick off their app development
- Individual dev or agency creates a recipe they use for all their new apps, and applies with Mise
- Starter kit creator distributes their kit as a Mise recipe
- Starter kit creator automates the building of their new repo-based (`laravel new` style) starter kit using a Mise
  recipe that runs and pushes afresh every time there's a new version of Laravel available

## Usage

Once Mise is installed globally with Composer, you'll install a new Laravel app:

### Usage

```bash
laravel new money-maker
cd money-maker
```

Then you can use Mise to apply recipes:

```bash
mise apply preset1 preset2 preset3
```

Or you can use it interactively, where Prompts will let you choose which you want to apply:

```bash
mise apply
```

### How it works

Mise comes with predefined "steps"; for example, a step named `duster/install` takes the following steps:

- `composer require-dev tightenco/duster`
- `git add . && git commit -m "Install Duster"`
- `./vendor/bin/duster fix`
- `git add . && git commit -m "Run Duster"`

Recipes are a list of steps, along with optional conditional logic around which steps to run.

### How recipes are defined/loaded

Recipes are defined in the Mise codebase for now. In the future, you'll be able to have your own local recipes, and also
pull them from a Mise SaaS.

We're also considering allowing you to set a default recipe to run, so you can maybe run `mise default` on every new
project.

### What a step looks like

Steps are individual PHP files. Here's what the above Duster install step looks like:

```php
namespace Steps/Duster;

class Install extends Step
{
    public function __invoke()
    {
        $this->composer->requireDev('tightenco/duster');
        $this->git->addAndCommit('Install Duster');
        $this->vendorExec('duster fix');
        $this->git->addAndCommit('Run Duster');
    }
}
```

We're working on building even more tooling to make common startup steps easy.

### What a recipe looks like

Let's imagine we have a recipe for creating a new Tighten SaaS. What steps do we want to take after `laravel new`?

```php
class Tighten extends Recipe
{
    public function __invoke()
    {
        $this->step('duster/install');
        $this->step('duster/ci');
    }
}
```

We can also take user input:

```php
class Tighten extends Recipe
{
    public function __invoke()
    {
        $this->step('duster/install');
        $this->step('duster/ci');

        if (confirm(label: 'Do you want to install our frontend tooling?')) {
            $this->step('tighten/prettier');
        }
    }
}
```

### How to create a custom recipe

If you'd like to build your own recipe, you can!

Build a class that extends `Tighten\Mise\Recipe` and place it in `~/.mise/Recipes`. It'll just show up!

Here's an example:

```php
// ~/.mise/Recipes/EchoDate.php
<?php

namespace Tighten\Mise\Recipes;

class EchoDate extends Recipe
{
    public string $key = 'echo-date';

    public function __invoke(): void
    {
        echo "Today's date is " . date('Y-m-j');
    }

    public function description(): string
    {
        return 'Echo the date.';
    }

    public function name(): string
    {
        return 'Echo date';
    }
}
```

## Reference

- Valet
- Lambo, especially this PR: https://github.com/tighten/lambo/pull/185
- Josh Manders announced a tool [Skeletor](https://github.com/aniftyco/skeletor) while I was plotting this; his tool is
  primarily hooking around `Composer` so I'll consider it a cousin :)
