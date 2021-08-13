<?php

class OtherContentBlock extends ContentBlock implements ContentBlockInterface
{
    public function getContent(string $directoryPath, string $fileName, array $fileData, string $fileNameTranslated): string
    {
        return $this->getOriginalHtmlFileContent('content-blocks/other-content-block.html');
    }
}
