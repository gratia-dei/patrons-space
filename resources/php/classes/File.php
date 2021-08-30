<?php

class File
{
    public function exists(string $path): bool
    {
        return @file_exists($path);
    }

    public function getFileContent(string $filePath): string
    {
        return @file_get_contents($filePath);
    }

    public function setFileContent(string $filePath, string $content): int
    {
        return @file_put_contents($filePath, $content);
    }

    public function getList(string $path, string $pattern = '*'): array
    {
        return @glob(rtrim($path, '/') . '/' . $pattern);
    }

    public function isDirectory(string $path): bool
    {
        return @is_dir($path);
    }

    public function removeFile(string $filePath): bool
    {
        return @unlink($filePath);
    }
}
