<?php

namespace JonasWindmann\EzScript\lexer\recognizers;

use JonasWindmann\EzScript\lexer\CharacterReader;
use JonasWindmann\EzScript\lexer\Token;
use JonasWindmann\EzScript\lexer\TokenType;

class LiteralRecognizer
{
    public function recognizeString(CharacterReader $reader): ?Token
    {
        $ch = $reader->peek();
        
        if ($ch !== '"' && $ch !== "'") {
            return null;
        }
        
        $quote = $reader->advance();
        $value = "";
        while (($c = $reader->peek()) !== null && $c !== $quote) {
            if ($c === "\\") {
                $reader->advance(); // consume backslash
                $esc = $reader->peek();
                if ($esc === null) return null;
                switch ($esc) {
                    case "n":  $value .= "\n"; break;
                    case "t":  $value .= "\t"; break;
                    case "r":  $value .= "\r"; break;
                    case "\\": $value .= "\\"; break;
                    case '"':  $value .= '"'; break;
                    case "'":  $value .= "'"; break;
                    default:   $value .= $esc; break;
                }
                $reader->advance(); // consume escaped char
                continue;
            }
            if ($c === "\n") $reader->incrementLine();
            $value .= $reader->advance();
        }
        
        if ($reader->peek() !== $quote) {
            return null; // unterminated string
        }
        
        $reader->advance(); // closing quote
        return new Token(TokenType::STRING, $value, $reader->getLine());
    }
    
    public function recognizeNumber(CharacterReader $reader): ?Token
    {
        $ch = $reader->peek();
        
        if (!ctype_digit($ch)) {
            return null;
        }
        
        $num = "";
        $dotSeen = false;
        while (true) {
            $c = $reader->peek();
            if ($c === null) break;
            if ($c === ".") {
                if ($dotSeen) break;
                $dotSeen = true;
                $num .= $reader->advance();
                continue;
            }
            if (!ctype_digit($c)) break;
            $num .= $reader->advance();
        }
        
        return new Token(TokenType::NUMBER, $num, $reader->getLine());
    }
}