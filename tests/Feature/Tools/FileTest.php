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

test('file->prependToMethod(...)', function () {
    $path = 'test.php';
    $content = 'echo "Hello, World!";';
    Storage::put($path, "<?php\n\nclass Test {\n    public function testMethod()\n    {\n        // Comment\n    }\n}");

    (new File)->prependToMethod($path, 'testMethod', $content);

    expect(Storage::get($path))->toBe("<?php\n\nclass Test {\n    public function testMethod()\n    {\n        {$content}\n        // Comment\n    }\n}");
});

test('file->addToJson(...)', function () {
    $path = 'test.json';
    $initialJson = ['existing' => 'value', 'other' => 'old', 'v' => ['a' => '!']];
    Storage::put($path, json_encode($initialJson));

    (new File)->addToJson($path, 'newKey', 'newValue');
    (new File)->addToJson($path, 'otherNewKey.subsection', 'internal');
    (new File)->addToJson($path, 'other', 'new');
    (new File)->addToJson($path, 'v.b', '?');

    $result = json_decode(Storage::get($path), true);
    expect($result)->toBe([
        'existing' => 'value',
        'other' => 'new',
        'v' => [
            'a' => '!',
            'b' => '?',
        ],
        'newKey' => 'newValue',
        'otherNewKey' => [
            'subsection' => 'internal',
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
        'existingKey' => 'newValue',
    ]);
});

test('file->replaceLines(...)', function () {
    $path = 'test.txt';
    $content = "Line 1\nLine 2\nLine 3";
    Storage::put($path, $content);

    (new File)->replaceLines($path, 'Line 2', "Replaced Line 1\nReplaced Line 2");

    expect(Storage::get($path))->toBe("Line 1\nReplaced Line 1\nReplaced Line 2\nLine 3");
});

test('file->replaceLines(...) honors indentation', function () {
    $path = 'test.txt';
    $content = "Line 1\n    Line 2\nLine 3";
    Storage::put($path, $content);

    (new File)->replaceLines($path, 'Line 2', "Replaced Line 1\nReplaced Line 2");

    expect(Storage::get($path))->toBe("Line 1\n    Replaced Line 1\n    Replaced Line 2\nLine 3");
});

test('file->replaceLines(...) with limit', function () {
    $path = 'test.txt';
    $content = "Line 1\nLine 2\nLine 3";
    Storage::put($path, $content);

    (new File)->replaceLines($path, 'Line', 'Replaced Line', 1);

    expect(Storage::get($path))->toBe("Replaced Line\nLine 2\nLine 3");
});

test('file->replaceLines(...) with non-existent line', function () {
    $path = 'test.txt';
    $content = "Line 1\nLine 2\nLine 3\n";
    Storage::put($path, $content);

    (new File)->replaceLines($path, 'Non-existent Line', 'Replacement');

    expect(Storage::get($path))->toBe($content);
});

test('file->stub(...)', function () {
    copy(base_path('tests/Fixtures/stub.txt'), base_path('stubs/testing.txt'));

    $destination = 'destination.txt';
    (new File)->stub('testing.txt', $destination);

    expect(Storage::exists($destination))->toBeTrue()
        ->and(trim(Storage::get($destination)))->toBe('Hello, World!');

    unlink(base_path('stubs/testing.txt'));
})->after(function () {
    // @todo: Figure out why this is not being called....
    //         ... Tony and Mateus discovered: the test name is normalized to run it, but not to check for after().
    //         ... so this requires a PR to fix Pest to work with test names containing strange characters.
    unlink(base_path('stubs/testing.txt'));
});

test('file->stub(...) throws exception for non-existent stub', function () {
    expect(fn () => (new File)->stub('non-existent', 'destination.txt'))
        ->toThrow(Exception::class);
});

test('file->addImport(...)', function () {
    $path = 'test.php';
    $initialContent = "<?php\n\nnamespace App\Awesome;\n\nclass Test\n{\n    // Some code\n}";
    Storage::put($path, $initialContent);

    (new File)->addImports($path, 'App\Models\User');

    expect(Storage::get($path))->toBe("<?php\n\nnamespace App\Awesome;\n\nuse App\Models\User;\n\nclass Test\n{\n    // Some code\n}");
});

test('file->addImport(...) with other imports', function () {
    $path = 'test.php';
    $initialContent = "<?php\n\nnamespace App\Awesome;\n\nuse App\Models\Contact;\n\nclass Test {\n    // Some code\n}";
    Storage::put($path, $initialContent);

    (new File)->addImports($path, 'App\Models\User');

    expect(Storage::get($path))->toBe("<?php\n\nnamespace App\Awesome;\n\nuse App\Models\Contact;\nuse App\Models\User;\n\nclass Test\n{\n    // Some code\n}");
});

test('file->addImport(...) skips duplicate imports', function () {
    $path = 'test.php';
    $initialContent = "<?php\n\nuse App\Models\User;\n\nclass Test\n{\n    // Some code\n}";
    Storage::put($path, $initialContent);

    (new File)->addImports($path, 'App\Models\User');

    expect(Storage::get($path))->toBe($initialContent);
});

test('file->addImport(...) throws exception when no class is found', function () {
    $path = 'test.php';
    $initialContent = "<?php\n\n// Just some code without a class";
    Storage::put($path, $initialContent);

    expect(fn () => (new File)->addImports($path, 'App\Models\User'))
        ->toThrow(Exception::class, "Class keyword not found in {$path}");
});

test('file->appendAfterLine(...)', function () {
    $path = 'test.txt';
    Storage::put($path, "Line 1\nLine 2\nLine 3");

    (new File)->appendAfterLine($path, 'Line 2', 'Appended content');

    expect(Storage::get($path))->toBe("Line 1\nLine 2\nAppended content\nLine 3");
});

test('file->appendAfterLine(...) with limit', function () {
    $path = 'test.txt';
    Storage::put($path, "Line 1\nLine 2\nAnother Line 2\nLine 3");

    (new File)->appendAfterLine($path, 'Line 2', 'Appended content', 1);

    expect(Storage::get($path))->toBe("Line 1\nLine 2\nAppended content\nAnother Line 2\nLine 3");
});

test('file->appendAfterLine(...) with multiple matches', function () {
    $path = 'test.txt';
    Storage::put($path, "Line 1\nLine 2\nLine 2\nLine 3");

    (new File)->appendAfterLine($path, 'Line 2', 'Appended content');

    expect(Storage::get($path))->toBe("Line 1\nLine 2\nAppended content\nLine 2\nAppended content\nLine 3");
});

test('file->appendAfterLine(...) with no matches', function () {
    $path = 'test.txt';
    $content = "Line 1\nLine 2\nLine 3";
    Storage::put($path, $content);

    (new File)->appendAfterLine($path, 'Non-existent', 'Appended content');

    expect(Storage::get($path))->toBe($content);
});

test('file->appendAfterLine(...) with empty file', function () {
    $path = 'test.txt';
    Storage::put($path, '');

    (new File)->appendAfterLine($path, 'Line', 'Appended content');

    expect(Storage::get($path))->toBe('');
});

test('file->appendAfterLine(...) honors indentation', function () {
    $path = 'test.txt';
    Storage::put($path, "Line 1\n    Line 2\n");

    (new File)->appendAfterLine($path, 'Line 2', 'Appended content');

    expect(Storage::get($path))->toBe("Line 1\n    Line 2\n    Appended content\n");
});
