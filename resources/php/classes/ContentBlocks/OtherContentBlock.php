<?php

class OtherContentBlock extends ContentBlock implements ContentBlockInterface
{
    public function getContent(string $path, string $fileNameTranslated): string
    {
        return $this->getOriginalHtmlFileContent('content-blocks/other-content-block.html');
    }
}
