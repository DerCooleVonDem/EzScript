<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class VariableEvaluator implements EvaluatorInterface
{
    /**
     * Evaluate a variable node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        if ($node['type'] !== 'Variable') {
            throw new \Exception("Expected Variable node, got " . $node['type']);
        }
        
        $name = $node['name'];
        return $env->get($name);
    }
}