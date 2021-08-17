<?php

class GeneratorScript
{
    private const PROCEDURES = [
        'RemoveAllGeneratedFilesProcedure',
    ];

    public function __construct()
    {
    }

    public function run(): void
    {
        foreach (self::PROCEDURES as $class) {
            (new $class())->run();
        }
    }
}
