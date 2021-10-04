<?php

class DateOtherContentBlock extends ContentBlock implements ContentBlockInterface
{
    public function getContent(string $path, string $fileNameTranslated): string
    {
        return $this->getOriginalHtmlFileContent('content-blocks/date-other-content-block.html');
    }
}
