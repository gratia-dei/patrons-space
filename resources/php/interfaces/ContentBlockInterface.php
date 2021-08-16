<?php

interface ContentBlockInterface
{
    public function getContent(string $directoryPath, string $fileNameTranslated, array $fileData, array $generatedFileData): string;
}
