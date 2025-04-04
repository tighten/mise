<?php

declare(strict_types=1);

namespace App\Tools\PhpParser;

use Illuminate\Support\Facades\Storage;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as PhpFilePrinter;

class Editor
{
    /** @param array<NodeVisitorAbstract> $edits */
    public function edit(string $path, array $edits): void
    {
        $parser = (new ParserFactory)->createForNewestSupportedVersion();
        // Parse the file into an AST.
        $stmts = $parser->parse(Storage::get($path));

        // Traverse the AST applying the edits.
        $traverser = new NodeTraverser;
        foreach ($edits as $edit) {
            $traverser->addVisitor($edit);
        }
        $traverser->traverse($stmts);

        // Convert the edited AST back into the php file format and save it.
        Storage::put($path, (new PhpFilePrinter)->prettyPrintFile($stmts));
    }
}
