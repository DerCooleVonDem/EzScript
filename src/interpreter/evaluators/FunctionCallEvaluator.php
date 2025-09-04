<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\functions\FunctionRegistry;
use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class FunctionCallEvaluator implements EvaluatorInterface
{
    private EvaluatorInterface $expressionEvaluator;
    private FunctionRegistry $functionRegistry;

    public function __construct(EvaluatorInterface $expressionEvaluator, FunctionRegistry $functionRegistry)
    {
        $this->expressionEvaluator = $expressionEvaluator;
        $this->functionRegistry = $functionRegistry;
    }

    /**
     * Evaluate a function call node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        if ($node['type'] !== 'Call') {
            throw new \Exception("Expected Call node, got " . $node['type']);
        }
        
        $callee = $node['callee'];
        $args = array_map(fn($arg) => $this->expressionEvaluator->evaluate($arg, $env), $node['args']);
        
        return $this->functionRegistry->call($callee, $args);
    }
}