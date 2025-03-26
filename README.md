# Mise ("meez")

> [!WARNING]  
> This tool is in alpha.

A CLI tool to automatically apply preset steps to (likely, but not necessarily, new) Laravel applications.

Using concepts like composable recipes built from individual steps.

Spiritual successor to [Lambo's presets](https://github.com/tighten/lambo/pull/185).

One common use case could be for a starter kit creator to automate building new versions of their starter kit; every time Laravel releases a new version, you can build again by running a new Laravel install and then running your Mise steps.

I also wonder whether we could distribute Mise recipes as standalone configurations--almost like "here's your starter kit, a Mise config file".

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

### How works

Mise comes with predefined "steps"; for example, a step named `duster/install` takes the following steps:

- `composer require-dev tightenco/duster`
- Run `./vendor/bin/duster github-actions`
- Run `git add . && git commit -m "Install Duster"`
- Run `./vendor/bin/duster fix`
- Run `git add . && git commit -m "Run Duster"`

Recipes can include any steps they want.

### How recipes are defined/loaded

Recipes are defined in the Mise codebase for now. In the future, you'll be able to have your own local recipes, and also pull them from a Mise SaaS.

Also maybe some useful thing where you can set a configuration item so if you run `mise default` or something, it'll run a predefined set of recipes, so you can say "all my new Laravel apps should have these three recipes run" as your default.

### What a step looks like

OK, so I'm imagining a step is single file (?), either a PHP class or a procedural PHP file. It *could* be yaml, which would be cleaner, but wouldn't allow for arbitrary PHP. Maybe allow both?

Let's imagine `duster` is a step. Maybe this? Will keep working on the API...

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

## Reference

- Valet
- Lambo, especially this PR: https://github.com/tighten/lambo/pull/185
- Josh Manders announced a tool [Skeletor](https://github.com/aniftyco/skeletor) while I was plotting this; his tool is primarily hooking around `Composer` so I'll consider it a cousin :)
