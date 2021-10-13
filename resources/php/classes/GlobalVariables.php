<?php

class GlobalVariables
{
    private static ?self $instance = null;
    private $variables = [];

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function get(string $name)
    {
        return $this->variables[$name] ?? null;
    }

    public function set(string $name, $value): self
    {
        $this->variables[$name] = $value;

        return $this;
    }
}
