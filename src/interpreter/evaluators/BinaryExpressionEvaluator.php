<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class BinaryExpressionEvaluator implements EvaluatorInterface
{
    private EvaluatorInterface $expressionEvaluator;

    public function __construct(EvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * Evaluate a binary expression node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        if ($node['type'] !== 'BinaryExpr') {
            throw new \Exception("Expected BinaryExpr node, got " . $node['type']);
        }
        
        $left = $this->expressionEvaluator->evaluate($node['left'], $env);
        $right = $this->expressionEvaluator->evaluate($node['right'], $env);
        return $this->applyBinaryOp($node['op'], $left, $right);
    }

    /**
     * Apply a binary operator to two values
     * 
     * @param string $op The operator
     * @param mixed $left The left operand
     * @param mixed $right The right operand
     * @return mixed The result of the operation
     */
    private function applyBinaryOp(string $op, $left, $right)
    {
        switch ($op) {
            case '+': return $left + $right;
            case '-': return $left - $right;
            case '*': return $left * $right;
            case '/': return $right != 0 ? $left / $right : throw new \Exception("Division by zero");
            case "==": return $left == $right;
            case "!=": return $left != $right;
            default: throw new \Exception("Unsupported operator: $op");
        }
    }
}