<?php

interface ContentBlockInterface
{
    public function getContent(string $directoryPath, string $fileBaseName, array $fileData, string $fileNameTranslated): string;
}
