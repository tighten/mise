<?php

namespace App\Tools\PhpParser\Visitors;

use PhpParser\Node;

trait InteractsWithNodes
{
    /**
     * Return whether the given AST has an class/interface/trait declaration.
     *
     * @param  array<Node>  $ast
     */
    protected function hasTypeKeyword(array $ast): bool
    {
        return collect($this->getStatements($ast))
            ->contains(function ($value) {
                return $value instanceof Node\Stmt\Class_ || $value instanceof Node\Stmt\Interface_ || $value instanceof Node\Stmt\Trait_;
            });
    }

    /**
     * Return whether the given AST has one or more "use" (import) statements.
     *
     * @param  array<Node>  $ast
     */
    protected function hasImports(array $ast): bool
    {
        return collect($this->getStatements($ast))
            ->contains(fn ($value) => $value instanceof Node\Stmt\Use_);
    }

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

    protected function setStatements(array $ast, array $statements): array
    {
        if (count($ast) === 1 && $ast[0] instanceof Node\Stmt\Namespace_) {
            $ast[0]->stmts = $statements;
        }

        return $statements;
    }
}
