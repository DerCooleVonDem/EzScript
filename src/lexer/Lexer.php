<?php

namespace JonasWindmann\EzScript\lexer;

use JonasWindmann\EzScript\lexer\recognizers\CommentRecognizer;
use JonasWindmann\EzScript\lexer\recognizers\IdentifierRecognizer;
use JonasWindmann\EzScript\lexer\recognizers\KeywordRecognizer;
use JonasWindmann\EzScript\lexer\recognizers\LiteralRecognizer;
use JonasWindmann\EzScript\lexer\recognizers\OperatorRecognizer;

class Lexer
{
    private CharacterReader $reader;
    private array $tokens = [];
    private KeywordRecognizer $keywordRecognizer;
    private OperatorRecognizer $operatorRecognizer;
    private LiteralRecognizer $literalRecognizer;
    private IdentifierRecognizer $identifierRecognizer;
    private CommentRecognizer $commentRecognizer;

    public function __construct(string $content)
    {
        $this->reader = new CharacterReader($content);
        $this->keywordRecognizer = new KeywordRecognizer();
        $this->operatorRecognizer = new OperatorRecognizer();
        $this->literalRecognizer = new LiteralRecognizer();
        $this->identifierRecognizer = new IdentifierRecognizer();
        $this->commentRecognizer = new CommentRecognizer();
    }

    private function addToken(Token $token): void
    {
        $this->tokens[] = $token->toArray();
    }

    public function getTokens(): array|bool
    {
        while ($this->reader->peek() !== null) {
            $ch = $this->reader->peek();

            // whitespace (keep line numbers)
            if (ctype_space($ch)) {
                if ($ch === "\n") $this->reader->incrementLine();
                $this->reader->advance();
                continue;
            }

            // comments
            if ($this->commentRecognizer->recognizeSingleLineComment($this->reader) ||
                $this->commentRecognizer->recognizeBlockComment($this->reader)) {
                continue;
            }

            // string literal
            $stringToken = $this->literalRecognizer->recognizeString($this->reader);
            if ($stringToken !== null) {
                $this->addToken($stringToken);
                continue;
            }

            // number literal
            $numberToken = $this->literalRecognizer->recognizeNumber($this->reader);
            if ($numberToken !== null) {
                $this->addToken($numberToken);
                continue;
            }

            // variable
            $variableToken = $this->identifierRecognizer->recognizeVariable($this->reader);
            if ($variableToken !== null) {
                $this->addToken($variableToken);
                continue;
            }

            // keyword or identifier
            $keywordToken = $this->keywordRecognizer->recognize($this->reader);
            if ($keywordToken !== null) {
                $this->addToken($keywordToken);
                continue;
            }

            // operator or delimiter
            $operatorToken = $this->operatorRecognizer->recognize($this->reader);
            if ($operatorToken !== null) {
                $this->addToken($operatorToken);
                continue;
            }

            // unknown char
            $this->reader->advance();
            $this->addToken(new Token(TokenType::UNKNOWN, $ch, $this->reader->getLine()));
        }

        $this->addToken(new Token(TokenType::EOF, null, $this->reader->getLine()));
        return $this->tokens;
    }

    public function getError(): string
    {
        return "Lexer error at line {$this->reader->getLine()}, pos {$this->reader->getPosition()}";
    }
}
