<?php

namespace JonasWindmann\EzScript\parser;

use JonasWindmann\EzScript\lexer\TokenType;
use JonasWindmann\EzScript\parser\parsers\DeclarationParser;
use JonasWindmann\EzScript\parser\parsers\ExpressionParser;
use JonasWindmann\EzScript\parser\parsers\LifecycleParser;
use JonasWindmann\EzScript\parser\parsers\StatementParser;

class Parser
{
    private array $tokens;
    private int $pos = 0;
    private StatementParser $statementParser;
    private ExpressionParser $expressionParser;
    private DeclarationParser $declarationParser;
    private LifecycleParser $lifecycleParser;

    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;

        // Create parser instances
        $this->statementParser = new StatementParser($this->tokens, $this->pos);
        $this->expressionParser = new ExpressionParser($this->tokens, $this->pos);
        $this->declarationParser = new DeclarationParser($this->tokens, $this->pos);
        $this->lifecycleParser = new LifecycleParser($this->tokens, $this->pos);

        // Set up dependencies
        $this->statementParser->setExpressionParser($this->expressionParser);
        $this->declarationParser->setExpressionParser($this->expressionParser);
        $this->declarationParser->setStatementParser($this->statementParser);
        $this->lifecycleParser->setStatementParser($this->statementParser);
    }

    private function peek(int $offset = 0): ?array
    {
        $i = $this->pos + $offset;
        return $this->tokens[$i] ?? null;
    }

    public function parse(): array
    {
        $nodes = [];
        while ($this->peek() && $this->peek()['type'] !== TokenType::EOF) {
            $nodes[] = $this->parseTopLevel();
        }
        return ['type' => 'Program', 'body' => $nodes];
    }

    private function parseTopLevel(): array
    {
        $tok = $this->peek();
        switch ($tok['type']) {
            case TokenType::START:
            case TokenType::STOP:
                return $this->lifecycleParser->parseLifecycleBlock();

            case TokenType::CONST:
                return $this->declarationParser->parseConst();

            case TokenType::FUNCTION:
                return $this->declarationParser->parseFunction();

            case TokenType::COMMAND:
                return $this->declarationParser->parseCommand();

            case TokenType::IMPORT:
                return $this->declarationParser->parseImport();

            default:
                throw new \Exception("Unexpected top-level token: " . $tok['type']);
        }
    }
}
