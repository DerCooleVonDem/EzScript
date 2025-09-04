<?php

namespace JonasWindmann\EzScript\lexer;

class CharacterReader
{
    private array $chars;
    private int $pos = 0;
    private int $line = 1;

    public function __construct(string $content)
    {
        $this->chars = str_split($content);
    }

    public function peek(int $offset = 0): ?string
    {
        $i = $this->pos + $offset;
        return $this->chars[$i] ?? null;
    }

    public function advance(): ?string
    {
        $c = $this->chars[$this->pos] ?? null;
        $this->pos++;
        return $c;
    }

    public function getPosition(): int
    {
        return $this->pos;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function incrementLine(): void
    {
        $this->line++;
    }
}