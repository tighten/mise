<?php

namespace App\Tools;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as PhpFilePrinter;

class PhpParser
{
    // Set a key to store the currently active file name; used
    // for debugging within visitors.
    const ACTIVE_FILENAME_KEY = 'hp-parser-actively-editing-file';

    protected Parser $parser;
    protected PhpFilePrinter $phpFilePrinter;

    public function __construct()
    {
        $this->parser = (new ParserFactory)->createForNewestSupportedVersion();
        $this->phpFilePrinter = new PhpFilePrinter;
    }

    /**
     * Central entry point for editing PHP files with PHP Parser.
     *
     * @param  string  $phpFilePath
     * @param  array<NodeVisitorAbstract>  $edits
     *
     * @throws Exception
     */
    public function edit(string $phpFilePath, array $edits): void
    {
        Cache::put(self::ACTIVE_FILENAME_KEY, $phpFilePath);

        $this->traverser($edits)
            ->traverse($ast = $this->toAst($phpFilePath));

        Storage::put($phpFilePath, $this->toPhpFile($ast));
    }

    /**
     * Create, and configure, a traverser that will apply the given edits to the AST.
     */
    protected function traverser(array $edits): NodeTraverser
    {
        $traverser = new NodeTraverser;

        foreach ($edits as $edit) {
            $traverser->addVisitor($edit);
        }

        return $traverser;
    }

    /**
     * Load a PHP file into an AST representation.
     */
    protected function toAst(string $path): ?array
    {
        return $this->parser->parse(Storage::get($path));
    }

    /**
     * Convert an AST representation back into a PHP file.
     */
    protected function toPhpFile(?array $ast): string
    {
        return $this->phpFilePrinter->prettyPrintFile($ast);
    }
}
