<?php

namespace JonasWindmann\EzScript\lexer\recognizers;

use JonasWindmann\EzScript\lexer\CharacterReader;
use JonasWindmann\EzScript\lexer\Token;
use JonasWindmann\EzScript\lexer\TokenType;

class KeywordRecognizer
{
    private array $keywords = [
        "start"      => TokenType::START,
        "stop"       => TokenType::STOP,
        "on"         => TokenType::ON,
        "command"    => TokenType::COMMAND,
        "task"       => TokenType::TASK,
        "function"   => TokenType::FUNCTION,
        "const"      => TokenType::CONST,
        "import"     => TokenType::IMPORT,
        "if"         => TokenType::IF,
        "else"       => TokenType::ELSE,
        "elseif"     => TokenType::ELSEIF,
        "while"      => TokenType::WHILE,
        "for"        => TokenType::FOR,
        "try"        => TokenType::TRY,
        "catch"      => TokenType::CATCH,
        "return"     => TokenType::RETURN,
        "break"      => TokenType::BREAK,
        "continue"   => TokenType::CONTINUE,
        "as"         => TokenType::AS,
        "alias"      => TokenType::ALIAS,
        "permission" => TokenType::PERMISSION,
        "usage"      => TokenType::USAGE,
    ];

    public function recognize(CharacterReader $reader): ?Token
    {
        $ch = $reader->peek();
        
        if (!ctype_alpha($ch) && $ch !== "_") {
            return null;
        }
        
        $id = "";
        while (($c = $reader->peek()) !== null && (ctype_alnum($c) || $c === "_")) {
            $id .= $reader->advance();
        }
        
        $idLower = strtolower($id);
        if (isset($this->keywords[$idLower])) {
            return new Token($this->keywords[$idLower], $id, $reader->getLine());
        } else {
            return new Token(TokenType::IDENTIFIER, $id, $reader->getLine());
        }
    }
}