<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * {
 *   recipes: { key: string, "name": "name", version: string, integrity: string, source: { url: string }  }[]
 * }
 */
class LocalRecipesService
{
    const Lock = 'mise-lock.json';
    const Disk = 'local-recipes';

    public function install($package): void
    {
        $response = Http::get($package['url'])->throw();
        $zipContent = $response->body();

        if ($this->validateIntegrity($zipContent) !== $package['integrity']) {
            throw new Exception('Integrity verification failed. Downloaded file integrity check failed.');
        }

        $tempZipName = 'temp_recipe_' . time() . '_' . random_int(1000, 9999) . '.zip';
        $tempDisk = Storage::disk('local');
        $tempDisk->put($tempZipName, $zipContent);
        $tempZipPath = $tempDisk->path($tempZipName);

        $zip = new ZipArchive;
        if ($zip->open($tempZipPath) !== true) {
            $tempDisk->delete($tempZipName);
            throw new Exception('Failed to open zip file.');
        }

        $disk = Storage::disk(self::Disk);
        $extractPath = $disk->path($package['namespace']);

        if (! $zip->extractTo($extractPath)) {
            $zip->close();
            $tempDisk->delete($tempZipName);
            throw new Exception('Failed to extract zip file.');
        }

        $zip->close();
        $tempDisk->delete($tempZipName);

        $this->updateLock($package['key'], [
            'name' => $package['name'],
            'version' => $package['version'],
            'integrity' => $package['integrity'],
            'source' => ['url' => $package['url']],
        ]);
    }

    public function all(): Collection
    {
        if (! $this->lockExists()) {
            return collect();
        }

        $content = Storage::disk(self::Disk)->get(self::Lock);

        return collect(json_decode($content, true)['recipes'] ?? []);
    }

    public function findByKey(string $key): ?array
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
        return hash('sha512', $content);
    }

    private function updateLock(string $key, array $data): void
    {
        $lockData = $this->lockExists() ? json_decode(Storage::disk(self::Disk)->get(self::Lock), true) : ['recipes' => []];

        $existingIndex = collect($lockData['recipes'])->search(fn ($recipe) => $recipe['key'] === $key);

        if (is_int($existingIndex)) {
            $lockData['recipes'][$existingIndex] = array_merge(['key' => $key], $data);
        } else {
            $lockData['recipes'][] = array_merge(['key' => $key], $data);
        }

        Storage::disk(self::Disk)->put(self::Lock, json_encode($lockData, JSON_PRETTY_PRINT));
    }
}
