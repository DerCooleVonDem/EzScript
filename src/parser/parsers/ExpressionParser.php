<?php

namespace JonasWindmann\EzScript\parser\parsers;

use JonasWindmann\EzScript\lexer\TokenType;

class ExpressionParser
{
    private array $tokens;
    private int $pos;

    public function __construct(array &$tokens, int &$pos)
    {
        $this->tokens = &$tokens;
        $this->pos = &$pos;
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

    // --- Expressions ---
    public function parseExpression(): array
    {
        return $this->parseLogicalOr();
    }

    private function parseLogicalOr(): array
    {
        $expr = $this->parseLogicalAnd();
        while ($this->peek() && $this->peek()['type'] === TokenType::OR) {
            $op = $this->advance();
            $right = $this->parseLogicalAnd();
            $expr = ['type' => 'BinaryExpr', 'op' => $op['value'], 'left' => $expr, 'right' => $right];
        }
        return $expr;
    }

    private function parseLogicalAnd(): array
    {
        $expr = $this->parseEquality();
        while ($this->peek() && $this->peek()['type'] === TokenType::AND) {
            $op = $this->advance();
            $right = $this->parseEquality();
            $expr = ['type' => 'BinaryExpr', 'op' => $op['value'], 'left' => $expr, 'right' => $right];
        }
        return $expr;
    }

    private function parseEquality(): array
    {
        $expr = $this->parseComparison();
        while ($this->peek() && in_array($this->peek()['type'], [TokenType::EQ, TokenType::NEQ])) {
            $op = $this->advance();
            $right = $this->parseComparison();
            $expr = ['type' => 'BinaryExpr', 'op' => $op['value'], 'left' => $expr, 'right' => $right];
        }
        return $expr;
    }

    private function parseComparison(): array
    {
        $expr = $this->parseTerm();
        while ($this->peek() && in_array($this->peek()['type'], [TokenType::LT, TokenType::GT, TokenType::LE, TokenType::GE])) {
            $op = $this->advance();
            $right = $this->parseTerm();
            $expr = ['type' => 'BinaryExpr', 'op' => $op['value'], 'left' => $expr, 'right' => $right];
        }
        return $expr;
    }

    private function parseTerm(): array
    {
        $expr = $this->parseFactor();
        while ($this->peek() && in_array($this->peek()['type'], [TokenType::PLUS, TokenType::MINUS])) {
            $op = $this->advance();
            $right = $this->parseFactor();
            $expr = ['type' => 'BinaryExpr', 'op' => $op['value'], 'left' => $expr, 'right' => $right];
        }
        return $expr;
    }

    private function parseFactor(): array
    {
        $expr = $this->parsePrimary();
        while ($this->peek() && in_array($this->peek()['type'], [TokenType::STAR, TokenType::SLASH])) {
            $op = $this->advance();
            $right = $this->parsePrimary();
            $expr = ['type' => 'BinaryExpr', 'op' => $op['value'], 'left' => $expr, 'right' => $right];
        }
        return $expr;
    }

    private function parsePrimary(): array
    {
        $tok = $this->advance();
        $expr = null;

        switch ($tok['type']) {
            case TokenType::NUMBER:
                $expr = ['type' => 'NumberLiteral', 'value' => $tok['value']];
                break;
            case TokenType::STRING:
                $expr = ['type' => 'StringLiteral', 'value' => $tok['value']];
                break;
            case TokenType::VARIABLE:
                $expr = ['type' => 'Variable', 'name' => $tok['value']];
                break;
            case TokenType::LPAREN:
                $expr = $this->parseExpression();
                $this->expect(TokenType::RPAREN);
                break;
            default:
                throw new \Exception("Unexpected expression token: " . $tok['type']);
        }

        return $this->parsePostfix($expr);
    }

    private function parsePostfix(array $expr): array
    {
        if ($this->peek() && in_array($this->peek()['type'], [TokenType::INC, TokenType::DEC])) {
            $op = $this->advance();
            return [
                'type' => 'PostfixExpr',
                'operator' => $op['type'] === TokenType::INC ? '++' : '--',
                'operand' => $expr
            ];
        }

        return $expr;
    }
}
