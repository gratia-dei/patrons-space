<?php

class Json
{
    public function decode(string $string): array
    {
        $result = @json_decode($string, true);
        if ($result === null) {
            $result = [];
        }

        return $result;
    }

    public function encode($content): string
    {
        return @json_encode($content, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}
