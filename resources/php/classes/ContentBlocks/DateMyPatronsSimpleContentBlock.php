<?php

class DateMyPatronsSimpleContentBlock extends DateMyPatronsFullContentBlock implements ContentBlockInterface
{
    protected function setProperties(): void
    {
        $immovableFilePath = $this->getGeneratedFileSuffix(self::IMMOVABLE_FILE_PATH);
        $this->immovableFileData = $this->getOriginalJsonFileContentArray($immovableFilePath);

        $this->mainTemplate = $this->getOriginalHtmlFileContent('content-blocks/date-my-patrons-simple-content-block.html');
    }
}
