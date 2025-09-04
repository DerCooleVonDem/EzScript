<?php

namespace JonasWindmann\EzScript\interpreter\evaluators;

use JonasWindmann\EzScript\interpreter\Interpreter;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class Evaluator implements EvaluatorInterface
{
    private LiteralEvaluator $literalEvaluator;
    private VariableEvaluator $variableEvaluator;
    private ?AssignmentEvaluator $assignmentEvaluator;
    private ?BinaryExpressionEvaluator $binaryExpressionEvaluator;
    private ?FunctionCallEvaluator $functionCallEvaluator;
    private ?BlockEvaluator $blockEvaluator;
    private ?IfEvaluator $ifEvaluator;
    private ?WhileEvaluator $whileEvaluator;
    private ?ForEvaluator $forEvaluator;
    private ?BreakEvaluator $breakEvaluator;
    private ?ContinueEvaluator $continueEvaluator;
    private ?ReturnEvaluator $returnEvaluator;
    private ?PostfixExpressionEvaluator $postfixExpressionEvaluator;

    public function __construct(
        LiteralEvaluator $literalEvaluator,
        VariableEvaluator $variableEvaluator,
        ?AssignmentEvaluator $assignmentEvaluator = null,
        ?BinaryExpressionEvaluator $binaryExpressionEvaluator = null,
        ?FunctionCallEvaluator $functionCallEvaluator = null,
        ?BlockEvaluator $blockEvaluator = null,
        ?IfEvaluator $ifEvaluator = null,
        ?WhileEvaluator $whileEvaluator = null,
        ?ForEvaluator $forEvaluator = null,
        ?BreakEvaluator $breakEvaluator = null,
        ?ContinueEvaluator $continueEvaluator = null,
        ?ReturnEvaluator $returnEvaluator = null,
        ?PostfixExpressionEvaluator $postfixExpressionEvaluator = null
    ) {
        $this->literalEvaluator = $literalEvaluator;
        $this->variableEvaluator = $variableEvaluator;
        $this->assignmentEvaluator = $assignmentEvaluator;
        $this->binaryExpressionEvaluator = $binaryExpressionEvaluator;
        $this->functionCallEvaluator = $functionCallEvaluator;
        $this->blockEvaluator = $blockEvaluator;
        $this->ifEvaluator = $ifEvaluator;
        $this->whileEvaluator = $whileEvaluator;
        $this->forEvaluator = $forEvaluator;
        $this->breakEvaluator = $breakEvaluator;
        $this->continueEvaluator = $continueEvaluator;
        $this->returnEvaluator = $returnEvaluator;
        $this->postfixExpressionEvaluator = $postfixExpressionEvaluator;
    }

    /**
     * Evaluate a node in the AST
     * 
     * @param array $node The node to evaluate
     * @param Environment $env The environment to use for evaluation
     * @return mixed The result of the evaluation
     */
    public function evaluate(array $node, Environment $env, Interpreter $interpreter = null)
    {
        switch ($node['type']) {
            case 'NumberLiteral':
            case 'StringLiteral':
                return $this->literalEvaluator->evaluate($node, $env);

            case 'Variable':
                return $this->variableEvaluator->evaluate($node, $env);

            case 'Assign':
                if ($this->assignmentEvaluator === null) {
                    throw new \Exception("AssignmentEvaluator not initialized");
                }
                return $this->assignmentEvaluator->evaluate($node, $env);

            case 'BinaryExpr':
                if ($this->binaryExpressionEvaluator === null) {
                    throw new \Exception("BinaryExpressionEvaluator not initialized");
                }
                return $this->binaryExpressionEvaluator->evaluate($node, $env);

            case 'Call':
                if ($this->functionCallEvaluator === null) {
                    throw new \Exception("FunctionCallEvaluator not initialized");
                }
                return $this->functionCallEvaluator->evaluate($node, $env);

            case 'StartBlock':
                if ($this->blockEvaluator === null) {
                    throw new \Exception("BlockEvaluator not initialized");
                }
                return $this->blockEvaluator->evaluate($node, $env);

            case 'If':
                if ($this->ifEvaluator === null) {
                    throw new \Exception("IfEvaluator not initialized");
                }
                return $this->ifEvaluator->evaluate($node, $env);

            case 'While':
                if ($this->whileEvaluator === null) {
                    throw new \Exception("WhileEvaluator not initialized");
                }
                return $this->whileEvaluator->evaluate($node, $env);

            case 'For':
                if ($this->forEvaluator === null) {
                    throw new \Exception("ForEvaluator not initialized");
                }
                return $this->forEvaluator->evaluate($node, $env);

            case 'Break':
                if ($this->breakEvaluator === null) {
                    throw new \Exception("BreakEvaluator not initialized");
                }
                return $this->breakEvaluator->evaluate($node, $env);

            case 'Continue':
                if ($this->continueEvaluator === null) {
                    throw new \Exception("ContinueEvaluator not initialized");
                }
                return $this->continueEvaluator->evaluate($node, $env);

            case 'Return':
                if ($this->returnEvaluator === null) {
                    throw new \Exception("ReturnEvaluator not initialized");
                }
                return $this->returnEvaluator->evaluate($node, $env);

            case 'PostfixExpr':
                if ($this->postfixExpressionEvaluator === null) {
                    throw new \Exception("PostfixExpressionEvaluator not initialized");
                }
                return $this->postfixExpressionEvaluator->evaluate($node, $env);

            case 'StopBlock':
                if($interpreter !== null) {
                    foreach ($node['body'] as $stmt) {
                        $interpreter->nodesOnStop[] = $stmt;
                    }
                }
                return null;

            default:
                throw new \Exception("Unknown node type: " . $node['type']);
        }
    }
}
