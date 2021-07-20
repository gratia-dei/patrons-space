<?php

class File
{
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function getFileContent(string $filePath): string
    {
        return @file_get_contents($filePath);
    }
}
