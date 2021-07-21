<?php

class RomanMartyrology2004DayElogiesContentBlock extends ContentBlock implements ContentBlockInterface
{
    public function getContent(string $directoryPath, string $fileName, array $fileData): string
    {
        return $this->getOriginalHtmlFileContent('content-blocks/other-content-block.html');
    }
}
