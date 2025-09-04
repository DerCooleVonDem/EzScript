<?php

namespace JonasWindmann\EzScript\parser\parsers;

use JonasWindmann\EzScript\lexer\TokenType;
use JonasWindmann\EzScript\parser\parsers\ExpressionParser;
use JonasWindmann\EzScript\parser\parsers\StatementParser;

class DeclarationParser
{
    private array $tokens;
    private int $pos;
    private ?ExpressionParser $expressionParser = null;
    private ?StatementParser $statementParser = null;

    public function __construct(array &$tokens, int &$pos)
    {
        $this->tokens = &$tokens;
        $this->pos = &$pos;
    }

    public function setExpressionParser(ExpressionParser $expressionParser): void
    {
        $this->expressionParser = $expressionParser;
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

    public function parseConst(): array
    {
        $this->advance(); // const
        $var = $this->expect(TokenType::VARIABLE);
        $this->expect(TokenType::ASSIGN);
        $value = $this->parseExpression();
        $this->expect(TokenType::SEMICOLON);
        return ['type' => 'ConstDecl', 'name' => $var['value'], 'value' => $value];
    }

    public function parseFunction(): array
    {
        $this->advance(); // function
        $name = $this->expect(TokenType::IDENTIFIER);
        $this->expect(TokenType::LPAREN);
        $params = [];
        while ($this->peek()['type'] !== TokenType::RPAREN) {
            $param = $this->expect(TokenType::VARIABLE);
            $params[] = $param['value'];
            if ($this->peek()['type'] === TokenType::COMMA) {
                $this->advance();
            }
        }
        $this->expect(TokenType::RPAREN);
        $this->expect(TokenType::LBRACE);
        $body = $this->parseBlockStatements();
        $this->expect(TokenType::RBRACE);
        return [
            'type' => 'FunctionDecl',
            'name' => $name['value'],
            'params' => $params,
            'body' => $body
        ];
    }

    public function parseCommand(): array
    {
        $this->advance(); // command
        $name = $this->expect(TokenType::STRING);
        $this->expect(TokenType::IDENTIFIER); // "default" or "op"
        $this->expect(TokenType::VARIABLE);   // $player
        $this->expect(TokenType::VARIABLE);   // $args
        $this->expect(TokenType::LBRACE);
        $body = $this->parseBlockStatements();
        $this->expect(TokenType::RBRACE);
        return [
            'type' => 'CommandDecl',
            'name' => $name['value'],
            'body' => $body
        ];
    }

    public function parseImport(): array
    {
        $this->advance(); // consume 'import'
        $path = $this->expect(TokenType::STRING); // file path
        $this->expect(TokenType::SEMICOLON); // require semicolon after import
        return [
            'type' => 'Import',
            'path' => $path['value']
        ];
    }

    private function parseExpression(): array
    {
        if ($this->expressionParser === null) {
            throw new \Exception("ExpressionParser not set in DeclarationParser");
        }
        return $this->expressionParser->parseExpression();
    }

    private function parseBlockStatements(): array
    {
        if ($this->statementParser === null) {
            throw new \Exception("StatementParser not set in DeclarationParser");
        }
        return $this->statementParser->parseBlockStatements();
    }
}
