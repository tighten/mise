# Mise ("meez")

A CLI tool to automatically apply preset steps to (likely, but not necessarily, new) Laravel applications.

Using concepts like composable recipes built from individual steps.

Spiritual successor to [Lambo's presets](https://github.com/tighten/lambo/pull/185).

One common use case could be for a starter kit creator to automate building new versions of their starter kit; every time Laravel releases a new version, you can build again by running a new Laravel install and then running your Mise steps.

I also wonder whether we could distribute Mise presets as standalone configurations--almost like "here's your starter kit, a Mise config file".

## Vision

I imagine it'll look like this. Once Mise is installed globally with Composer, you'll install a new Laravel app:

### Usage

```bash
laravel new money-maker
cd money-maker
```

Then you can use Mise to apply presets:

```bash
mise apply preset1 preset2 preset3
```

Or you can use it interactively, where Prompts will let you choose which you want to apply:

```bash
mise apply
```

### How it would work

I can imagine Mise comes with predefined "steps"; for example, a step named "duster" could do the following:

- `composer rqeuire-dev tightenco/duster`
- Run `./vendor/bin/duster github-actions`
- Run `git add . && git commit -m "Install Duster"`
- Run `./vendor/bin/duster fix`
- Run `git add . && git commit -m "Run Duster"`

The definition for this step would be built into code in Mise, using some combination of convenience helpers that make common tasks (e.g. "git commit with message", "composer require dev") easy; then presets would include this step.

### How presets/recipes would be defined/loaded

Presets would be defined... that's the hard part. I know some can be defined in Mise. And I know some can be defined locally. But what is a safe way to define presets to be shared?

- Composer is too heavy, I think, unless one person/group wanted to release a whole pack of steps/recipes
- Pulling from gists makes it too easy for someone just to update it to something nefarious and then you're running untested bash scripts on your machine
- Clumsiest but safest is to just have a central site where you can share presets but you have to manually copy to them to your local machine

Also maybe some useful thing where you can set a configuration item so if you run `mise default` or something, it'll run a predefined set of presets, so you can say "all my new Laravel apps should have these three presets run" as your default.

### Building a step

OK, so I'm imagining a step is single file (?), either a PHP class or a procedural PHP file. It *could* be yaml, which would be cleaner, but wouldn't allow for arbitrary PHP. Maybe allow both?

Let's imagine `duster` is a step. Maybe this? Will keep working on the API...

```php
namespace Steps/Duster;

class Install extends Step
{
    public function __invoke()
    {
        $this->composer->requireDev('tightenco/duster');
        $this->git->add('.')->commit('Install Duster');
        // or $this->git->addAndCommit('Install Duster'), not sure
        $this->exec('./vendor/bin/duster fix');
        $this->git->add('.')->commit('Run Duster');
    }
}
```

I can imagine we'll want tooling to create files, replace content in files, rename or move files. More Git tooling, more Composer tooling, NPM tooling.

Some of this will come from having a `filesystem` component directly available, and, of course, access to the entire Laravel world through the container. Some it'd be nice to have one-off commands or even little suites of tools (e.g this `git` helper described above) to simplify some of the steps. I'll be looking to Lambo and Valet for at least some inspiration on those.

### Building a recipe/preset

Let's imagine we have a preset for creating a new Tighten SaaS. What steps do we want to take after `laravel new`?

We could have it be a simple YML/JSON file... just with a list of steps... or it can be a PHP file so it can have standalone work outside of steps, or pass configuration items to steps?

```php
class Tighten extends Recipe
{
    public function __invoke()
    {
        $this->step('duster/install');
        $this->step('duster/ci', someParameterHereOrWhatever: true);
    }
}
```

I don't know if I want any steps to take user input, but if they can, doing it as a class would help that:

```php
class Tighten extends Recipe
{
    public function __invoke()
    {
        $this->step('duster/install');
        $this->step('duster/ci', someParameterHereOrWhatever: true);

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
