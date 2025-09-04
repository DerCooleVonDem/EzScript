<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class IfEvaluator implements EvaluatorInterface
{
    private EvaluatorInterface $expressionEvaluator;

    public function __construct(EvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * Evaluate an if statement node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        if ($node['type'] !== 'If') {
            throw new \Exception("Expected If node, got " . $node['type']);
        }
        
        $condition = $this->expressionEvaluator->evaluate($node['condition'], $env);
        
        if ($condition) {
            // Execute the 'then' branch
            $result = null;
            foreach ($node['then'] as $stmt) {
                $result = $this->expressionEvaluator->evaluate($stmt, $env);
            }
            return $result;
        } else if ($node['else'] !== null) {
            // Execute the 'else' branch
            $result = null;
            foreach ($node['else'] as $stmt) {
                $result = $this->expressionEvaluator->evaluate($stmt, $env);
            }
            return $result;
        }
        
        return null;
    }
}