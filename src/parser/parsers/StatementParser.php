<?php

namespace JonasWindmann\EzScript\parser\parsers;

use JonasWindmann\EzScript\lexer\TokenType;
use JonasWindmann\EzScript\parser\parsers\ExpressionParser;

class StatementParser
{
    private array $tokens;
    private int $pos;
    private ?ExpressionParser $expressionParser = null;

    public function __construct(array &$tokens, int &$pos)
    {
        $this->tokens = &$tokens;
        $this->pos = &$pos;
    }

    public function setExpressionParser(ExpressionParser $expressionParser): void
    {
        $this->expressionParser = $expressionParser;
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

    public function parseStatement(): array
    {
        $tok = $this->peek();

        switch ($tok['type']) {
            case TokenType::VARIABLE:
                return $this->parseAssignmentOrCall();

            case TokenType::IDENTIFIER:
                return $this->parseFunctionCall();

            case TokenType::IF:
                return $this->parseIf();

            case TokenType::WHILE:
                return $this->parseWhile();

            case TokenType::FOR:
                return $this->parseFor();

            case TokenType::BREAK:
                return $this->parseBreak();

            case TokenType::CONTINUE:
                return $this->parseContinue();

            case TokenType::RETURN:
                return $this->parseReturn();

            default:
                throw new \Exception("Unknown statement at line {$tok['line']}: {$tok['type']}");
        }
    }

    public function parseBlockStatements(): array
    {
        $stmts = [];
        while ($this->peek() && $this->peek()['type'] !== TokenType::RBRACE) {
            $stmts[] = $this->parseStatement();
        }
        return $stmts;
    }

    private function parseAssignmentOrCall(bool $requireSemicolon = true): array
    {
        // Start with a variable node
        $expr = ['type' => 'Variable', 'name' => $this->advance()['value']];

        // Handle chains like $player.give_op().something_else()
        while ($this->peek() && $this->peek()['type'] === TokenType::DOT) {
            $this->advance(); // consume '.'
            $method = $this->expect(TokenType::IDENTIFIER);

            if ($this->peek()['type'] === TokenType::LPAREN) {
                // It's a method call
                $args = $this->parseArguments();
                $expr = [
                    'type'   => 'MethodCall',
                    'object' => $expr,
                    'method' => $method['value'],
                    'args'   => $args
                ];
            } else {
                // It's just property access
                $expr = [
                    'type'   => 'PropertyAccess',
                    'object' => $expr,
                    'property' => $method['value']
                ];
            }
        }

        // Increment/Decrement: $var++; or $var--;
        if (in_array($this->peek()['type'], [TokenType::INC, TokenType::DEC])) {
            $op = $this->advance();
            $this->expect(TokenType::SEMICOLON);
            return [
                'type' => 'PostfixExpr',
                'operator' => $op['type'] === TokenType::INC ? '++' : '--',
                'operand' => $expr
            ];
        }

        // Assignment: $var = expr;
        if ($this->peek()['type'] === TokenType::ASSIGN) {
            $this->advance();
            $value = $this->parseExpression();
            if ($requireSemicolon) {
                $this->expect(TokenType::SEMICOLON);
            }
            return [
                'type'   => 'Assign',
                'target' => $expr,
                'expr'   => $value
            ];
        }

        // Just a usage like $player;
        if ($this->peek()['type'] === TokenType::SEMICOLON) {
            $this->advance();
            return ['type' => 'VarUsage', 'expr' => $expr];
        }

        // Must be a statement, so expect semicolon at the end
        if ($this->peek()['type'] === TokenType::SEMICOLON) {
            $this->advance();
        }

        return $expr;
    }

    private function parseFunctionCall(): array
    {
        $callee = $this->advance(); // IDENTIFIER
        $args = $this->parseArguments();
        // allow optional semicolon at the end
        if ($this->peek() && $this->peek()['type'] === TokenType::SEMICOLON) {
            $this->advance();
        }
        return [
            'type' => 'Call',
            'callee' => $callee['value'],
            'args' => $args
        ];
    }

    private function parseArguments(): array
    {
        $this->expect(TokenType::LPAREN);
        $args = [];
        while ($this->peek()['type'] !== TokenType::RPAREN) {
            $args[] = $this->parseExpression();
            if ($this->peek()['type'] === TokenType::COMMA) {
                $this->advance();
            }
        }
        $this->expect(TokenType::RPAREN);
        return $args;
    }

    private function parseIf(): array
    {
        $this->advance(); // if
        $this->expect(TokenType::LPAREN);
        $cond = $this->parseExpression();
        $this->expect(TokenType::RPAREN);
        $this->expect(TokenType::LBRACE);
        $then = $this->parseBlockStatements();
        $this->expect(TokenType::RBRACE);

        $else = null;
        if ($this->peek()['type'] === TokenType::ELSE) {
            $this->advance();
            $this->expect(TokenType::LBRACE);
            $else = $this->parseBlockStatements();
            $this->expect(TokenType::RBRACE);
        }

        return [
            'type' => 'If',
            'condition' => $cond,
            'then' => $then,
            'else' => $else
        ];
    }

    private function parseWhile(): array
    {
        $this->advance(); // while
        $this->expect(TokenType::LPAREN);
        $cond = $this->parseExpression();
        $this->expect(TokenType::RPAREN);
        $this->expect(TokenType::LBRACE);
        $body = $this->parseBlockStatements();
        $this->expect(TokenType::RBRACE);

        return [
            'type' => 'While',
            'condition' => $cond,
            'body' => $body
        ];
    }

    private function parseFor(): array
    {
        $this->advance(); // for
        $this->expect(TokenType::LPAREN);

        // Initialization
        $init = null;
        if ($this->peek()['type'] !== TokenType::SEMICOLON) {
            if ($this->peek()['type'] === TokenType::VARIABLE) {
                $init = $this->parseAssignmentOrCall(false);
            } else {
                $init = $this->parseExpression();
                $this->expect(TokenType::SEMICOLON);
            }
        } else {
            $this->advance(); // Skip the semicolon
        }

        // Condition
        $cond = null;
        if ($this->peek()['type'] !== TokenType::SEMICOLON) {
            $cond = $this->parseExpression();
        }
        $this->expect(TokenType::SEMICOLON);

        // Update
        $update = null;
        if ($this->peek()['type'] !== TokenType::RPAREN) {
            $update = $this->parseExpression();
        }
        $this->expect(TokenType::RPAREN);

        // Body
        $this->expect(TokenType::LBRACE);
        $body = $this->parseBlockStatements();
        $this->expect(TokenType::RBRACE);

        return [
            'type' => 'For',
            'init' => $init,
            'condition' => $cond,
            'update' => $update,
            'body' => $body
        ];
    }

    private function parseBreak(): array
    {
        $this->advance(); // break
        $this->expect(TokenType::SEMICOLON);

        return [
            'type' => 'Break'
        ];
    }

    private function parseContinue(): array
    {
        $this->advance(); // continue
        $this->expect(TokenType::SEMICOLON);

        return [
            'type' => 'Continue'
        ];
    }

    private function parseReturn(): array
    {
        $this->advance(); // return

        $value = null;
        if ($this->peek()['type'] !== TokenType::SEMICOLON) {
            $value = $this->parseExpression();
        }

        $this->expect(TokenType::SEMICOLON);

        return [
            'type' => 'Return',
            'value' => $value
        ];
    }

    private function parseExpression(): array
    {
        if ($this->expressionParser === null) {
            throw new \Exception("ExpressionParser not set in StatementParser");
        }
        return $this->expressionParser->parseExpression();
    }
}
