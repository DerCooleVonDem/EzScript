<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class ReturnEvaluator implements EvaluatorInterface
{
    private EvaluatorInterface $expressionEvaluator;

    public function __construct(EvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * Evaluate a return statement node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     * @throws \Exception with message 'RETURN' to be caught by function evaluators
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        if ($node['type'] !== 'Return') {
            throw new \Exception("Expected Return node, got " . $node['type']);
        }
        
        $value = null;
        if ($node['value'] !== null) {
            $value = $this->expressionEvaluator->evaluate($node['value'], $env);
        }
        
        $exception = new \Exception('RETURN');
        $exception->returnValue = $value;
        throw $exception;
    }
}