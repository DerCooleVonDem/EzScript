<?php

namespace JonasWindmann\EzScript\parser\parsers;

use JonasWindmann\EzScript\lexer\TokenType;
use JonasWindmann\EzScript\parser\parsers\StatementParser;

class LifecycleParser
{
    private array $tokens;
    private int $pos;
    private ?StatementParser $statementParser = null;

    public function __construct(array &$tokens, int &$pos)
    {
        $this->tokens = &$tokens;
        $this->pos = &$pos;
    }

    public function setStatementParser(StatementParser $statementParser): void
    {
        $this->statementParser = $statementParser;
    }

    private function peek(int $offset = 0): ?array
    {
        $i = $this->pos + $offset;
        return $this->tokens[$i] ?? null;
    }

    private function advance(): ?array
    {
        $t = $this->tokens[$this->pos] ?? null;
        $this->pos++;
        return $t;
    }

    private function expect(string $type): array
    {
        $token = $this->advance();
        if (!$token || $token['type'] !== $type) {
            throw new \Exception("Parser error: Expected {$type}, got " . ($token['type'] ?? 'EOF'));
        }
        return $token;
    }

    public function parseLifecycleBlock(): array
    {
        $keyword = $this->advance();
        $this->expect(TokenType::LBRACE);
        $body = $this->parseBlockStatements();
        $this->expect(TokenType::RBRACE);
        return [
            'type' => ucfirst(strtolower($keyword['type'])) . 'Block',
            'body' => $body
        ];
    }

    private function parseBlockStatements(): array
    {
        if ($this->statementParser === null) {
            throw new \Exception("StatementParser not set in LifecycleParser");
        }
        return $this->statementParser->parseBlockStatements();
    }
}
