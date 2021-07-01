<?php

class Json
{
    public function decode(string $string): array
    {
        return json_decode($string, true);
    }
}
