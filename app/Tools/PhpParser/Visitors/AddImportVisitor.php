<?php

namespace App\Tools\PhpParser\Visitors;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;

class AddImportVisitor extends NodeVisitorAbstract {
    /**
     * @var array|\class-string[]|string|string[]
     */
    private string|array $imports;

    public function leaveNode(Node $node): void
    {
        if ($node instanceof Node\Stmt\Use_) {
            foreach ($this->imports as $import) {
                $node->uses[] = new Node\UseItem(new Name($import));
            }
        }
    }

    /** @param class-string|array<class-string> $imports */
    public function __construct(string|array $imports) {
        $this->imports = is_array($imports) ? $imports : [$imports];
    }
}
