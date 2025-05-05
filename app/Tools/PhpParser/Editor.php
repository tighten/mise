<?php

declare(strict_types=1);

namespace App\Tools\PhpParser;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as PhpFilePrinter;

class Editor
{
    const ACTIVE_FILENAME = self::class . '-filename';

    private Parser $parser;
    private PhpFilePrinter $phpFilePrinter;

    public function __construct()
    {
        $this->parser = (new ParserFactory)->createForNewestSupportedVersion();
        $this->phpFilePrinter = new PhpFilePrinter;
    }

    /**
     * @param  array<NodeVisitorAbstract>  $edits
     *
     * @throws Exception
     */
    public function edit(string $phpFile, array $edits): void
    {
        Cache::put(self::ACTIVE_FILENAME, $phpFile);

        $traverser = $this->configureTraverser($edits);
        $ast = $this->toAst($phpFile);
        $traverser->traverse($ast);
        Storage::put($phpFile, $this->toPhpFile($ast));
    }

    private function toAst(string $path): ?array
    {
        return $this->parser->parse(Storage::get($path));
    }

    private function toPhpFile(?array $ast): string
    {
        return $this->phpFilePrinter->prettyPrintFile($ast);
    }

    private function configureTraverser(array $edits): NodeTraverser
    {
        $traverser = new NodeTraverser;
        foreach ($edits as $edit) {
            $traverser->addVisitor($edit);
        }

        return $traverser;
    }
}
