<?php

namespace JonasWindmann\EzScript\interpreter\functions;

class LogFunction
{
    /**
     * Log a message to the console
     * 
     * @param mixed ...$args The arguments to log
     * @return null
     */
    public static function log(...$args): void
    {
        echo "[EzScript] " . implode(" ", $args) . PHP_EOL;
    }
}