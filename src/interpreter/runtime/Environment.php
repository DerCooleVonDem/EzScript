<?php

namespace JonasWindmann\EzScript\interpreter\runtime;

class Environment
{
    private array $values = [];

    /**
     * Define a variable in the environment
     * 
     * @param string $name The name of the variable
     * @param mixed $value The value of the variable
     * @return mixed The value
     */
    public function define(string $name, $value)
    {
        $this->values[$name] = $value;
        return $value;
    }

    /**
     * Get a variable from the environment
     * 
     * @param string $name The name of the variable
     * @return mixed The value of the variable
     * @throws \Exception If the variable is not defined
     */
    public function get(string $name)
    {
        if (!array_key_exists($name, $this->values)) {
            throw new \Exception("Undefined variable: $name");
        }
        return $this->values[$name];
    }

    /**
     * Check if a variable is defined in the environment
     * 
     * @param string $name The name of the variable
     * @return bool True if the variable is defined, false otherwise
     */
    public function isDefined(string $name): bool
    {
        return array_key_exists($name, $this->values);
    }
}