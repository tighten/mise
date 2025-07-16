<?php

namespace Tighten\Mise\Tools\PhpParser\Visitors;

use Exception;
use Illuminate\Support\Facades\Cache;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;
use Tighten\Mise\Tools\PhpParser;

class AddImportVisitor extends NodeVisitorAbstract
{
    use InteractsWithNodes;

    /** @var array<class-string> */
    private array $imports;

    /** @param class-string|array<class-string> $imports */
    public function __construct(string|array $imports)
    {
        $this->imports = is_array($imports) ? $imports : [$imports];
    }

    /**
     * Prep the document before traversing it;
     * add an empty import block if none exists.
     *
     * @param  array<Node>  $nodes
     *
     * @throws Exception
     */
    public function beforeTraverse(array $nodes): ?array
    {
        if (! $this->hasTypeKeyword($nodes)) {
            throw new Exception('Class keyword not found in ' . Cache::get(PhpParser::ACTIVE_FILENAME_KEY));
        }

        // If there are no imports, add an empty import block.
        // This is necessary because the `leaveNode` method won't even
        // be able to function as written if there are no imports.
        if (! $this->hasImports($nodes)) {
            $this->addEmptyImportBlock($nodes);
        }

        return $nodes;
    }

    public function addEmptyImportBlock(array $nodes): void
    {
        $statements = $this->getStatements($nodes);
        array_unshift($statements, new Use_([]));
        $this->setStatements($nodes, $statements);
    }

    /**
     * Parse a node after traversing it.
     * If it's the import block, add the imports.
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Use_) {
            return $this->addImports($node);
        }
    }

    /**
     * Given the import block, add the specified imports.
     */
    public function addImports(Use_ $node): int
    {
        $currentImports = collect($node->uses)
            ->map(fn ($value) => $value->name->name)
            ->toArray();

        foreach ($this->imports as $import) {
            if (in_array($import, $currentImports)) {
                continue;
            }

            $node->uses[] = new UseItem(new Name($import));
        }

        return NodeVisitor::STOP_TRAVERSAL;
    }
}
