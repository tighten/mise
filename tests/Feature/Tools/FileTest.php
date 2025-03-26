<?php

use App\Tools\File;
use Illuminate\Support\Facades\Storage;
use Laravel\Prompts\Prompt;

beforeEach(function () {
    Prompt::fake();
    Storage::fake();
});

test('file->create(...)', function () {
    $path = 'test.txt';

    (new File)->create($path, 'Hello World');

    expect(Storage::exists($path))->toBeTrue()
        ->and(Storage::get($path))->toBe('Hello World');
});

test('file->append(...)', function () {
    $path = 'test.txt';
    Storage::put($path, 'Hello');

    (new File)->append($path, 'World');

    expect(Storage::get($path))->toBe("Hello\nWorld");
});

test('file->rename(...)', function () {
    $oldPath = 'old.txt';
    $newPath = 'new.txt';
    Storage::put($oldPath, 'Hello World');

    (new File)->rename($oldPath, $newPath);

    expect(Storage::exists($oldPath))->toBeFalse()
        ->and(Storage::exists($newPath))->toBeTrue()
        ->and(Storage::get($newPath))->toBe('Hello World');
});

test('file->move(...)', function () {
    $source = 'source.txt';
    $destination = 'destination.txt';
    Storage::put($source, 'Hello World');

    (new File)->move($source, $destination);

    expect(Storage::exists($source))->toBeFalse()
        ->and(Storage::exists($destination))->toBeTrue()
        ->and(Storage::get($destination))->toBe('Hello World');
});

test('file->copy(...)', function () {
    $source = 'source.txt';
    $destination = 'destination.txt';
    Storage::put($source, 'Hello World');

    (new File)->copy($source, $destination);

    expect(Storage::exists($source))->toBeTrue()
        ->and(Storage::exists($destination))->toBeTrue()
        ->and(Storage::get($source))->toBe('Hello World')
        ->and(Storage::get($destination))->toBe('Hello World');
});

test('file->delete(...)', function () {
    $path = 'test.txt';

    Storage::put($path, 'Hello World');
    (new File)->delete($path);

    expect(Storage::exists($path))->toBeFalse();
});
