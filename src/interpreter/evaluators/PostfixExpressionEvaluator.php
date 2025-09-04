<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class PostfixExpressionEvaluator implements EvaluatorInterface
{
    private EvaluatorInterface $expressionEvaluator;

    public function __construct(EvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * Evaluate a postfix expression node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        if ($node['type'] !== 'PostfixExpr') {
            throw new \Exception("Expected PostfixExpr node, got " . $node['type']);
        }
        
        // Get the operand (should be a variable)
        $operand = $node['operand'];
        if ($operand['type'] !== 'Variable') {
            throw new \Exception("Postfix operators can only be applied to variables");
        }
        
        // Get the current value of the variable
        $varName = $operand['name'];
        $value = $env->get($varName);
        
        // Store the original value to return (postfix operators return the value before the operation)
        $originalValue = $value;
        
        // Apply the operation
        if ($node['operator'] === '++') {
            $value++;
        } else if ($node['operator'] === '--') {
            $value--;
        } else {
            throw new \Exception("Unknown postfix operator: " . $node['operator']);
        }
        
        // Update the variable in the environment
        $env->set($varName, $value);
        
        // Return the original value
        return $originalValue;
    }
}