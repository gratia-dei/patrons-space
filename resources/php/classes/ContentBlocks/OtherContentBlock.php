<?php

class OtherContentBlock extends ContentBlock implements ContentBlockInterface
{
    public function getContent(string $directoryPath, string $fileNameTranslated, array $fileData, array $generatedFileData): string
    {
        return $this->getOriginalHtmlFileContent('content-blocks/other-content-block.html');
    }
}
