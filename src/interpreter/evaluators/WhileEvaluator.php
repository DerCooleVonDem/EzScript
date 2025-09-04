<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class WhileEvaluator implements EvaluatorInterface
{
    private EvaluatorInterface $expressionEvaluator;

    public function __construct(EvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * Evaluate a while loop node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        if ($node['type'] !== 'While') {
            throw new \Exception("Expected While node, got " . $node['type']);
        }
        
        $result = null;
        
        try {
            while ($this->expressionEvaluator->evaluate($node['condition'], $env)) {
                try {
                    foreach ($node['body'] as $stmt) {
                        $result = $this->expressionEvaluator->evaluate($stmt, $env);
                    }
                } catch (\Exception $e) {
                    if ($e->getMessage() === 'BREAK') {
                        break;
                    } else if ($e->getMessage() === 'CONTINUE') {
                        continue;
                    } else {
                        throw $e;
                    }
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