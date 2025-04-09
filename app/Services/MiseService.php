<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class MiseService
{
    protected string $url;

    public function __construct()
    {
        $this->url = config('app.mise-url');
    }

    public function all(): Collection
    {
        $data = Http::get($this->url . '/api/recipes')
            ->throw()
            ->json('data');

        return collect($data);
    }

    public function findByKey(string $recipe): Collection
    {
        $data = Http::get($this->url . '/api/recipes/' . $recipe)
            ->throw()
            ->json('data');

        return collect($data);
    }

    public function allForSelect(): Collection
    {
        return $this->all()->pluck('name', 'key');
    }

    public function keys(): Collection
    {
        return $this->all()->pluck('key');
    }
}
