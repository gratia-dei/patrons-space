<?php

class Json
{
    public function decode(string $string): array
    {
        $result = json_decode($string, true);
        if ($result === null) {
            $result = [];
        }

        return $result;
    }
}
