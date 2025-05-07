<?php

namespace App\Tools\PhpParser\Visitors;

use App\Tools\PhpParser;
use Exception;
use Illuminate\Support\Facades\Cache;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use PhpParser\NodeVisitorAbstract;

class AddImportVisitor extends NodeVisitorAbstract
{
    use InteractsWithNodes;

    /** @var class-string|array<class-string> */
    private string|array $imports;

    /** @param class-string|array<class-string> $imports */
    public function __construct(string|array $imports)
    {
        $this->imports = is_array($imports) ? $imports : [$imports];
    }

    public function leaveNode(Node $node): void
    {
        if ($node instanceof Use_) {
            $currentImports = collect($node->uses)->map(fn ($value) => $value->name->name)->toArray();
            foreach ($this->imports as $import) {
                if (in_array($import, $currentImports)) {
                    continue;
                }
                $node->uses[] = new UseItem(new Name($import));
            }
        }
    }

    /**
     * @param  array<Node>  $nodes
     *
     * @throws Exception
     */
    public function beforeTraverse(array $nodes): ?array
    {
        if (! $this->hasClass($nodes)) {
            throw new Exception('Class keyword not found in ' . Cache::get(PhpParser::ACTIVE_FILENAME));
        }

        if (! $this->hasImports($nodes)) {
            $statements = $this->getStatements($nodes);
            array_unshift($statements, new Use_(collect($this->imports)->map(fn ($import) => new UseItem(new Name($import)))->toArray()));
            $this->setStatements($nodes, $statements);
        }

        return $nodes;
    }
}
