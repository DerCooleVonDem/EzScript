<?php

namespace JonasWindmann\EzScript\lexer;

class Token
{
    private string $type;
    private $value;
    private int $line;

    public function __construct(string $type, $value, int $line)
    {
        $this->type = $type;
        $this->value = $value;
        $this->line = $line;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function toArray(): array
    {
        return [
            "type" => $this->type,
            "value" => $this->value,
            "line" => $this->line
        ];
    }
}