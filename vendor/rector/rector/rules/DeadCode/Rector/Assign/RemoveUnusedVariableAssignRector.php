<?php

declare (strict_types=1);
namespace Rector\DeadCode\Rector\Assign;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\AssignRef;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Include_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use Rector\DeadCode\SideEffect\SideEffectNodeDetector;
use Rector\NodeAnalyzer\VariableAnalyzer;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Php\ReservedKeywordAnalyzer;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\Rector\AbstractScopeAwareRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Rector\Tests\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector\RemoveUnusedVariableAssignRectorTest
 */
final class RemoveUnusedVariableAssignRector extends AbstractScopeAwareRector
{
    /**
     * @readonly
     * @var \Rector\Php\ReservedKeywordAnalyzer
     */
    private $reservedKeywordAnalyzer;
    /**
     * @readonly
     * @var \Rector\DeadCode\SideEffect\SideEffectNodeDetector
     */
    private $sideEffectNodeDetector;
    /**
     * @readonly
     * @var \Rector\NodeAnalyzer\VariableAnalyzer
     */
    private $variableAnalyzer;
    /**
     * @readonly
     * @var \Rector\PhpParser\Node\BetterNodeFinder
     */
    private $betterNodeFinder;
    public function __construct(ReservedKeywordAnalyzer $reservedKeywordAnalyzer, SideEffectNodeDetector $sideEffectNodeDetector, VariableAnalyzer $variableAnalyzer, BetterNodeFinder $betterNodeFinder)
    {
        $this->reservedKeywordAnalyzer = $reservedKeywordAnalyzer;
        $this->sideEffectNodeDetector = $sideEffectNodeDetector;
        $this->variableAnalyzer = $variableAnalyzer;
        $this->betterNodeFinder = $betterNodeFinder;
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition('Remove unused assigns to variables', [new CodeSample(<<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        $value = 5;
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
    }
}
CODE_SAMPLE
)]);
    }
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes() : array
    {
        return [ClassMethod::class, Function_::class];
    }
    /**
     * @param ClassMethod|Function_ $node
     * @return null|\PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Function_
     */
    public function refactorWithScope(Node $node, Scope $scope)
    {
        $stmts = $node->stmts;
        if ($stmts === null || $stmts === []) {
            return null;
        }
        // we cannot be sure here
        if ($this->containsCompactFuncCall($node)) {
            return null;
        }
        if ($this->containsFileIncludes($node)) {
            return null;
        }
        $assignedVariableNamesByStmtPosition = $this->resolvedAssignedVariablesByStmtPosition($stmts);
        $hasChanged = \false;
        foreach ($assignedVariableNamesByStmtPosition as $stmtPosition => $variableName) {
            if ($this->isVariableUsedInFollowingStmts($node, $stmtPosition, $variableName)) {
                continue;
            }
            /** @var Expression<Assign> $currentStmt */
            $currentStmt = $stmts[$stmtPosition];
            /** @var Assign $assign */
            $assign = $currentStmt->expr;
            if ($this->hasCallLikeInAssignExpr($assign, $scope)) {
                // clean safely
                $cleanAssignedExpr = $this->cleanCastedExpr($assign->expr);
                $newExpression = new Expression($cleanAssignedExpr);
                $this->mirrorComments($newExpression, $currentStmt);
                $node->stmts[$stmtPosition] = $newExpression;
            } else {
                unset($node->stmts[$stmtPosition]);
            }
            $hasChanged = \true;
        }
        if ($hasChanged) {
            return $node;
        }
        return null;
    }
    private function cleanCastedExpr(Expr $expr) : Expr
    {
        if (!$expr instanceof Cast) {
            return $expr;
        }
        return $this->cleanCastedExpr($expr->expr);
    }
    private function hasCallLikeInAssignExpr(Expr $expr, Scope $scope) : bool
    {
        return (bool) $this->betterNodeFinder->findFirst($expr, function (Node $subNode) use($scope) : bool {
            return $this->sideEffectNodeDetector->detectCallExpr($subNode, $scope);
        });
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Function_ $functionLike
     */
    private function isVariableUsedInFollowingStmts($functionLike, int $assignStmtPosition, string $variableName) : bool
    {
        if ($functionLike->stmts === null) {
            return \false;
        }
        foreach ($functionLike->stmts as $key => $stmt) {
            // do not look yet
            if ($key <= $assignStmtPosition) {
                continue;
            }
            $stmtScope = $stmt->getAttribute(AttributeKey::SCOPE);
            if (!$stmtScope instanceof Scope) {
                continue;
            }
            $foundVariable = $this->betterNodeFinder->findVariableOfName($stmt, $variableName);
            if ($foundVariable instanceof Variable) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Function_ $functionLike
     */
    private function containsCompactFuncCall($functionLike) : bool
    {
        $compactFuncCall = $this->betterNodeFinder->findFirst($functionLike, function (Node $node) : bool {
            if (!$node instanceof FuncCall) {
                return \false;
            }
            return $this->isName($node, 'compact');
        });
        return $compactFuncCall instanceof FuncCall;
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Function_ $functionLike
     */
    private function containsFileIncludes($functionLike) : bool
    {
        return (bool) $this->betterNodeFinder->findInstancesOf($functionLike, [Include_::class]);
    }
    /**
     * @param array<int, Stmt> $stmts
     * @return array<int, string>
     */
    private function resolvedAssignedVariablesByStmtPosition(array $stmts) : array
    {
        $assignedVariableNamesByStmtPosition = [];
        $refVariableNames = [];
        foreach ($stmts as $key => $stmt) {
            if (!$stmt instanceof Expression) {
                continue;
            }
            if ($stmt->expr instanceof AssignRef && $stmt->expr->var instanceof Variable) {
                $refVariableNames[] = (string) $this->getName($stmt->expr->var);
            }
            if (!$stmt->expr instanceof Assign) {
                continue;
            }
            $assign = $stmt->expr;
            if (!$assign->var instanceof Variable) {
                continue;
            }
            $variableName = $this->getName($assign->var);
            if (!\is_string($variableName)) {
                continue;
            }
            if ($this->reservedKeywordAnalyzer->isNativeVariable($variableName)) {
                continue;
            }
            if ($this->shouldSkipVariable($assign->var, $variableName, $refVariableNames)) {
                continue;
            }
            $assignedVariableNamesByStmtPosition[$key] = $variableName;
        }
        return $assignedVariableNamesByStmtPosition;
    }
    /**
     * @param string[] $refVariableNames
     */
    private function shouldSkipVariable(Variable $variable, string $variableName, array $refVariableNames) : bool
    {
        if ($this->variableAnalyzer->isStaticOrGlobal($variable)) {
            return \true;
        }
        if ($this->variableAnalyzer->isUsedByReference($variable)) {
            return \true;
        }
        return \in_array($variableName, $refVariableNames, \true);
    }
}
