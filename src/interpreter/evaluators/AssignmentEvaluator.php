<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class AssignmentEvaluator implements EvaluatorInterface
{
    private EvaluatorInterface $expressionEvaluator;

    public function __construct(EvaluatorInterface $expressionEvaluator)
    {
        $this->expressionEvaluator = $expressionEvaluator;
    }

    /**
     * Evaluate an assignment node in the AST
     *
     * @param array $node
     * @param Environment $env
     * @param Interpreter|null $interpreter
     * @return mixed The result of the evaluation
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        if ($node['type'] !== 'Assign') {
            throw new \Exception("Expected Assign node, got " . $node['type']);
        }
        
        $name = $node['target']['name'];
        $value = $this->expressionEvaluator->evaluate($node['expr'], $env);
        return $env->define($name, $value);
    }
}