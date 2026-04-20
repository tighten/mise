<?php

namespace Tighten\Mise\Tools\PhpParser\Visitors;

use Exception;
use Illuminate\Support\Facades\Cache;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\NodeVisitorAbstract;
use Tighten\Mise\Tools\PhpParser;

class AddTraitVisitor extends NodeVisitorAbstract
{
    use InteractsWithNodes;

    /** @var array<string> */
    private array $traits;

    /** @param string|array<string> $traits */
    public function __construct(string|array $traits)
    {
        $this->traits = is_array($traits) ? $traits : [$traits];
    }

    /**
     * Validate the document before traversing it.
     *
     * @param  array<Node>  $nodes
     *
     * @throws Exception
     */
    public function beforeTraverse(array $nodes): ?array
    {
        if (! $this->hasClass($nodes)) {
            throw new Exception('Class not found in ' . Cache::get(PhpParser::ACTIVE_FILENAME_KEY));
        }

        return $nodes;
    }

    /**
     * Parse a node after traversing it.
     * If it's a class, add the traits.
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Class_) {
            $this->addTraitsToClass($node);
        }
    }

    /**
     * Add traits to a class node.
     */
    private function addTraitsToClass(Class_ $class): void
    {
        $existingTraitUseInfo = $this->findExistingTraitUse($class);
        $existingTraits = $this->extractExistingTraitNames($existingTraitUseInfo);
        $traitsToAdd = $this->determineTraitsToAdd($existingTraits);

        if (empty($traitsToAdd)) {
            return;
        }

        if ($existingTraitUseInfo['statement'] !== null) {
            $this->updateExistingTraitUse($class, $existingTraitUseInfo, $traitsToAdd);
        } else {
            $this->createNewTraitUse($class, $traitsToAdd);
        }
    }

    /**
     * Find the existing trait use statement in a class.
     *
     * @return array{statement: ?TraitUse, index: ?int}
     */
    private function findExistingTraitUse(Class_ $class): array
    {
        foreach ($class->stmts as $index => $stmt) {
            if ($stmt instanceof TraitUse) {
                return [
                    'statement' => $stmt,
                    'index' => $index,
                ];
            }
        }

        return [
            'statement' => null,
            'index' => null,
        ];
    }

    /**
     * Extract the simple names of existing traits.
     *
     * @param  array{statement: ?TraitUse, index: ?int}  $traitUseInfo
     * @return array<string>
     */
    private function extractExistingTraitNames(array $traitUseInfo): array
    {
        if ($traitUseInfo['statement'] === null) {
            return [];
        }

        $existingTraits = [];
        foreach ($traitUseInfo['statement']->traits as $trait) {
            $existingTraits[] = $this->getSimpleName($this->getTraitName($trait));
        }

        return $existingTraits;
    }

    /**
     * Determine which traits need to be added.
     *
     * @param  array<string>  $existingTraits
     * @return array<string>
     */
    private function determineTraitsToAdd(array $existingTraits): array
    {
        $traitsToAdd = [];

        foreach ($this->traits as $trait) {
            $traitSimpleName = $this->getSimpleName($trait);
            if (! in_array($traitSimpleName, $existingTraits)) {
                $traitsToAdd[] = $trait;
            }
        }

        return $traitsToAdd;
    }

    /**
     * Update an existing trait use statement with new traits.
     *
     * @param  array{statement: TraitUse, index: int}  $existingTraitUseInfo
     * @param  array<string>  $traitsToAdd
     */
    private function updateExistingTraitUse(Class_ $class, array $existingTraitUseInfo, array $traitsToAdd): void
    {
        // Combine existing traits with new ones
        $allTraits = $existingTraitUseInfo['statement']->traits;
        foreach ($traitsToAdd as $trait) {
            $allTraits[] = new Name($trait);
        }

        // Create new TraitUse statement, preserving attributes (including comments) from the original
        $newTraitUse = new TraitUse($allTraits);
        $newTraitUse->setAttributes($existingTraitUseInfo['statement']->getAttributes());

        // Replace the existing trait use statement
        $class->stmts[$existingTraitUseInfo['index']] = $newTraitUse;
    }

    /**
     * Create a new trait use statement and add it to the class.
     *
     * @param  array<string>  $traitsToAdd
     */
    private function createNewTraitUse(Class_ $class, array $traitsToAdd): void
    {
        $traitNodes = array_map(fn ($trait) => new Name($trait), $traitsToAdd);
        $newTraitUse = new TraitUse($traitNodes);

        // Insert at the beginning of the class body
        array_unshift($class->stmts, $newTraitUse);
    }

    /**
     * Check if the AST has a class declaration.
     *
     * @param  array<Node>  $ast
     */
    private function hasClass(array $ast): bool
    {
        return collect($this->getStatements($ast))
            ->contains(fn ($value) => $value instanceof Class_);
    }

    /**
     * Get the trait name from a Name node.
     */
    private function getTraitName(Name $name): string
    {
        return $name->toString();
    }

    /**
     * Get the simple name (without namespace) from a fully qualified trait name.
     */
    private function getSimpleName(string $trait): string
    {
        $parts = explode('\\', $trait);

        return end($parts);
    }
}
