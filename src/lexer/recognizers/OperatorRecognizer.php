<?php

namespace JonasWindmann\EzScript\lexer\recognizers;

use JonasWindmann\EzScript\lexer\CharacterReader;
use JonasWindmann\EzScript\lexer\Token;
use JonasWindmann\EzScript\lexer\TokenType;

class OperatorRecognizer
{
    public function recognize(CharacterReader $reader): ?Token
    {
        $ch = $reader->peek();

        switch ($ch) {
            // --- Assignment & Equality ---
            case "=":
                if ($reader->peek(1) === "=") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::EQ, "==", $reader->getLine());
                }
                $reader->advance();
                return new Token(TokenType::ASSIGN, "=", $reader->getLine());

            case "!":
                if ($reader->peek(1) === "=") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::NEQ, "!=", $reader->getLine());
                }
                $reader->advance();
                return new Token(TokenType::NOT, "!", $reader->getLine());

            // --- Relational ---
            case "<":
                if ($reader->peek(1) === "=") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::LE, "<=", $reader->getLine());
                }
                $reader->advance();
                return new Token(TokenType::LT, "<", $reader->getLine());

            case ">":
                if ($reader->peek(1) === "=") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::GE, ">=", $reader->getLine());
                }
                $reader->advance();
                return new Token(TokenType::GT, ">", $reader->getLine());

            // --- Arithmetic & compound assignment ---
            case "+":
                if ($reader->peek(1) === "+") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::INC, "++", $reader->getLine());
                }
                if ($reader->peek(1) === "=") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::PLUSEQ, "+=", $reader->getLine());
                }
                $reader->advance();
                return new Token(TokenType::PLUS, "+", $reader->getLine());

            case "-":
                if ($reader->peek(1) === "-") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::DEC, "--", $reader->getLine());
                }
                if ($reader->peek(1) === "=") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::MINUSEQ, "-=", $reader->getLine());
                }
                if ($reader->peek(1) === ">") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::ARROW, "->", $reader->getLine());
                }
                $reader->advance();
                return new Token(TokenType::MINUS, "-", $reader->getLine());

            case "*":
                if ($reader->peek(1) === "=") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::STAREQ, "*=", $reader->getLine());
                }
                $reader->advance();
                return new Token(TokenType::STAR, "*", $reader->getLine());

            case "/":
                if ($reader->peek(1) === "=") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::SLASHEQ, "/=", $reader->getLine());
                }
                $reader->advance();
                return new Token(TokenType::SLASH, "/", $reader->getLine());

            case "%":
                if ($reader->peek(1) === "=") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::MODEQ, "%=", $reader->getLine());
                }
                $reader->advance();
                return new Token(TokenType::MOD, "%", $reader->getLine());

            // --- Logical operators ---
            case "&":
                if ($reader->peek(1) === "&") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::AND, "&&", $reader->getLine());
                }
                return null;

            case "|":
                if ($reader->peek(1) === "|") {
                    $reader->advance(); $reader->advance();
                    return new Token(TokenType::OR, "||", $reader->getLine());
                }
                return null;

            // --- Misc ---
            case ".":
                $reader->advance();
                return new Token(TokenType::DOT, ".", $reader->getLine());

            case "{": $reader->advance(); return new Token(TokenType::LBRACE, "{", $reader->getLine());
            case "}": $reader->advance(); return new Token(TokenType::RBRACE, "}", $reader->getLine());
            case "(": $reader->advance(); return new Token(TokenType::LPAREN, "(", $reader->getLine());
            case ")": $reader->advance(); return new Token(TokenType::RPAREN, ")", $reader->getLine());
            case "[": $reader->advance(); return new Token(TokenType::LBRACKET, "[", $reader->getLine());
            case "]": $reader->advance(); return new Token(TokenType::RBRACKET, "]", $reader->getLine());
            case ",": $reader->advance(); return new Token(TokenType::COMMA, ",", $reader->getLine());
            case ";": $reader->advance(); return new Token(TokenType::SEMICOLON, ";", $reader->getLine());
            case ":": $reader->advance(); return new Token(TokenType::COLON, ":", $reader->getLine());
        }

        return null;
    }
}
