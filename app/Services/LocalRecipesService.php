<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * {
 *   recipes: { key: string, "name": "name", version: string, hash: string, source: { url: string }  }[]
 * }
 */
class LocalRecipesService
{
    const Lock = 'mise-lock.json';
    const Disk = 'local-recipes';

    public function install($url): void
    {
        // Pull Zip file from remote Mise service
        // Integrity check
        // Extract to local recipes folder
        // Update lock file
        //

        // Storage::drive('local-recipes')->put($data['class'] . '.php', $data['file']);

        // // Save metadata for the main recipe
        // $metadata->setRecipeMetadata($key, [
        //     'recipe_class' => $data['class'],
        //     'local_hash' => $data['file_hash'],
        //     'remote_hash' => $data['file_hash'],
        //     'version' => $data['version'] ?? null,
        // ]);

        // collect($data['steps'])->each(function ($step) {
        //     Storage::drive('local-recipes')->put($step['class'] . '.php', $step['file']);
        // });
    }

    public function all(): Collection
    {
        // Get lock file
        // Iterate over each recipe
        // Return array of recipes

        if (! $this->lockExists()) {
            return collect();
        }

        $content = Storage::disk(self::Disk)->get(self::Lock);

        return collect(json_decode($content, true)['recipes'] ?? []);
    }

    public function findByKey(string $key): ?Collection
    {
        return $this->all()->first(fn ($recipe) => $recipe['key'] === $key);
    }

    public function exists(string $key): bool
    {
        return $this->all()->contains('key', $key);
    }

    private function lockExists(): bool
    {
        return Storage::disk(self::Disk)->exists(self::Lock);
    }

    private function validateIntegrity(string $content): string
    {
        return hash('sha256', $content);
    }

    private function updateLock(string $key, array $data) {}
}
