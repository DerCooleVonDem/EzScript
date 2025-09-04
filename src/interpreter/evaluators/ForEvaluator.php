<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class ForEvaluator implements EvaluatorInterface
{
    private EvaluatorInterface $expressionEvaluator;

    public function __construct(EvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * Evaluate a for loop node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        if ($node['type'] !== 'For') {
            throw new \Exception("Expected For node, got " . $node['type']);
        }
        
        $result = null;
        
        try {
            // Initialize
            if ($node['init'] !== null) {
                $this->expressionEvaluator->evaluate($node['init'], $env);
            }
            
            // Loop
            while (true) {
                // Check condition
                if ($node['condition'] !== null) {
                    $condition = $this->expressionEvaluator->evaluate($node['condition'], $env);
                    if (!$condition) {
                        break;
                    }
                }
                
                // Execute body
                try {
                    foreach ($node['body'] as $stmt) {
                        $result = $this->expressionEvaluator->evaluate($stmt, $env);
                    }
                } catch (\Exception $e) {
                    if ($e->getMessage() === 'BREAK') {
                        break;
                    } else if ($e->getMessage() === 'CONTINUE') {
                        // Skip to update
                    } else {
                        throw $e;
                    }
                }
                
                // Update
                if ($node['update'] !== null) {
                    $this->expressionEvaluator->evaluate($node['update'], $env);
                }
            }
        } catch (\Exception $e) {
            if ($e->getMessage() === 'RETURN') {
                throw $e; // Re-throw return to be caught by function evaluator
            } else {
                throw $e;
            }
        }
        
        return $result;
    }
}