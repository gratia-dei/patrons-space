<?php

class OtherContentBlock extends ContentBlock implements ContentBlockInterface
{
    public function getContent(): string
    {
        return $this->getOriginalHtmlFileContent('content-blocks/other-content-block.html');
    }
}
