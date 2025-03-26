<?php

use App\Tools\File;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
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

test('file->deleteLinesContaining(...)', function () {
    $path = 'test.txt';
    $content = "Line 1\nLine 2\nLine 3\nAnother Line 2\n";
    Storage::put($path, $content);

    (new File)->deleteLinesContaining($path, 'Line 2');

    expect(Storage::get($path))->toBe("Line 1\nLine 3\n");
});

test('file->deleteLinesContaining(...) with non-existent line', function () {
    $path = 'test.txt';
    $content = "Line 1\nLine 2\nLine 3\nAnother Line 2\n";
    Storage::put($path, $content);

    (new File)->deleteLinesContaining($path, 'Non-existent Line');

    expect(Storage::get($path))->toBe($content);
});

test('file->addToMethod(...)', function () {
    $path = 'test.php';
    $content = 'echo "Hello, World!";';
    Storage::put($path, "<?php\n\nclass Test {\n    public function testMethod()\n    {\n        // Comment\n    }\n}");

    (new File)->addToMethod($path, 'testMethod', $content);

    expect(Storage::get($path))->toBe("<?php\n\nclass Test {\n    public function testMethod()\n    {\n        // Comment\n        {$content}\n    }\n}");
});

test('file->addToJson(...)', function () {
    $path = 'test.json';
    $initialJson = ['existing' => 'value', 'other' => 'old'];
    Storage::put($path, json_encode($initialJson));

    (new File)->addToJson($path, 'newKey', 'newValue');
    (new File)->addToJson($path, 'otherNewKey.subsection', 'internal');
    (new File)->addToJson($path, 'other', 'new');

    $result = json_decode(Storage::get($path), true);
    expect($result)->toBe([
        'existing' => 'value',
        'other' => 'new',
        'newKey' => 'newValue',
        'otherNewKey' => [
            'subsection' => 'internal'
        ],
    ]);
});

test('file->addToJson(...) updates existing key', function () {
    $path = 'test.json';
    $initialJson = ['existingKey' => 'oldValue'];
    Storage::put($path, json_encode($initialJson));

    (new File)->addToJson($path, 'existingKey', 'newValue');

    $result = json_decode(Storage::get($path), true);
    expect($result)->toBe([
        'existingKey' => 'newValue'
    ]);
});
