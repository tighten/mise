<?php

declare(strict_types=1);

namespace Tests\Tools;

use App\Tools\FileFormatter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake();
});

describe('PHP File Formatting', function () {
    it('it sorts imports', function () {
        $format = new FileFormatter();

        Storage::put('UnOrganizedImports.php', File::get(base_path('tests/Fixtures/FileFormatter/PHP/UnOrganizedImports.php.fixture')));

        $format->file('UnOrganizedImports.php')
            ->importOrder()
            ->fix();

        expect(Storage::get('UnOrganizedImports.php'))->toBe(File::get(base_path('tests/Fixtures/FileFormatter/PHP/OrganisedImports.php.fixture')));
    });

    it('it fixes array syntax', function () {
        $format = new FileFormatter();

        Storage::put('ArrayFunctionSyntax.php', File::get('tests/Fixtures/FileFormatter/PHP/ArrayFunctionSyntax.php.fixture'));

        $format->file('ArrayFunctionSyntax.php')
            ->arraySyntax()
            ->fix();

        expect(Storage::get('ArrayFunctionSyntax.php'))->toBe(File::get(base_path('tests/Fixtures/FileFormatter/PHP/ArrayShortSyntax.php.fixture')));
    });

    it('fixes multiple issues', function () {
        $format = new FileFormatter();

        Storage::put('MultipleIssues.php', File::get('tests/Fixtures/FileFormatter/PHP/MultipleIssues.php.fixture'));

        $format->file('MultipleIssues.php')
            ->importOrder()
            ->arraySyntax()
            ->fix();

        expect(Storage::get('MultipleIssues.php'))->toBe(File::get(base_path('tests/Fixtures/FileFormatter/PHP/MultipleIssuesFixed.php.fixture')));
    });

    it('accepts php-cs-fixer rules', function () {
        $format = new FileFormatter();

        Storage::put('MultipleIssues.php', File::get('tests/Fixtures/FileFormatter/PHP/MultipleIssues.php.fixture'));

        $format->file('MultipleIssues.php')
               ->rules(['ordered_imports', 'array_syntax'])
               ->fix();

        expect(Storage::get('MultipleIssues.php'))->toBe(File::get(base_path('tests/Fixtures/FileFormatter/PHP/MultipleIssuesFixed.php.fixture')));
    });
});
