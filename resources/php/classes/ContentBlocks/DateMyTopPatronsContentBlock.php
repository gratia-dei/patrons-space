<?php

class DateMyTopPatronsContentBlock extends ContentBlock implements ContentBlockInterface
{
    public function getContent(string $directoryPath, string $fileNameTranslated, array $fileData, array $generatedFileData): string
    {
        //...

        return $this->getOriginalHtmlFileContent('content-blocks/date-my-top-patrons-content-block.html');
    }
}
