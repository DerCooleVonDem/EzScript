<?php

namespace JonasWindmann\EzScript\interpreter;

use JonasWindmann\EzScript\interpreter\evaluators\AssignmentEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\BinaryExpressionEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\BlockEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\BreakEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\ContinueEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\Evaluator;
use JonasWindmann\EzScript\interpreter\evaluators\ForEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\FunctionCallEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\IfEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\LiteralEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\PostfixExpressionEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\ReturnEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\VariableEvaluator;
use JonasWindmann\EzScript\interpreter\evaluators\WhileEvaluator;
use JonasWindmann\EzScript\interpreter\functions\FunctionRegistry;
use JonasWindmann\EzScript\interpreter\functions\LogFunction;
use JonasWindmann\EzScript\interpreter\runtime\Environment;

class Interpreter
{

    private Environment $environment;
    private FunctionRegistry $functionRegistry;
    private Evaluator $evaluator;

    public array $nodesOnStop = [];

    public function __construct()
    {
        $this->environment = new Environment();
        $this->functionRegistry = new FunctionRegistry();

        // Register built-in functions
        $this->functionRegistry->register('log', [LogFunction::class, 'log']);

        // Create evaluators
        $literalEvaluator = new LiteralEvaluator();
        $variableEvaluator = new VariableEvaluator();

        // Create the main evaluator first with null dependencies
        $this->evaluator = new Evaluator(
            $literalEvaluator,
            $variableEvaluator,
            null, // Will be updated in updateEvaluatorDependencies
            null, // Will be updated in updateEvaluatorDependencies
            null, // Will be updated in updateEvaluatorDependencies
            null, // Will be updated in updateEvaluatorDependencies
            null, // Will be updated in updateEvaluatorDependencies
            null, // Will be updated in updateEvaluatorDependencies
            null, // Will be updated in updateEvaluatorDependencies
            null, // Will be updated in updateEvaluatorDependencies
            null, // Will be updated in updateEvaluatorDependencies
            null, // Will be updated in updateEvaluatorDependencies
            null  // Will be updated in updateEvaluatorDependencies
        );

        // Now update the dependencies that need the main evaluator
        $this->updateEvaluatorDependencies();
    }

    private function updateEvaluatorDependencies(): void
    {
        // Create all the evaluators that depend on the main evaluator
        $assignmentEvaluator = new AssignmentEvaluator($this->evaluator);
        $binaryExpressionEvaluator = new BinaryExpressionEvaluator($this->evaluator);
        $functionCallEvaluator = new FunctionCallEvaluator($this->evaluator, $this->functionRegistry);
        $blockEvaluator = new BlockEvaluator($this->evaluator);
        $ifEvaluator = new IfEvaluator($this->evaluator);
        $whileEvaluator = new WhileEvaluator($this->evaluator);
        $forEvaluator = new ForEvaluator($this->evaluator);
        $breakEvaluator = new BreakEvaluator();
        $continueEvaluator = new ContinueEvaluator();
        $returnEvaluator = new ReturnEvaluator($this->evaluator);
        $postfixExpressionEvaluator = new PostfixExpressionEvaluator($this->evaluator);

        // Set the evaluators on the main evaluator using reflection
        $reflectionClass = new \ReflectionClass($this->evaluator);

        $assignmentProperty = $reflectionClass->getProperty('assignmentEvaluator');
        $assignmentProperty->setAccessible(true);
        $assignmentProperty->setValue($this->evaluator, $assignmentEvaluator);

        $binaryProperty = $reflectionClass->getProperty('binaryExpressionEvaluator');
        $binaryProperty->setAccessible(true);
        $binaryProperty->setValue($this->evaluator, $binaryExpressionEvaluator);

        $functionCallProperty = $reflectionClass->getProperty('functionCallEvaluator');
        $functionCallProperty->setAccessible(true);
        $functionCallProperty->setValue($this->evaluator, $functionCallEvaluator);

        $blockProperty = $reflectionClass->getProperty('blockEvaluator');
        $blockProperty->setAccessible(true);
        $blockProperty->setValue($this->evaluator, $blockEvaluator);

        $ifProperty = $reflectionClass->getProperty('ifEvaluator');
        $ifProperty->setAccessible(true);
        $ifProperty->setValue($this->evaluator, $ifEvaluator);

        $whileProperty = $reflectionClass->getProperty('whileEvaluator');
        $whileProperty->setAccessible(true);
        $whileProperty->setValue($this->evaluator, $whileEvaluator);

        $forProperty = $reflectionClass->getProperty('forEvaluator');
        $forProperty->setAccessible(true);
        $forProperty->setValue($this->evaluator, $forEvaluator);

        $breakProperty = $reflectionClass->getProperty('breakEvaluator');
        $breakProperty->setAccessible(true);
        $breakProperty->setValue($this->evaluator, $breakEvaluator);

        $continueProperty = $reflectionClass->getProperty('continueEvaluator');
        $continueProperty->setAccessible(true);
        $continueProperty->setValue($this->evaluator, $continueEvaluator);

        $returnProperty = $reflectionClass->getProperty('returnEvaluator');
        $returnProperty->setAccessible(true);
        $returnProperty->setValue($this->evaluator, $returnEvaluator);

        $postfixExpressionProperty = $reflectionClass->getProperty('postfixExpressionEvaluator');
        $postfixExpressionProperty->setAccessible(true);
        $postfixExpressionProperty->setValue($this->evaluator, $postfixExpressionEvaluator);
    }

    public function run(array $program): void
    {
        if ($program['type'] !== 'Program') {
            throw new \Exception("Invalid AST root");
        }

        foreach ($program['body'] as $node) {
            $this->evaluator->evaluate($node, $this->environment, $this);
        }
    }

    public function stop(): void
    {
        foreach ($this->nodesOnStop as $node) {
            $this->evaluator->evaluate($node, $this->environment, $this);
        }
    }
}
