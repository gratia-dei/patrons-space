<?php

class DateMyPatronsSimplifiedListContentBlock extends DateMyPatronsFullContentBlock implements ContentBlockInterface
{
    protected function setProperties(): void
    {
        $immovableFilePath = $this->getGeneratedFileSuffix(self::IMMOVABLE_FILE_PATH);
        $this->immovableFileData = $this->getOriginalJsonFileContentArray($immovableFilePath);

        $this->mainTemplate = $this->getOriginalHtmlFileContent('content-blocks/date-my-patrons-simplified-list-content-block.html');
    }
}
