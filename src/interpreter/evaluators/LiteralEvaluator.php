<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class LiteralEvaluator implements EvaluatorInterface
{
    /**
     * Evaluate a literal node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        switch ($node['type']) {
            case 'NumberLiteral':
                return (int)$node['value'];
            case 'StringLiteral':
                return (string)$node['value'];
            default:
                throw new \Exception("Unknown literal type: " . $node['type']);
        }
    }
}