<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class BreakEvaluator implements EvaluatorInterface
{
    /**
     * Evaluate a break statement node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     * @throws \Exception with message 'BREAK' to be caught by loop evaluators
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        if ($node['type'] !== 'Break') {
            throw new \Exception("Expected Break node, got " . $node['type']);
        }
        
        throw new \Exception('BREAK');
    }
}