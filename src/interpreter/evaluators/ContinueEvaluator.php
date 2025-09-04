<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class ContinueEvaluator implements EvaluatorInterface
{
    /**
     * Evaluate a continue statement node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     * @throws \Exception with message 'CONTINUE' to be caught by loop evaluators
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        if ($node['type'] !== 'Continue') {
            throw new \Exception("Expected Continue node, got " . $node['type']);
        }
        
        throw new \Exception('CONTINUE');
    }
}