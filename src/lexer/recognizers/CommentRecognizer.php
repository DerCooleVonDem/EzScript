<?php

namespace JonasWindmann\EzScript\lexer\recognizers;

use JonasWindmann\EzScript\lexer\CharacterReader;
use JonasWindmann\EzScript\lexer\Token;
use JonasWindmann\EzScript\lexer\TokenType;

class CommentRecognizer
{
    public function recognizeSingleLineComment(CharacterReader $reader): bool
    {
        $ch = $reader->peek();
        
        if ($ch !== '#') {
            return false;
        }
        
        while ($reader->peek() !== null && $reader->peek() !== "\n") {
            $reader->advance();
        }
        
        return true;
    }
    
    public function recognizeBlockComment(CharacterReader $reader): bool
    {
        $ch = $reader->peek();
        
        if ($ch !== '/' || $reader->peek(1) !== '*') {
            return false;
        }
        
        $reader->advance(); // /
        $reader->advance(); // *
        
        while (!($reader->peek() === "*" && $reader->peek(1) === "/")) {
            if ($reader->peek() === null) {
                return false; // unterminated comment
            }
            
            if ($reader->peek() === "\n") {
                $reader->incrementLine();
            }
            
            $reader->advance();
        }
        
        $reader->advance(); // *
        $reader->advance(); // /
        
        return true;
    }
}