<?php

namespace Tests\Tools;

use App\Tools\FileFormatter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake();
});

describe('PHP File Formatting', function () {
    it('accepts an array of php-cs-fixer rules', function () {
        $formatter = new FileFormatter;
        Storage::put('MultipleIssues.php', File::get('tests/Fixtures/FileFormatter/PHP/MultipleIssues.php.fixture'));

        $formatter->fix('MultipleIssues.php', ['ordered_imports', 'array_syntax']);

        expect(Storage::get('MultipleIssues.php'))->toBe(File::get(base_path('tests/Fixtures/FileFormatter/PHP/MultipleIssuesFixed.php.fixture')));
    });

    it('accepts a single php-cs-fixer rule as a string', function () {
        $format = new FileFormatter;
        Storage::put('UnOrganizedImports.php', File::get(base_path('tests/Fixtures/FileFormatter/PHP/UnOrganizedImports.php.fixture')));

        $format->fix('UnOrganizedImports.php', 'ordered_imports');

        expect(Storage::get('UnOrganizedImports.php'))->toBe(File::get(base_path('tests/Fixtures/FileFormatter/PHP/OrganisedImports.php.fixture')));
    });
});
