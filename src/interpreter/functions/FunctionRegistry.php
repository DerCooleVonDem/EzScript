<?php

namespace JonasWindmann\EzScript\interpreter\functions;

class FunctionRegistry
{
    private array $functions = [];

    /**
     * Register a function
     * 
     * @param string $name The name of the function
     * @param callable $function The function implementation
     */
    public function register(string $name, callable $function): void
    {
        $this->functions[$name] = $function;
    }

    /**
     * Call a function
     * 
     * @param string $name The name of the function
     * @param array $args The arguments to pass to the function
     * @return mixed The result of the function call
     * @throws \Exception If the function is not registered
     */
    public function call(string $name, array $args)
    {
        if (!array_key_exists($name, $this->functions)) {
            throw new \Exception("Unknown function: $name");
        }
        
        return ($this->functions[$name])(...$args);
    }

    /**
     * Check if a function is registered
     * 
     * @param string $name The name of the function
     * @return bool True if the function is registered, false otherwise
     */
    public function isRegistered(string $name): bool
    {
        return array_key_exists($name, $this->functions);
    }
}