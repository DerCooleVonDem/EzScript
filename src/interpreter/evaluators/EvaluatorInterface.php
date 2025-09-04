<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

interface EvaluatorInterface
{
    /**
     * Evaluate a node in the AST
     * 
     * @param array $node The node to evaluate
     * @param Environment $env The environment to use for evaluation
     * @return mixed The result of the evaluation
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null);
}