<?php

namespace JonasWindmann\EzScript\lexer\recognizers;

use JonasWindmann\EzScript\lexer\CharacterReader;
use JonasWindmann\EzScript\lexer\Token;
use JonasWindmann\EzScript\lexer\TokenType;

class IdentifierRecognizer
{
    public function recognizeVariable(CharacterReader $reader): ?Token
    {
        $ch = $reader->peek();
        
        if ($ch !== '$') {
            return null;
        }
        
        $reader->advance(); // consume $
        $id = "";
        $c = $reader->peek();
        if ($c === null || !(ctype_alpha($c) || $c === "_")) {
            // invalid var name, still emit VARIABLE with empty id
            return new Token(TokenType::VARIABLE, "", $reader->getLine());
        }
        
        while (($c = $reader->peek()) !== null && (ctype_alnum($c) || $c === "_")) {
            $id .= $reader->advance();
        }
        
        return new Token(TokenType::VARIABLE, $id, $reader->getLine());
    }
}