<?php

declare(strict_types=1);

namespace App\Tools\PhpParser\Visitors;

use PhpParser\Node;

trait InteractsWithNodes
{
    /**
     * @param  array<Node>  $ast
     */
    protected function getStatements(array $ast)
    {
        if (count($ast) === 1 && $ast[0] instanceof Node\Stmt\Namespace_) {
            return $ast[0]->stmts;
        }

        return $ast;
    }

    protected function hasImports(array $ast): bool
    {
        return collect($this->getStatements($ast))->contains(fn ($value) => $value instanceof Node\Stmt\Use_);
    }

    protected function hasClass(array $ast): bool
    {
        return collect($this->getStatements($ast))->contains(fn ($value) => $value instanceof Node\Stmt\Class_);
    }

    protected function setStatements(array $ast, array $statements): array
    {
        if (count($ast) === 1 && $ast[0] instanceof Node\Stmt\Namespace_) {
            $ast[0]->stmts = $statements;
        }

        return $statements;
    }
}
