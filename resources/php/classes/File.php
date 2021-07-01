<?php

class File
{
    public function getFileContent($filePath): string
    {
        return file_get_contents($filePath);
    }
}
