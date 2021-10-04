<?php

class DateMyTopPatronsContentBlock extends ContentBlock implements ContentBlockInterface
{
    public function getContent(string $path, string $fileNameTranslated): string
    {
        //...

        return $this->getOriginalHtmlFileContent('content-blocks/date-my-top-patrons-content-block.html');
    }
}
